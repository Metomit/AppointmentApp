<!DOCTYPE html>
<html>
    <head>
        <title>Appointment App</title>
    </head>

    <body>
        The content of the document......
        <?php
            echo "And a php example " . "test<br>";
            include("database.php");
            $obj = new Database();
            $status = $obj->executeQuery("SELECT * FROM Appointments")->fetch_assoc()["appointment_id"] . "<br>";
            echo $status;
        ?>
    </body>

</html> 