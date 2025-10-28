<?php
require('../../utils/db_conn.php');
session_start();


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') 
{
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Society Security System</title>
    <link rel="stylesheet" type="text/css" href="../../css/style_admin.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Admin Dashboard</h1>
            <a href="../../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Building Details</h3>
                <p>Upload and manage all building details</p>
                <a href="manage_buildings.php">Manage</a>
            </div>

            <div class="card">
                <h3>Flat Details</h3>
                <p>Upload and manage flat information</p>
                <a href="manage_flats.php">Manage</a>
            </div>

            <div class="card">
                <h3>Resident Details</h3>
                <p>View or edit resident information</p>
                <a href="manage_residents.php">Manage</a>
            </div>

            <div class="card">
                <h3>Staff Details</h3>
                <p>Security, cleaning, and maintenance staff</p>
                <a href="manage_staff.php">Manage</a>
            </div>
            
            </div>
        </div>
    </div>
</body>
</html>
