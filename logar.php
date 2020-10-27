<?php
    require 'database/db.php';

    if ($_POST) {
        $username = $_POST['username'];
        $pass = $_POST['password'];

        if ($username == "" || $username == null) {
            echo "<script language='javascript' type='text/javascript'>
                    alert('O campo Usuário deve ser preenchido');
                    window.location.href='login.php';</script>";
        }

        if ($pass == "" || $pass == null) {
            echo "<script language='javascript' type='text/javascript'>
                    alert('O campo Senha deve ser preenchido');
                    window.location.href='login.php';</script>";
        }

        $db = Db::getInstance();
        $connection = $db->getConnection();

        $pass = sha1($pass);
        $sql_search_user = "SELECT * FROM tb_user WHERE username = '$username' AND password = '$pass'";
        $user = $connection->query($sql_search_user)->fetch();

        if ($user) {
            session_destroy();
            session_start();
            $_SESSION['usuario_logado'] = $user;

            header('Location: admin/index.php');
            $success = true;
        } else {
            echo "<script language='javascript' type='text/javascript'>
                    alert('Usuário ou senha inválido');
                    window.location.href='login.php';
                    </script>";
        }
    }
?>