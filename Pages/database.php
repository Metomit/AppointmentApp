<?php
    class Database{
        private $db_server;
        private $db_user;
        private $db_password;
        private $db_name;
        private $conn;

        public function __construct() {
            $this->db_server = "localhost";
            $this->db_user = "root";
            $this->db_password = "toor";
            $this->db_name = "appointmentappdb";

            try{
                $this->conn = new mysqli($this->db_server, $this->db_user, $this->db_password, $this->db_name);
            }
            catch(mysqli_sql_exception){
                echo "Database connection error!";
                exit();
            }
        }

        public function executeQuery($qstring){
            try{
                $result = $this->conn->query($qstring);
                return $result;
            }
            catch(mysqli_sql_exception $e){
                echo "Database connection error!";
                echo $e->getMessage();
                $this->conn -> close();
                exit();
            }
        }

        public function close() {
            $this->conn->close();
        }
    }
?>