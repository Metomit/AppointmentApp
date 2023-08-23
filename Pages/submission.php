<?php
class AppointmentFormHandler {
    private $database_obj;
    private $startTime,$endTime,$date,$service,$status,$specialReq,$contactName,$contactSurname,$email,$phoneNumber;
    private $startTimeErr,$endTimeErr,$dateErr,$emailErr,$overlapErr;

    public function __construct($database_obj) {
        $this->database_obj = $database_obj;
        $this->startTime = $this->endTime = $this->date = $this->service = $this->status = $this->specialReq = $this->contactName = $this->contactSurname = $this->email = $this->phoneNumber = "";
        $this->startTimeErr = $this->endTimeErr = $this->dateErr = $this->emailErr = $this->overlapErr = "";
    }

    private function validateTime($time){
        $bits = explode(':', $time);
        if ($bits[0] >= 17 || $bits[1] >= 60 || count($bits) != 2 || $bits[0] < 9){
            return false;
        }
        return true;
    }

    public function processForm($isUpdate) {

        // $this->startTime checks--------------
        if (empty($_POST["startTime"])) {
            $this->startTimeErr = "Start time is required";
        } else if (!$this->validateTime($_POST["startTime"])) {
            $this->startTimeErr = "Please enter a valid time format (HH:MM) or make sure the time is within work hours";
        } else {
            $this->startTime = $_POST["startTime"];
        }

        // $this->endTime checks-------------
        if (empty($_POST["endTime"])) {
            $this->endTimeErr = "End time is required";
        } else if (!$this->validateTime($_POST["endTime"])) {
            $this->endTimeErr = "Please enter a valid time format (HH:MM) or make sure the time is within work hours";
        } else if(strtotime($_POST["endTime"])<=strtotime($_POST["startTime"])){
            $this->endTimeErr = "End time cannot be less than start time";
        }
         else {
            $this->endTime = $_POST["endTime"];
        }

        // $this->date checks-------------
        if (empty($_POST["date"])) {
            $this->dateErr = "The date field is required";
        } else if (DateTime::createFromFormat('Y-m-d', $_POST["date"]) == false) {
            $this->dateErr = "Please enter a valid date (YYYY-MM-DD)";
        } else if (date("w",strtotime($_POST["date"]))==0 || date("w",strtotime($_POST["date"]))==6) {
            $this->dateErr = "The date you entered is durng the weekend, please enter a valid workday";
        } else {
            $this->date = $_POST["date"];
        }

        // $this->overlap check-------------
        if (empty($this->startTimeErr) && empty($this->endTimeErr) && empty($this->dateErr)) {
            $query = "";
            if($isUpdate==true){
                echo "SELECT * FROM appointments WHERE NOT appointment_id=".$_POST["appointment_id"].'<br>';
                $query = "SELECT COUNT(*) AS overlap_count FROM (SELECT * FROM appointments WHERE NOT appointment_id=".$_POST["appointment_id"].") derived
                            WHERE (appointment_start_time < TIME('".$this->endTime."')) AND
                            (appointment_end_time > TIME('".$this->startTime."')) AND DATE(appointment_date)='".$this->date."'";
            }
            else{
                $query = "SELECT COUNT(*) AS overlap_count FROM appointments WHERE (appointment_start_time < TIME('".$this->endTime."')) AND
                            (appointment_end_time > TIME('".$this->startTime."')) AND DATE(appointment_date)='".$this->date."'";
            }
            echo $query;

            $result = $this->database_obj->executeQuery($query);
            $row = $result->fetch_assoc();
            echo '<br>'.$row["overlap_count"].'<br>';
            if ($row['overlap_count'] > 0) {
                $this->overlapErr = "* Appointment overlaps with an existing appointment";
            }
        }

        // $this->status check-------------
        if (empty($_POST["status"])) {
            $currentTime = time();
            if ($currentTime < strtotime($_POST["date"].$_POST["startTime"])) {
                $this->status = "WAITING FOR APPOINTMENT";
            } else if ($currentTime < strtotime($_POST["date"].$_POST["endTime"])) {
                $this->status = "CURRENTLY AT APPOINTMENT";
            } else {
                $this->status = "PAST DUE";
            }
        } else {
            $this->status = $_POST["status"];
        }

        // $this->email check-------------
        if (empty($_POST["email"])) {
            $this->emailErr = "Please enter an email";
        } else {
            $this->email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
        }

        $this->specialReq = filter_var($_POST["specialReq"], FILTER_SANITIZE_SPECIAL_CHARS);
        $this->contactName = filter_var($_POST["contactName"], FILTER_SANITIZE_SPECIAL_CHARS);
        $this->contactSurname = filter_var($_POST["contactSurname"], FILTER_SANITIZE_SPECIAL_CHARS);
        $this->phoneNumber = filter_var($_POST["phoneNumber"], FILTER_SANITIZE_NUMBER_INT);

        $this->service = $_POST["service"];
    }

