<?php
    require 'database/db.php';
    // All relevant changes can be made in the data file. Please read the docs: https://github.com/flokX/devShort/wiki

    $success = false;
    $config_path = implode(DIRECTORY_SEPARATOR, array(__DIR__, "admin", "config.json"));
    $config_content = json_decode(file_get_contents($config_path), true);

    if ($config_content["installer"]["password"]) {

        $db = Db::getInstance();
        try {
            $db->createUserTb();
            $db->createUrlTb();
            $db->createStatsTb();
        } catch (Exception $e) {
            var_dump($e);
            exit;
        }
        $connection = $db->getConnection();

        // Create root .htaccess with the rewrite rules
        $installation_path = rtrim($_SERVER["REQUEST_URI"], "installer.php");
        $root_htaccess = "
            # The entrys below were set by the installer.
            
            # Rewrite rule to get the short URLs
            RewriteEngine On
            RewriteBase $installation_path
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule ^(.*)$ {$installation_path}redirect.php?short=$1 [L]";

        file_put_contents(__DIR__ . DIRECTORY_SEPARATOR . ".htaccess", $root_htaccess, FILE_APPEND);

        // Create the .htpasswd for the secure directory. If already a hashed password is there, copy it.
        $admin_password = sha1($config_content["installer"]["password"]);
        $admin_user = $config_content["installer"]["username"];

        $sql_search_user = "SELECT username FROM tb_user WHERE username = '$admin_user'";
        $user = $connection->query($sql_search_user)->fetch();
        if(!$user){
            $sql_insert_user = "INSERT INTO tb_user (id_user, username, password)
                VALUES (null, '$admin_user', '$admin_password')";
            $connection->exec($sql_insert_user);
        }

        // Change password entry to the hash and remove installer file.
        $config_content["installer"] = '';
        file_put_contents($config_path, json_encode($config_content, JSON_PRETTY_PRINT));

        //rename file installer
        rename('installer.php', 'ok_installed.php');
        header('Location: index.php');
        $success = true;

    }

?>

<!doctype html>
<html lang="en" class="h-100">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex, nofollow">
    <meta name="author" content="The devShort team">
    <link rel="icon" href="assets/icon.png">
    <title>Installer | devShort</title>
    <link href="assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="assets/main.css" rel="stylesheet">
</head>

<body class="d-flex flex-column h-100">

<main role="main" class="flex-shrink-0">
    <div class="container">
        <nav class="mt-3" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">devShort</li>
                <li class="breadcrumb-item active" aria-current="page">Installer</li>
            </ol>
        </nav>
        <?php

            if ($success) {
                echo "";
            } else {
                echo "<h1 class=\"mt-5\">Error while installing.</h1>
<p class=\"lead\">Please configure the <i>config.json</i> as shown in the <a href=\"https://github.com/flokX/devShort/wiki/Installation#installation\">devShort wiki</a> and try again.</p>
<p>We assume that you have not yet set an admin password.</p>";
            }
        ?>
    </div>
</main>

<footer class="footer">
    <div class="container-fluid">
        <nav class="float-left">
            <ul>
                <li>
                    <a href="https://www.ramonveloso.dev.br" target="_blank">
                        Autor do projeto
                    </a>
                </li>
            </ul>
        </nav>
        <div class="copyright float-right font-12">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>, layout made with <i class="material-icons">favorite</i> by
            <a href="https://www.creative-tim.com" target="_blank">Creative Tim</a> for a better web.
        </div>
    </div>
</footer>

</body>

</html>
