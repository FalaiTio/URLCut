<?php
    require '../database/db.php';
    session_start();
    include '../verifica_sessao.php';

    $config_path = __DIR__ . DIRECTORY_SEPARATOR . "config.json";
    $config_content = json_decode(file_get_contents($config_path), true);

    $db = Db::getInstance();
    $connection = $db->getConnection();

    $sql_search_url = "SELECT * FROM tb_url";
    $urls = $connection->query($sql_search_url)->fetchAll();

    if (isset($_GET['excluir'])) {
        $id = $_GET['excluir'];

        $sql_search_url = "SELECT name FROM tb_url WHERE id_url = '$id'";
        $url = $connection->query($sql_search_url)->fetch();

        if ($url) {
            $name = $url['name'];

            $sql_excluir_url = "DELETE FROM tb_url WHERE id_url = '$id'";
            $connection->exec($sql_excluir_url);

            $sql_excluir_stats = "DELETE FROM tb_stats WHERE url = '$name'";
            $connection->exec($sql_excluir_url);

            echo "<script language='javascript' type='text/javascript'>
                    alert('Link excluído com sucesso.');
                    window.location.href='./links.php';
                    </script>";
        }

    }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8"/>
    <link rel="icon" href="../<?php echo $config_content["settings"]["favicon"]; ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <title>
        <?php echo $config_content["settings"]["name"]; ?>
    </title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport'/>
    <link rel="stylesheet" type="text/css"
          href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons"/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
    <link href="/assets/css/material-dashboard.css?v=2.1.2" rel="stylesheet"/>
    <link href="/assets/main.css" rel="stylesheet"/>
</head>

<body class="">
<div class="wrapper ">
    <div class="sidebar" data-color="orange" data-background-color="white" data-image="/assets/img/sidebar-1.jpg">
        <div class="logo">
            <a href="./index.php" class="simple-text logo-normal">
                <img src="../assets/img/logo.png">
            </a>
        </div>
        <div class="sidebar-wrapper">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link" href="../index.php">
                        <i class="material-icons">dashboard</i>
                        <p>Home</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">
                        <i class="material-icons">poll</i>
                        <p>Painel</p>
                    </a>
                </li>
                <li class="nav-item active">
                    <a class="nav-link" href="./links.php">
                        <i class="material-icons">article</i>
                        <p>Links Existentes</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./user.php">
                        <i class="material-icons">person</i>
                        <p>Usuários</p>
                    </a>
                </li>
            </ul>
        </div>
    </div>
    <div class="main-panel">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
            <div class="container-fluid">
                <div class="navbar-wrapper">
                    <a class="navbar-brand" href="javascript:;">Gerenciar Links</a>
                </div>
                <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index"
                        aria-expanded="false" aria-label="Toggle navigation">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                    <span class="navbar-toggler-icon icon-bar"></span>
                </button>

                <div class="collapse navbar-collapse justify-content-end">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown"
                               aria-haspopup="true" aria-expanded="false">
                                <i class="material-icons">person</i>
                                <p class="d-lg-none d-md-block">
                                    Conta
                                </p>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                                <p class="dropdown-item" href="#"><?= $_SESSION['usuario_logado']['username'] ?></p>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="../deslogar.php">Sair</a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <!-- End Navbar -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header card-header-warning">
                                <h4 class="card-title">Pesquisar Links</h4>
                            </div>
                            <div class="card-body">
                                <form class="form">
                                    <input type="hidden" id="acao" name="acao" value="search">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group bmd-form-group">
                                                <label for="name">Link Curto</label>
                                                <input type="text" class="form-control" id="name" name="name"
                                                       aria-describedby="name-help">
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="form-group bmd-form-group">
                                                <label for="link">Link Original</label>
                                                <input type="text" class="form-control" id="link" name="link">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row text-right">
                                        <div class="col-md-12">
                                            <button type="submit" id="search-shortlink"
                                                    class="btn btn-success mb-1">
                                                Pesquisar
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="d-flex justify-content-center">
                            <div id="spinner" class="spinner-border text-primary" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header card-header-warning">
                                <h4 class="card-title">Links</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <h4 class="card-title">Aqui estão os links cadastrados</h4>
                                <table class="table table-striped" id="result_search">
                                    <thead class="text-capitalize text-warning">
                                    <tr>
                                        <td>Link Curto</td>
                                        <td>Link Original</td>
                                        <td>Usuário</td>
                                        <td>Ação</td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php if ($urls): ?>
                                        <?php foreach ($urls as $url): ?>
                                            <tr>
                                                <td>
                                                    <a class="nav-link" target="_blank" href="../redirect.php?short=<?= $url['name'] ?>"><?= $url['name'] ?></a>
                                                </td>
                                                <td>
                                                    <?= $url['url'] ?>
                                                </td>
                                                <td>
                                                    <?= $url['username'] ?>
                                                </td>
                                                <td>
                                                    <a class="nav-link"
                                                       onclick="javascript: return confirm('Deseja excluir o link?');"
                                                       href="./links.php?excluir=<?= $url['id_url'] ?>">
                                                        <i class="material-icons">delete_forever</i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
</div>
</div>

<!--   Core JS Files   -->
<script src="../assets/js/core/jquery.min.js"></script>
<script src="../assets/js/core/popper.min.js"></script>
<script src="../assets/js/core/bootstrap-material-design.min.js"></script>
<script src="../assets/js/plugins/perfect-scrollbar.jquery.min.js"></script>
<script src="../assets/js/plugins/moment.min.js"></script>
<!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
<script src="../assets/js/material-dashboard.js?v=2.1.2" type="text/javascript"></script>

<script src="../assets/vendor/jquery/jquery.min.js"></script>
<script src="main.js"></script>
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