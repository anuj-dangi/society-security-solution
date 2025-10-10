<?php

$sname= "localhost";
$unmae= "anuj";
$password = "anuj";
$db_name = "society_security";

$conn = mysqli_connect($sname, $unmae, $password, $db_name);

if (!$conn) 
{
    echo "Connection failed!"; 
    die("Error connecting to database");
}
?>