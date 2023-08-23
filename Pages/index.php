<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
        <title>Appointment App</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
            }

            a {
                text-decoration: none;
                color: inherit;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                border: 1px solid #ccc;
                margin: 20px 0;
            }

            th {
                background-color: #f2f2f2;
                color: #333;
                font-weight: bold;
                padding: 10px;
                border: 1px solid #ccc;
                text-align: left;
            }

            td {
                padding: 10px;
                border: 1px solid #ccc;
            }

            tr:nth-child(even) {
                background-color: #f7f7f7;
            }
            #create {
                display: block;
                background-color: #007bff;
                color: #fff;
                border: none;
                padding: 0;
                border-radius: 4px;
                cursor: pointer;
                width: 235px;
            }
            #create:hover {
                background-color: #0056b3;
            }
            #create>a{
                display: inline-block;
                padding: 10px 20px;
                background-color: #007bff;
                color: #ffffff;
                text-decoration: none;
                border-radius: 5px;
                transition: background-color 0.3s ease-in-out;
                font-weight: bold;
            }
            .edit:hover,
            .delete:hover{
                cursor: pointer;
                color: red;
            }
        </style>
    </head>
    <body>
        <?php
            include("database.php");
            $database_obj = new Database();
        ?>
        <div id="create"><a href="/form.php">Schedule an appointment</a></div>
        <?php
            $query = "SELECT * FROM (SELECT * FROM appointments a join contactinfo c on a.appointment_contact_info_id=c.contact_id)
                        j join services s on s.service_id=j.appointment_service_id ORDER BY a.appointment_date, a.appointment_start_time;";
            $result = $database_obj->executeQuery($query);
            
            if ($result->num_rows > 0) {
                echo "<table border='1'>
                        <tr>
                        <th>Action</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Service</th>
                        <th>Special requirements/Pre-existing medical condition</th>
                        <th>Patient first name</th>
                        <th>Patient last name</th>
                        <th>Patient emaiil</th>
                        <th>Patient phone number</th>
                        </tr>";
                
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td><input type=\"hidden\" name=\"appointment_id\" value=\"{$row['appointment_id']}\">
                            <a class=\"edit\" href=\"form.php?appointment_id={$row['appointment_id']}\">Reschedule</a> <span class=\"delete\">Cancel</span></td>
                            <td>{$row['appointment_start_time']}</td>
                            <td>{$row['appointment_end_time']}</td>
                            <td>{$row['appointment_date']}</td>
                            <td>{$row['appointment_status']}</td>
                            <td>{$row['service_name']}</td>
                            <td>{$row['appointment_special_requirements']}</td>
                            <td>{$row['contact_name']}</td>
                            <td>{$row['contact_surname']}</td>
                            <td>{$row['contact_email']}</td>
                            <td>{$row['contact_phonenumber']}</td>
                            </tr>";
                }
            
                echo "</table>";
            }
            else{
                echo "No appointments scheduled yet";
            }
        ?>
        <script>
            $(document).ready(function() {

                $(".delete").click(function() {

                    var appointment_id = $($(this).parent().find("input")[0]).val();
                    //console.log(appointment_id);

                    $.ajax({
                        type: "GET",
                        url: "delete.php",
                        data: {
                            appointment_id: appointment_id
                        },
                        cache: false,
                        success: function(response) {
                            console.log("Returned: " + response);
                            location.reload(true);
                            alert(response);
                        },
                        error: function(xhr, status, error) {
                            console.error("Error: " + error);
                        }
                    });

                });

            });
        </script>
    </body>
</html>