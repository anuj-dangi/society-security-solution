<?php
require('../../utils/db_conn.php');
session_start();

// Allow only supervisor
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor') 
{
    header("Location: ../../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor Dashboard - Society Security System</title>
    <link rel="stylesheet" type="text/css" href="../../css/style_supervisor.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Supervisor Dashboard</h1>
            <a href="../../logout.php" class="logout-btn">Logout</a>
        </div>

        <div class="card-container">
            <div class="card">
                <h3>Manage Visitors</h3>
                <p>Add, manage and history of visitors</p>
                <a href="manage_visitors.php">Open</a>
            </div>

            <div class="card">
                <h3>Manage Regular Vendors</h3>
                <p>Maintain records of frequent service vendors</p>
                <a href="regular_vendors.php">Open</a>
            </div>

            <div class="card">
                <h3>Flat & Resident Lookup</h3>
                <p>Quickly find resident and flat details</p>
                <a href="flat_lookup.php">Open</a>
            </div>

            <div class="card">
                <h3>Maintenance Status</h3>
                <p>Check and update maintenance payment info</p>
                <a href="maintenance_status.php">Open</a>
            </div>

            <div class="card">
                <h3>Society Staff Management</h3>
                <p>View and manage staff details and schedules</p>
                <a href="staff_details.php">Open</a>
            </div>
        </div>
    </div>
</body>
</html>
