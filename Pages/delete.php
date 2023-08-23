<?php
    include("database.php");
    if($_SERVER["REQUEST_METHOD"]=="GET"){
        if(isset($_GET["appointment_id"])){
            $id = $_GET["appointment_id"];
            
            $database_obj = new Database();

            $query = "DELETE FROM appointments WHERE appointment_id=".$id;

            $result = $database_obj->executeQuery($query);

            $database_obj->close();

            $response = $result?"Successfully canceled appointment":"Cancelation failed due to database error";

            echo $response;
        }
    }
?>