<?php
    include 'config.php';
    
    class db_model{
        public $conn;

        function __construct() {
            $this->conn = new mysqli(DBHOST, DBUSER, DBPWD, DBNAME);
        }

        function get_query($sql) {
            $result = $this->conn->query($sql);
            while ($rows[] = $result->fetch_assoc());
            $result->close();
            return $rows;
        }

        function set_query($sql) {
            $result = $this->conn->query($sql);
            return $result;
        }

        function __destruct() {
            $this->conn->close();
        }
    }
?>