<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Resident')
{
    http_response_code(403);
    exit("Unauthorized");
}

$username = $_SESSION['username'];
$resident = $conn->query("SELECT * FROM resident_details WHERE username='$username'")->fetch_assoc();
$flat_id = $resident['flat_id'];

$visitors = $conn->query("
    SELECT * FROM normal_visitor 
    WHERE flat_no=(SELECT flat_no FROM flat_details WHERE flat_id='$flat_id') 
    ORDER BY visit_time DESC
");

if ($visitors->num_rows > 0)
{
    while ($v = $visitors->fetch_assoc())
    {
        echo "<tr>";
        echo "<td>{$v['visitor_id']}</td>";
        echo "<td>" . htmlspecialchars($v['name']) . "</td>";
        echo "<td>" . htmlspecialchars($v['phone_no']) . "</td>";
        echo "<td>" . htmlspecialchars($v['purpose']) . "</td>";
        echo "<td>" . htmlspecialchars($v['status']) . "</td>";
        echo "<td>" . htmlspecialchars($v['visit_time']) . "</td>";
        echo "<td>";
        if ($v['status'] == "Pending")
        {
            echo "<form method='POST' style='display:inline;' action='resident.php'>
                    <input type='hidden' name='visitor_id' value='{$v['visitor_id']}'>
                    <button type='submit' name='action' value='Approved' class='btn approve'>Approve</button>
                    <button type='submit' name='action' value='Denied' class='btn deny'>Deny</button>
                  </form>";
        }
        else
        {
            echo "-";
        }
        echo "</td>";
        echo "</tr>";
    }
}
else
{
    echo "<tr><td colspan='7'>No visitors found for your flat.</td></tr>";
}
?>