    public function scheduleNewAppointment(){

        $this->processForm(false);

        // Query execution
        if (empty($this->startTimeErr) && empty($this->endTimeErr) && empty($this->dateErr) && empty($this->overlapErr) && empty($this->emailErr)) {
            $contactQuery = "INSERT INTO contactinfo(contact_name, contact_surname, contact_email, contact_phonenumber) VALUES
            (".(empty($this->contactName) ? "NULL" : "'".$this->contactName."'").",".
            (empty($this->contactSurname) ? "NULL" : "'".$this->contactSurname."'").",'".$this->email."',".
            (empty($this->phoneNumber) ? "NULL" : "'".$this->phoneNumber."'").")";
            echo $contactQuery.'<br>';

            $this->database_obj->executeQuery($contactQuery);

            $lastInsertedQuery = "SELECT * FROM contactinfo ORDER BY contact_id DESC LIMIT 1";
            $lastInsertedId = $this->database_obj->executeQuery($lastInsertedQuery)->fetch_assoc()["contact_id"];

            echo $lastInsertedId.'<br>';

            $appointmentQuery = "INSERT INTO appointments(appointment_start_time, appointment_end_time, appointment_date, appointment_status, appointment_service_id,
            appointment_special_requirements, appointment_contact_info_id) VALUES
            ('".$this->startTime."','".$this->endTime."','".$this->date."','".$this->status."',".$this->service.",'".$this->specialReq."',".$lastInsertedId.")";
            echo $appointmentQuery;

            $this->database_obj->executeQuery($appointmentQuery);

            echo "<script>window.location.href='index.php';alert('Succesfully scheduled appointment');</script>";
            exit();
        }
    }

    public function updateAppointment(){
        
        $this->processForm(true);

        // Query execution
        if (empty($this->startTimeErr) && empty($this->endTimeErr) && empty($this->dateErr) && empty($this->overlapErr) && empty($this->emailErr)) {

            $contactQuery = "UPDATE contactinfo SET contact_name='".$this->contactName."', contact_surname='".$this->contactSurname."', contact_email='".
                                $this->email."', contact_phonenumber='".$this->phoneNumber."' WHERE contact_id=".$_POST["contact_id"];

            $appointmentQuery = "UPDATE appointments SET appointment_start_time='".$this->startTime."', appointment_end_time='".$this->endTime.
                                "', appointment_date='".$this->date."', appointment_status='".$this->status."', appointment_service_id=".$this->service.
                                ", appointment_special_requirements='".$this->specialReq."' WHERE appointment_id=".$_POST["appointment_id"];


            
            echo '<br>'.$contactQuery.'<br><br>'.$appointmentQuery;

            $this->database_obj->executeQuery($contactQuery);

            $this->database_obj->executeQuery($appointmentQuery);

            //header("Location: index.php");
            //echo "<script>alert('Succesfully rescheduled appointment');</script>";
            echo "<script>window.location.href='index.php';alert('Succesfully rescheduled appointment');</script>";
            exit();
        }
    }

    public function getStartTimeErr() {
        return $this->startTimeErr;
    }

    public function getEndTimeErr() {
        return $this->endTimeErr;
    }

    public function getDateErr() {
        return $this->dateErr;
    }

    public function getEmailErr() {
        return $this->emailErr;
    }

    public function getOverlapErr() {
        return $this->overlapErr;
    }

}
?>