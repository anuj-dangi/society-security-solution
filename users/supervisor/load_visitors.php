<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor')
{
    http_response_code(403);
    exit("Unauthorized");
}

$pending = $conn->query("
    SELECT * FROM normal_visitor
    WHERE status='Pending'
    ORDER BY visit_time DESC
");

$history = $conn->query("
    SELECT * FROM normal_visitor
    WHERE status!='Pending'
    ORDER BY visit_time DESC
");
?>

<div id="pendingVisitors">
    <?php
    if ($pending->num_rows > 0)
    {
        ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Purpose</th>
                    <th>Building</th>
                    <th>Flat</th>
                    <th>Status</th>
                    <th>Visit Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($v = $pending->fetch_assoc())
                {
                    ?>
                    <tr>
                        <td><?= $v['visitor_id'] ?></td>
                        <td><?= htmlspecialchars($v['name']) ?></td>
                        <td><?= htmlspecialchars($v['phone_no']) ?></td>
                        <td><?= htmlspecialchars($v['purpose']) ?></td>
                        <td><?= htmlspecialchars($v['building_name']) ?></td>
                        <td><?= htmlspecialchars($v['flat_no']) ?></td>
                        <td><?= htmlspecialchars($v['status']) ?></td>
                        <td><?= htmlspecialchars($v['visit_time']) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    else
    {
        ?>
        <p>No pending visitors.</p>
        <?php
    }
    ?>
</div>

<div id="visitorHistory">
    <?php
    if ($history->num_rows > 0)
    {
        ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Purpose</th>
                    <th>Building</th>
                    <th>Flat</th>
                    <th>Status</th>
                    <th>Visit Time</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($v = $history->fetch_assoc())
                {
                    ?>
                    <tr>
                        <td><?= $v['visitor_id'] ?></td>
                        <td><?= htmlspecialchars($v['name']) ?></td>
                        <td><?= htmlspecialchars($v['phone_no']) ?></td>
                        <td><?= htmlspecialchars($v['purpose']) ?></td>
                        <td><?= htmlspecialchars($v['building_name']) ?></td>
                        <td><?= htmlspecialchars($v['flat_no']) ?></td>
                        <td><?= htmlspecialchars($v['status']) ?></td>
                        <td><?= htmlspecialchars($v['visit_time']) ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    else
    {
        ?>
        <p>No visitor history available.</p>
        <?php
    }
    ?>
</div>
