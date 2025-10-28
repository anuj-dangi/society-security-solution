<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Resident')
{
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['action']) && isset($_POST['visitor_id']))
{
    $visitor_id = intval($_POST['visitor_id']);
    $action = $_POST['action'];
    $stmt = $conn->prepare("UPDATE normal_visitor SET status=? WHERE visitor_id=?");
    $stmt->bind_param("si", $action, $visitor_id);
    $stmt->execute();
    header("Location: resident.php?msg=Visitor%20status%20updated%20successfully");
    exit();
}

$username = $_SESSION['username'];
$resident = $conn->query("SELECT * FROM resident_details WHERE username='$username'")->fetch_assoc();
$flat_id = $resident['flat_id'];

$flat_query = $conn->query("
    SELECT f.flat_no, b.building_name 
    FROM flat_details f
    JOIN building_table b ON f.building_id = b.building_id
    WHERE f.flat_id = '$flat_id'
");
$flat_info = $flat_query->fetch_assoc();

$flat_residents = $conn->query("SELECT * FROM resident_details WHERE flat_id='$flat_id' ORDER BY resident_id ASC");
$owner = $flat_residents->fetch_assoc();
$members = [];

while ($r = $flat_residents->fetch_assoc())
{
    $members[] = $r;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Resident Dashboard</title>
    <link rel="stylesheet" href="../../css/style_resident.css">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>Resident Dashboard</h1>
        <a href="../../logout.php" class="logout-btn">Logout</a>
    </div>

    <h2 class="section-title">Your Flat Details</h2>
    <div class="add-form">
        <p><strong>Building Name:</strong> <?= htmlspecialchars($flat_info['building_name'] ?? 'N/A') ?></p>
        <p><strong>Flat Number:</strong> <?= htmlspecialchars($flat_info['flat_no'] ?? 'N/A') ?></p>
        <p><strong>Owner:</strong> <?= htmlspecialchars($owner['name'] ?? 'N/A') ?></p>
        <p><strong>Members:</strong>
            <?php
            if (!empty($members))
            {
                foreach ($members as $m)
                {
                    echo htmlspecialchars($m['name']) . ", ";
                }
            }
            else
            {
                echo "No additional members.";
            }
            ?>
        </p>
    </div>

    <?php if (isset($_GET['msg'])) { ?>
        <p style="background:#d4edda;color:#155724;padding:10px;border-radius:6px;margin-bottom:20px;">
            <?= htmlspecialchars($_GET['msg']) ?>
        </p>
    <?php } ?>

    <h2 class="section-title">Visitor Records</h2>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Purpose</th>
                    <th>Status</th>
                    <th>Visit Time</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="visitorTableBody">
                <tr><td colspan="7">Loading visitors...</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script>
async function loadVisitors()
{
    try
    {
        const res = await fetch("fetch_visitors.php");
        if (!res.ok)
        {
            throw new Error("Failed to fetch visitors");
        }
        const html = await res.text();
        document.getElementById("visitorTableBody").innerHTML = html;
    }
    catch (err)
    {
        console.error(err);
    }
}

loadVisitors();
setInterval(loadVisitors, 2000);
</script>

</body>
</html>
