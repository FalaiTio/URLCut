<?php
    require '../database/db.php';
    session_start();
    include '../verifica_sessao.php';

    $db = Db::getInstance();
    $connection = $db->getConnection();

    $retorno = array();
    if ($_POST) {

        if ($_POST['acao'] == 'add') {
            $username = htmlspecialchars($_POST["username"]);
            $name = htmlspecialchars($_POST["name"]);
            $link = htmlspecialchars($_POST["link"]);

            $sql_search_url = "SELECT name FROM tb_url WHERE name = '$name'";
            $stmt = $connection->query($sql_search_url);
            $result_name = $stmt->fetch();

            if ($result_name) {
                $retorno = array('erro' => true, 'mensagem' => 'Link curto ' . $name . ' já cadastrado, por favor, insira outro nome para o seu link');
                echo json_encode($retorno);
                exit;
            }

            $sql_search_url = "SELECT name FROM tb_url WHERE url = '$link'";
            $stmt = $connection->query($sql_search_url);
            $result_url = $stmt->fetch();

            if ($result_url) {
                $retorno = array('erro' => true, 'mensagem' => 'Link ' . $link . ' já cadastrado, por favor, insira outro link');
                echo json_encode($retorno);
                exit;
            }
            $sql_insert_url = "INSERT INTO tb_url (id_url, name, url, username)
                VALUES (null, '$name', '$link', '$username')";

            $connection->exec($sql_insert_url);
            $retorno = array('sucesso' => true);

            echo json_encode($retorno);
        }

        if ($_POST['acao'] == 'search') {
            $name = htmlspecialchars($_POST["name"]);
            $link = htmlspecialchars($_POST["link"]);

            $sql_search_url = "SELECT * FROM tb_url WHERE 1=1";
            if (!empty($name)) {
                $sql_search_url .= " AND name LIKE '%$name%'";
            }
            if (!empty($link)) {
                $sql_search_url .= " AND url LIKE '%$link%'";
            }

            $stmt = $connection->query($sql_search_url);
            $result = $stmt->fetchAll();

            if (!$result) {
                $retorno = array('erro' => true, 'mensagem' => 'Nenhum Link encontrado com os parâmetros informados.');
                echo json_encode($retorno);
                exit;
            } else {
                $retorno = array('sucesso' => true, 'resultado' => $result);
                echo json_encode($retorno);
                exit;
            }

        }
    }
