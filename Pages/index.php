<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <title>Appointment Application</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            form {
                background-color: #fff;
                border-radius: 5px;
                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                padding: 20px;
                width: 400px;
                margin: 20px auto;
            }
            .box {
                margin-bottom: 10px;
            }
            label {
                display: block;
                font-weight: bold;
                margin-bottom: 5px;
            }
            input[type="text"],
            select,
            textarea {
                width: 100%;
                padding: 8px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
            }
            select {
                cursor: pointer;
            }
            textarea {
                resize: vertical;
                min-height: 100px;
            }
            #submit {
                display: block;
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 10px 15px;
                border-radius: 4px;
                cursor: pointer;
                width: 100%;
            }
            #submit:hover {
                background-color: #0056b3;
            }
            #contactInfo {
                border: 1px solid #ccc;
                margin-bottom: 15px;
                border-radius: 4px;
                padding: 5px 5px 5px 5px;
                height: 220px;
            }
            #contactInfo>div{
                transform: scale(0.7);
                -webkit-transform-origin-x: 0;
                -webkit-transform-origin-y: 0;
            }
            #contactInfo>h3{
                margin: 0;
                margin-bottom: 10px;
            }
            .error{
                color: #FF0000;
            }
        </style>
    </head>
    <body>
        <?php 
            include("database.php");
            $database_obj = new Database();
            include("submission.php");
            //include("database.php");
            //$obj = new Database();
            //$status = $obj->executeQuery("SELECT * FROM Appointments")->fetch_assoc()["appointment_id"] . "<br>";
            //echo $status;
        ?>
        <form method="post" action="index.php">   
            <div class="box">
                <label>Start Time:</label><input type="text" name="startTime" id="startTime" /><span class="error">* <?php echo $startTimeErr;?></span>
            </div>
            <div class="box">
                <label>End Time:</label><input type="text" name="endTime" id="endTime" /><span class="error">* <?php echo $endTimeErr;?></span>
            </div>
            <div class="box">
                <label>Date:</label><input type="text" name="date" id="date" /><span class="error">* <?php echo $dateErr;?></span>
            </div>
            <span class="error"><?php echo $overlapErr;?></span>
            <div class="box">
                <label>Service:</label>
                <select name="service" id="service">
                    <?php
                        //include("database.php");
                        //$database_obj = new Database();
                        $services_result = $database_obj->executeQuery("SELECT * FROM Services");
                        $services = array();
                        while($row = $services_result->fetch_assoc()) {
                            $services[$row['service_id']] = $row['service_name'];
                        }
                        $service_options = "";
                        foreach($services as $key => $value) {
                            $service_options .= '<option value="'.$key.'">'.$value.'</option>';
                        }
                        echo $service_options;
                    ?>
                </select>
            </div>
            <div class="box">
                <label>Status:</label><input type="text" name="status" id="status" />
            </div>
            <div class="box">
                <label>Special requirements/Pre-existing medical condition:</label><textarea type="text" name="specialReq" id="specialReq"></textarea>
            </div>
            <div id="contactInfo">
                <h3>Contact info:</h3>
                <div>
                    <div class="box">
                        <label>First name:</label><input type="text" name="contactName" id="contactName" />
                    </div>
                    <div class="box">
                        <label>Last name:</label><input type="text" name="contactSurname" id="contactSurname" />
                    </div>
                    <div class="box">
                        <label>Email:</label><input type="text" name="email" id="email" /><span class="error">* <?php echo $emailErr;?></span>
                    </div>
                    <div class="box">
                        <label>Phone number:</label><input type="text" name="phoneNumber" id="phoneNumber" />
                    </div>
                </div>
            </div>
            <input id="submit" type="submit" class="btn-submit" value="Submit" />
        </form>
        <!--script>
            $(document).ready(function() {

                $("#submit").click(function() {

                    var startTime = $("#startTime").val();
                    var endTime = $("#endTime").val();
                    var date = $("#date").val();
                    var status = $("#status").val();
                    var service = $("#service").val();
                    var specialReq = $("#specialReq").val();
                    var contactName = $("#contactName").val();
                    var contactSurname = $("#contactSurname").val();
                    var email = $("#email").val();
                    var phoneNumber = $("#phoneNumber").val();

                    $.ajax({
                        type: "POST",
                        url: "localhost:8080/index.php",
                        dataType: 'json',
                        data: {
                            startTime: startTime,
                            endTime: endTime,
                            date: date,
                            status: status,
                            service: service,
                            specialReq: specialReq,
                            contactName: contactName,
                            contactSurname: contactSurname,
                            email: email,
                            phoneNumber: phoneNumber,
                        },
                        cache: false
                    });

                });

            });
        </script-->
    </body>
</html>