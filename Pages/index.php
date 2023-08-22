<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <title>Appointment Application</title>
    </head>
    <body>
        <?php 
            echo "And a php example " . "test<br>";
            include("database.php");
            $obj = new Database();
            $status = $obj->executeQuery("SELECT * FROM Appointments")->fetch_assoc()["appointment_id"] . "<br>";
            echo $status;
        ?>
        <form>
            <div class="box">
                <label>Start Time:</label><input type="text" name="startTime" id="startTime" />
            </div>
            <div class="box">
                <label>End Time:</label><input type="text" name="endTime" id="endTime" />
            </div>
            <div class="box">
                <label>Service:</label><input type="email" name="email" id="email" />
                <select name="service" id="service">
                    <?php
                        
                    ?>
                    <option value="volvo">Volvo</option>
                    <option value="saab">Saab</option>
                    <option value="mercedes">Mercedes</option>
                    <option value="audi">Audi</option>
                </select>
            </div>
            <div class="box">
                <label>Message:</label><textarea type="text" name="message" id="message"></textarea>
            </div>
            <input id="submit" type="button" class="btn-submit" value="Submit" />
        </form>
    </body>
</html>