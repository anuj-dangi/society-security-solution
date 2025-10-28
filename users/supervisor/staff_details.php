<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor') 
{
    header("Location: ../../index.php");
    exit();
}

$staff = $conn->query("
    SELECT staff_id, name, phone_no, email, role, shift_time
    FROM staff_details
    ORDER BY name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor - Staff Details</title>
    <link rel="stylesheet" href="../../css/style_staff_details.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Society Staff Details</h1>
            <div>
                <a href="./supervisor.php" class="back-btn">← Back</a>
                <a href="../../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <h2 class="section-title">Staff Members</h2>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Staff ID</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Shift Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($staff->num_rows > 0) 
                    {
                        while ($s = $staff->fetch_assoc()) 
                        { 
                    ?>
                            <tr>
                                <td><?= $s['staff_id'] ?></td>
                                <td><?= htmlspecialchars($s['name']) ?></td>
                                <td><?= htmlspecialchars($s['phone_no']) ?></td>
                                <td><?= htmlspecialchars($s['email']) ?></td>
                                <td><?= htmlspecialchars($s['role']) ?></td>
                                <td><?= htmlspecialchars($s['shift_time']) ?></td>
                            </tr>
                    <?php 
                        } 
                    } 
                    else 
                    { 
                    ?>
                        <tr>
                            <td colspan="6">No staff records found.</td>
                        </tr>
                    <?php 
                    } 
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
