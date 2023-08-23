<?php
$startTime = $endTime = $date = $service = $status = $specialReq = $contactName = $contactSurname = $email = $phoneNumber = "";
$startTimeErr = $endTimeErr = $dateErr = $serviceErr = $statusErr = $specialReqErr = $contactNameErr = $contactSurnameErr = $emailErr = $phoneNumberErr = $overlapErr = "";

function validateTime($time){
    $bits = explode(':', $time);
    if ($bits[0] > 17 || $bits[1] >= 60 || count($bits) != 2 || $bits[0] < 9){
        return false;
    }
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //$startTime checks
    if (empty($_POST["startTime"])) {
        $startTimeErr = "Start time is required";
    } 
    else if (!validateTime($_POST["startTime"])){
        $startTimeErr = "Please enter a valid time format (HH:MM) or make sure the time is within work hours";
    }
    else{
        $startTime = $_POST["startTime"];
    }

    //$endTime checks-------------

    if (empty($_POST["endTime"])) {
        $endTimeErr = "End time is required";
    } 
    else if (!validateTime($_POST["endTime"])){
        $endTimeErr = "Please enter a valid time format (HH:MM) or make sure the time is within work hours";
    }
    else{
        $endTime = $_POST["endTime"];
    }

    //$date checks-------------

    if (empty($_POST["date"])) {
        $dateErr = "The date field is required";
    } 
    else if (DateTime::createFromFormat('Y-m-d', $_POST["date"]) == false) {
        $dateErr = "Please enter a valid date (YYYY-MM-DD)";
    }
    else{
        $date = $_POST["date"];
    }


    //$overlap check-------------

    if(empty($startTimeErr) && empty($endTimeErr) && empty($dateErr)){

        $query = "SELECT COUNT(*) AS overlap_count FROM appointments WHERE (appointment_start_time <= '".$startTime."' AND
                    appointment_end_time >= '".$endTime."' AND DATE(appointment_date)='".$date."')";
        echo $query;

        //$queryObj = new Database();
        $result = $database_obj->executeQuery($query);
        $row = $result->fetch_assoc();
        if ($row['overlap_count'] > 0) {
            $overlapErr = "* Appointment overlaps with an existing appointment";
        }
    }

    //$status check-------------

    if (empty($_POST["status"])) {
        $currentTime = time();
        if($currentTime<strtotime($_POST["date"].$_POST["startTime"])){
            $status = "WAITING FOR APPOINTMENT";
        }
        else if($currentTime<strtotime($_POST["date"].$_POST["endTime"])){
            $status = "CURRENTLY AT APPOINTMENT";
        }
        else{
            $status = "PAST DUE";
        }

    } 
    else {
        $status = $_POST["status"];
    }

    //email check-------------

    if(empty($_POST["email"])){
        $emailErr = "Please enter an email";
    }
    else{
        $email = filter_var($_POST["email"],FILTER_SANITIZE_EMAIL);
    }

    $specialReq = filter_var($_POST["specialReq"],FILTER_SANITIZE_SPECIAL_CHARS);
    $contactName = filter_var($_POST["contactName"],FILTER_SANITIZE_SPECIAL_CHARS);
    $contactSurname = filter_var($_POST["contactSurname"],FILTER_SANITIZE_SPECIAL_CHARS);
    $phoneNumber = filter_var($_POST["phoneNumber"],FILTER_SANITIZE_NUMBER_INT);

    $service = $_POST["service"];

    //Query execution

    if(empty($startTimeErr) && empty($endTimeErr) && empty($dateErr) && empty($overlapErr) && empty($emailErr)){
        

        $contactQuery = "INSERT INTO contactinfo(contact_name,contact_surname,contact_email,contact_phonenumber) VALUES
        (".(empty($contactName)?"NULL":"'".$contactName."'").",".(empty($contactSurname)?"NULL":"'".$contactSurname."'").",'"
        .$email."',".(empty($phoneNumber)?"NULL":"'".$phoneNumber."'").")";
        echo $contactQuery.'<br>';

        $database_obj->executeQuery($contactQuery);

        $lastInsertedQuery = "SELECT * FROM contactinfo ORDER BY contact_id DESC LIMIT 1";
        $lastInsertedId = $database_obj->executeQuery($lastInsertedQuery)->fetch_assoc()["contact_id"];

        echo $lastInsertedId.'<br>';

        $appointmentQuery = "INSERT INTO appointments(appointment_start_time,appointment_end_time,appointment_date,appointment_status,appointment_service_id,
        appointment_special_requirements,appointment_contact_info_id) VALUES
        ('".$startTime."','".$endTime."','".$date."','".$status."',".$service.",'".$specialReq."',".$lastInsertedId.")";
        echo $appointmentQuery;

        $database_obj->executeQuery($appointmentQuery);
    }
}
?>