<?php
/* php& mysqldb connection file */
$user = "root"; //mysqlusername
$pass = ""; //mysqlpassword
$host = "localhost"; //server name or ipaddress
$dbname= "intern-portal"; //your db name
$dbconn = mysqli_connect($host, $user, $pass, $dbname);
if (!$dbconn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>