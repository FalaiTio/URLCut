<?php

    /*
     * Criado por Ramon Veloso
     * ramon.onix@gmail.com
     */

    class Db
    {
        private $_connection;
        private static $_instance;
        private $_host = 'localhost';
        private $_username = 'root';
        private $_password = 'ADMSERVER';
        private $_database = 'shorturl';

        /*
        Get an instance of the Database
        @return Instance
        */
        public static function getInstance()
        {
            if (!self::$_instance) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        private function __construct()
        {
            try {

                $this->_connection = new \PDO("mysql:host=$this->_host;dbname=$this->_database", $this->_username, $this->_password);

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        private function __clone()
        {
        }

        public function getConnection()
        {
            return $this->_connection;
        }

        public function verificainstalacao()
        {
            $db = Db::getInstance();
            $connection = $db->getConnection();

            $sql_verify = "SELECT table_name FROM information_schema.tables 
                           WHERE TABLE_SCHEMA = '$this->_database' 
                           AND TABLE_NAME = 'tb_url'";

            $stmt = $connection->query($sql_verify);

            if ($stmt) {
                $result = $stmt->fetch();
                if ($result) {
                    return true;
                }
            }

            return false;
        }

        public function createUserTb()
        {
            $db = Db::getInstance();
            $connection = $db->getConnection();

            //Criar tbl para usuário
            $create_tbl_user = 'CREATE TABLE tb_user (
                id_user INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(100) NOT NULL,
                password VARCHAR(250) NOT NULL  
            )';

            $connection->exec($create_tbl_user);

        }

        public function createUrlTb()
        {
            $db = Db::getInstance();
            $connection = $db->getConnection();

            //Criar tbl para url
            $create_tbl_url = 'CREATE TABLE tb_url (
                id_url INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                url VARCHAR(250) NOT NULL,
                username VARCHAR(100) NOT NULL
            )';

            $connection->exec($create_tbl_url);

        }

        public function createStatsTb()
        {
            $db = Db::getInstance();
            $connection = $db->getConnection();

            //Criar tbl para stats
            $create_tbl_stats = 'CREATE TABLE tb_stats (
                id_stats INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                timestamp VARCHAR(250),
                value INT(6)
            )';

            $connection->exec($create_tbl_stats);
        }
    }

    //    class Post {
    //
    //        public function __construct(){
    //            $db = Db::getInstance();
    //            $this->_dbh = $db->getConnection();
    //
    //        }
    //
    //        public function getPosts()
    //        {
    //            try {
    //
    //                /*** The SQL SELECT statement ***/
    //                $sql = "SELECT * FROM posts";
    //                foreach ($this->_dbh->query($sql) as $row) {
    //                    var_dump($row);
    //                }
    //
    //                /*** close the database connection ***/
    //                $this->_dbh = null;
    //            } catch (PDOException $e) {
    //                echo $e->getMessage();
    //            }
    //        }
    //
    //    }