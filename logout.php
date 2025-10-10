<?php
session_start(); // Start the session to access session variables

// Unset specific session variables (optional)
unset($_SESSION['resident_id']);
unset($_SESSION['username']);

session_destroy(); // Destroy all session data

// Redirect to the login page
header("Location: login.php");
exit(); // Terminate script execution after redirection
?>