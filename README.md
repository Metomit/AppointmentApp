# AppointmentApp

## Description

This project aims to create a simple web application for managing doctor's appointments. The app is designed for a medical practice with a single doctor who works on weekdays from 09:00 to 17:00. Patients can use the app to book appointments for specific services provided by the doctor. The app also allows rescheduling and cancellation of appointments.

## Setup

1. Firstly make sure you have PHP 8.2.9 and MySQL Community Server Ver 8.1.0 installed.
2. Clone this repository to your local machine.
3. Import the provided MySQL database export to set up the necessary database structure.

## IMPORTANT

Tweak the parameters in the Pages/database.php page to your accordance. I named the database 'appointmentappdb' and the db user and password is the admin user on my MySQL server setup.

## Details

The app requires no technologies other than PHP and MySQL installed. It runs using the built-in server that comes with PHP therefore it's minimalist and as concise as possible, with no overhead or additional setup needed for something like an Apache or nginx server.

The php.ini is used simply to load the mysqli module to work wiith MySQL in PHP.

run.bat is a quick way to boot the server properly ("php -S localhost:8080 -t Pages -c php.ini" is the command if inteded to start server manually).

The index.php displays all the current appointments in the database. By clicking 'Schedule an appointment', we can schedule a new appointment through form.php and therefore add a new entry to the database. The same can be done by click Reschedule in the Action column to modify a given record. Deleting an appointment is done through the 'Cancel' button in the Actoin column and is done via the delete.php page.

## Technologies Used

- PHP
- MySQL
- Javascript (jQuery)
- CSS
- HTML
