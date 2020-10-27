<?php
    if (!isset($_SESSION['usuario_logado'])) {
        session_reset();
        session_destroy();

        $urlAtual = explode('/', $_SERVER['REQUEST_URI']);
        $urlRedirecionar = 'login.php';
        if (in_array('admin', $urlAtual)) {
            $urlRedirecionar = '../login.php';
        }
        echo "<script language='javascript' type='text/javascript'>
                    window.location.href='$urlRedirecionar';
                    </script>";
    }
?>