<?php
session_start();

unset($_SESSION['resident_id']);
unset($_SESSION['username']);

session_destroy(); 

header("Location: index.php");
exit(); 
?>