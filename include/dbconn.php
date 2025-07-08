<?php
/* PHP & MySQL database connection file */
$user = "root"; //mysql username
$pass = ""; //mysql password  
$host = "localhost"; //server name or ip address
$dbname = "intern_portal"; //your db name

// Create connection with error handling
$dbconn = mysqli_connect($host, $user, $pass, $dbname);

if (!$dbconn) {
    // Log the error securely (don't expose database details)
    error_log("Database connection failed: " . mysqli_connect_error());
    die("Database connection error. Please try again later.");
}

// Set charset to prevent character set confusion attacks
mysqli_set_charset($dbconn, "utf8");

// Enable strict mode for better data integrity (MySQL 8.0+ compatible)
mysqli_query($dbconn, "SET sql_mode = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION'");
?>