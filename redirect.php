<?php
    require 'database/db.php';
    // All relevant changes can be made in the data file. Please read the docs: https://github.com/flokX/devShort/wiki
    $base_path = implode(DIRECTORY_SEPARATOR, array(__DIR__, "admin"));
    $config_content = json_decode(file_get_contents($base_path . DIRECTORY_SEPARATOR . "config.json"), true);

    $db = Db::getInstance();
    $connection = $db->getConnection();

    $short = htmlspecialchars($_GET['short']);

    $return_404 = array("favicon.ico", "assets/vendor/bootstrap/bootstrap.min.css.map", "assets/vendor/frappe-charts/frappe-charts.min.iife.js.map");

    // If the robots.txt is requested, return it
    if ($short === "robots.txt") {
        header("Content-Type: text/plain; charset=utf-8");
        echo "User-agent: *\n";
        echo "Disallow: /\n";
        exit;
    } else if (in_array($short, $return_404)) {
        header("HTTP/1.1 404 Not Found");
        exit;
    }

    // Counts the access to the given $name
    function count_access($base_path, $name)
    {
        $db = Db::getInstance();
        $connection = $db->getConnection();

        $filename = $base_path . DIRECTORY_SEPARATOR . "stats.json";
        $stats = json_decode('', true);
        file_put_contents($filename, json_encode($stats, JSON_PRETTY_PRINT));
        $time_save = mktime(1, 1, 1, date('m'), date('d')+1, date('Y'));

        $sql_search_url = "SELECT name FROM tb_url WHERE name = '$name'";
        $stmt = $connection->query($sql_search_url);
        $result_url = $stmt->fetch();

        if ($result_url) {
            $sql_search_stats = "SELECT name FROM tb_stats WHERE name = '$name' AND timestamp = '$time_save'";
            $stmt = $connection->query($sql_search_stats);
            $result_stats = $stmt->fetch();

            if ($result_stats) {
                $sql_update_stats = "
                    UPDATE tb_stats 
                    SET timestamp = '$time_save', value = value+1 
                    WHERE name = '$name'";
                $connection->exec($sql_update_stats);
            } else {
                $sql_insert_stats = "INSERT INTO tb_stats (id_stats, name, timestamp, value)
                VALUES (null, '$name', '$time_save', '1')";
                $connection->exec($sql_insert_stats);
            }

            $sql_search_all_stats = "SELECT * FROM tb_stats";
            $stmt = $connection->query($sql_search_all_stats);
            $result_all_stats = $stmt->fetchAll();
            if ($result_all_stats) {
                foreach ($result_all_stats as $all_stat) {
                    $stats[$all_stat['name']][$all_stat['timestamp']] = $all_stat['value'];
                }
                file_put_contents($filename, json_encode($stats, JSON_PRETTY_PRINT));
            }
        }

    }

    $base_path = implode(DIRECTORY_SEPARATOR, array(__DIR__, "admin"));

    $sql_search_url = "SELECT * FROM tb_url WHERE name = '$short'";
    $stmt = $connection->query($sql_search_url);
    $url_exist = $stmt->fetch();

    if ($url_exist) {
        if (filter_var($url_exist['url'], FILTER_VALIDATE_URL) === FALSE) {
            header("HTTP/1.1 404 Not Found");
        } else {
            count_access($base_path, $short);
            header("Location: " . $url_exist['url'], $http_response_code = 303);
            exit;
        }
    } else if ($short === "") {
        count_access($base_path, "Index");
        header("Location: index.php", $http_response_code = 301);
        exit;
    } else {
        count_access($base_path, "404-request");
        header("HTTP/1.1 404 Not Found");
    }
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <link rel="icon" href="<?php echo $config_content["settings"]["favicon"]; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>
        <?php echo $config_content["settings"]["name"]; ?>
    </title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport'/>
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <link href="/assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet"/>
</head>

<body class="">
<div class="container d-flex h-100">
    <div class="row align-self-center w-100">
        <div class="col-md-12 mx-auto">
            <br/>
            <br/>
            <div class="card-title">
                <h4 class="card-title"><?php echo $config_content["settings"]["name"]; ?></h4>
                <div class="alert alert-danger">
                    <h3>404 | Url não encontrada</h3>
                    <p class="lead">O link curto "<?php echo $short; ?>" não foi encontrado no nosso
                        servidor. Pode ter sido excluído, expirou ou foi digitado incorretamente.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="container-fluid">
        <div class="copyright text-center">
            &copy;
            <script>
                document.write(new Date().getFullYear())
            </script>
            , SESCOOP <i class="material-icons">favorite</i> Somos Cooperativismo
        </div>
    </div>
