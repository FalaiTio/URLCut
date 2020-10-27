<?php
    session_start();
    session_destroy();

    $urlAtual = explode('/', $_SERVER['REQUEST_URI']);
    $urlRedirecionar = 'login.php';
    if (in_array('admin', $urlAtual)) {
        $urlRedirecionar = '../login.php';
    }

    header('Location: ' . $urlRedirecionar);
    $success = true;
?>