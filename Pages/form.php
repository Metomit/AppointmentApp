<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            input[type="date"],
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
            #info{
                font-family: Arial, sans-serif;
                font-weight: bold;
                font-size: 14px;
                color: #FFA500;
            }
        </style>
    </head>
    <body>
        <?php 
            include("database.php");
            $database_obj = new Database();

            include("submission.php");
            $formValidator = new AppointmentFormHandler($database_obj);

            $queriedId = $queriedStartTime = $queriedEndTime = $queriedDate = $queriedService = $queriedStatus = $queriedSpecialReq = $queriedContactId = $queriedName = $queriedSurname = $queriedEmail = $queriedPhonenumber = "";

            if($_SERVER["REQUEST_METHOD"]=="POST"){

                if(empty($_POST["appointment_id"])){
                    $formValidator->scheduleNewAppointment();
                }
                else{
                    $formValidator->updateAppointment();
                }
                
            }
            if($_SERVER["REQUEST_METHOD"]=="GET" && isset($_GET["appointment_id"])){

                $idquery = "SELECT * FROM appointments a join contactinfo c on a.appointment_contact_info_id=c.contact_id WHERE a.appointment_id=".$_GET["appointment_id"];
                
                $res = $database_obj->executeQuery($idquery)->fetch_assoc();
                
                $queriedId = $res["appointment_id"];
                $queriedStartTime = date('H:i', strtotime($res["appointment_start_time"]));
                $queriedEndTime = date('H:i', strtotime($res["appointment_end_time"]));
                $queriedDate = $res["appointment_date"];
                $queriedService = $res["appointment_service"];
                $queriedStatus = $res["appointment_status"];
                $queriedSpecialReq = $res["appointment_special_requirements"];
                $queriedContactId = $res["contact_id"];
                $queriedName = $res["contact_name"];
                $queriedSurname = $res["contact_surname"];
                $queriedEmail = $res["contact_email"];
                $queriedPhonenumber = $res["contact_phonenumber"];

            }
        ?>
        <form method="post" action="form.php">   
            <input type="hidden" name="appointment_id" id="appointment_id" value=<?php echo "\"{$queriedId}\""?>>
            <div class="box">
                <label>Start Time:</label><input type="text" name="startTime" id="startTime" value=<?php echo "\"{$queriedStartTime}\""?>/><span class="error">* <?php echo $formValidator->getStartTimeErr();?></span>
            </div>
            <div class="box">
                <label>End Time:</label><input type="text" name="endTime" id="endTime" value=<?php echo "\"{$queriedEndTime}\""?>/><span class="error">* <?php echo $formValidator->getEndTimeErr();?></span>
            </div>
            <div class="box">
                <label>Date:</label><input type="date" name="date" id="date" value=<?php echo "\"{$queriedDate}\""?>/><span class="error">* <?php echo $formValidator->getDateErr();?></span>
            </div>
            <span class="error"><?php echo $formValidator->getOverlapErr();?></span>
            <div class="box">
                <label>Service:</label>
                <select name="service" id="service" value=<?php echo "\"{$queriedService}\""?>>
                    <?php
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
                <label>Status:</label><input type="text" name="status" id="status" value=<?php echo "\"{$queriedStatus}\""?> />
            </div>
            <div class="box">
                <label>Special requirements/Pre-existing medical condition:</label><textarea type="text" name="specialReq" id="specialReq" value=<?php echo "\"{$queriedSpecialReq}\""?>></textarea>
            </div>
            <input type="hidden" name="contact_id" id="contact_id" value=<?php echo "\"{$queriedContactId}\""?>/>
            <div id="contactInfo">
                <h3>Contact info:</h3>
                <div>
                    <div class="box">
                        <label>First name:</label><input type="text" name="contactName" id="contactName" value=<?php echo "\"{$queriedName}\""?>/>
                    </div>
                    <div class="box">
                        <label>Last name:</label><input type="text" name="contactSurname" id="contactSurname" value=<?php echo "\"{$queriedSurname}\""?>/>
                    </div>
                    <div class="box">
                        <label>Email:</label><input type="text" name="email" id="email" value=<?php echo "\"{$queriedEmail}\""?>/><span class="error">* <?php echo $formValidator->getEmailErr();?></span>
                    </div>
                    <div class="box">
                        <label>Phone number:</label><input type="text" name="phoneNumber" id="phoneNumber" value=<?php echo "\"{$queriedPhonenumber}\""?>/>
                    </div>
                </div>
            </div>
            <div class="box">
                <span id="info">*Our specialist only works Mon – Fri from 09:00 – 17:00</span>
            </div>
            <input id="submit" type="submit" class="btn-submit" value="Submit" />
        </form>
    </body>
</html>