</footer>
<!--   Core JS Files   -->
<script src="../assets/js/core/jquery.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap-material-design.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<script src="../assets/js/plugins/moment.min.js"></script>
<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<script src="../assets/js/material-dashboard.js?v=2.1.2" type="text/javascript"></script>

<script src="../assets/vendor/frappe-charts/frappe-charts.min.iife.js"></script>
<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        $().ready(function () {
            $sidebar = $('.sidebar');

            $sidebar_img_container = $sidebar.find('.sidebar-background');

            $full_page = $('.full-page');

            $sidebar_responsive = $('body > .navbar-collapse');

            window_width = $(window).width();

            fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

            if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
                if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
                    $('.fixed-plugin .dropdown').addClass('open');
                }

            }

            $('.fixed-plugin a').click(function (event) {
                // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
                if ($(this).hasClass('switch-trigger')) {
                    if (event.stopPropagation) {
                        event.stopPropagation();
                    } else if (window.event) {
                        window.event.cancelBubble = true;
                    }
                }
            });

            $('.fixed-plugin .active-color span').click(function () {
                $full_page_background = $('.full-page-background');

                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                var new_color = $(this).data('color');

                if ($sidebar.length != 0) {
                    $sidebar.attr('data-color', new_color);
                }

                if ($full_page.length != 0) {
                    $full_page.attr('filter-color', new_color);
                }

                if ($sidebar_responsive.length != 0) {
                    $sidebar_responsive.attr('data-color', new_color);
                }
            });

            $('.fixed-plugin .background-color .badge').click(function () {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');

                var new_color = $(this).data('background-color');

                if ($sidebar.length != 0) {
                    $sidebar.attr('data-background-color', new_color);
                }
            });

            $('.fixed-plugin .img-holder').click(function () {
                $full_page_background = $('.full-page-background');

                $(this).parent('li').siblings().removeClass('active');
                $(this).parent('li').addClass('active');


                var new_image = $(this).find("img").attr('src');

                if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
                    $sidebar_img_container.fadeOut('fast', function () {
                        $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
                        $sidebar_img_container.fadeIn('fast');
                    });
                }

                if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
                    var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

                    $full_page_background.fadeOut('fast', function () {
                        $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
                        $full_page_background.fadeIn('fast');
                    });
                }

                if ($('.switch-sidebar-image input:checked').length == 0) {
                    var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
                    var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

                    $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
                    $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
                }

                if ($sidebar_responsive.length != 0) {
                    $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
                }
            });

            $('.switch-sidebar-image input').change(function () {
                $full_page_background = $('.full-page-background');

                $input = $(this);

                if ($input.is(':checked')) {
                    if ($sidebar_img_container.length != 0) {
                        $sidebar_img_container.fadeIn('fast');
                        $sidebar.attr('data-image', '#');
                    }

                    if ($full_page_background.length != 0) {
                        $full_page_background.fadeIn('fast');
                        $full_page.attr('data-image', '#');
                    }

                    background_image = true;
                } else {
                    if ($sidebar_img_container.length != 0) {
                        $sidebar.removeAttr('data-image');
                        $sidebar_img_container.fadeOut('fast');
                    }

                    if ($full_page_background.length != 0) {
                        $full_page.removeAttr('data-image', '#');
                        $full_page_background.fadeOut('fast');
                    }

                    background_image = false;
                }
            });

            $('.switch-sidebar-mini input').change(function () {
                $body = $('body');

                $input = $(this);

                if (md.misc.sidebar_mini_active == true) {
                    $('body').removeClass('sidebar-mini');
                    md.misc.sidebar_mini_active = false;

                    // $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar();

                } else {

                    // $('.sidebar .sidebar-wrapper, .main-panel').perfectScrollbar('destroy');

                    setTimeout(function () {
                        $('body').addClass('sidebar-mini');

                        md.misc.sidebar_mini_active = true;
                    }, 300);
                }

                // we simulate the window Resize so the charts will get updated in realtime.
                var simulateWindowResize = setInterval(function () {
                    window.dispatchEvent(new Event('resize'));
                }, 180);

                // we stop the simulation of Window Resize after the animations are completed
                setTimeout(function () {
                    clearInterval(simulateWindowResize);
                }, 1000);

            });
        });
    });
</script>
</body>

</html>
