<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor')
{
    header("Location: ../../index.php");
    exit();
}

function safe_html($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$flats = $conn->query("
    SELECT f.flat_id, f.flat_no, b.building_name
    FROM flat_details f
    JOIN building_table b ON f.building_id = b.building_id
    ORDER BY b.building_name, f.flat_no
");

if (isset($_POST['add_maintenance']))
{
    $flat_id = (int)$_POST['flat_id'];
    $amount = (float)$_POST['amount'];
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $due_status = mysqli_real_escape_string($conn, $_POST['due_status']);
    $payment_date = !empty($_POST['payment_date'])
        ? "'" . mysqli_real_escape_string($conn, $_POST['payment_date']) . "'"
        : "NULL";
    $payment_mode = !empty($_POST['payment_mode'])
        ? "'" . mysqli_real_escape_string($conn, $_POST['payment_mode']) . "'"
        : "NULL";

    $conn->query("
        INSERT INTO maintenance_details (flat_id, amount, description, due_status, payment_date, payment_mode)
        VALUES ($flat_id, $amount, '$description', '$due_status', $payment_date, $payment_mode)
    ");
}

if (isset($_POST['update_status']))
{
    $maintenance_id = (int)$_POST['maintenance_id'];
    $new_status = mysqli_real_escape_string($conn, $_POST['new_status']);
    $conn->query("
        UPDATE maintenance_details 
        SET due_status='$new_status' 
        WHERE maintenance_id=$maintenance_id
    ");
}

$maintenance = $conn->query("
    SELECT m.*, f.flat_no, b.building_name
    FROM maintenance_details m
    JOIN flat_details f ON m.flat_id = f.flat_id
    JOIN building_table b ON f.building_id = b.building_id
    ORDER BY m.maintenance_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Maintenance Status - Supervisor</title>
    <link rel="stylesheet" href="../../css/style_maintenance_status.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Maintenance Status</h1>
            <div>
                <a href="supervisor.php" class="back-btn">← Back</a>
                <a href="../../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <h2 class="section-title">Add Maintenance Record</h2>
        <form method="POST" class="add-form">
            <div class="form-row">
                <select name="flat_id" required>
                    <option value="">Select Flat</option>
                    <?php
                    while ($f = $flats->fetch_assoc())
                    {
                        ?>
                        <option value="<?= $f['flat_id'] ?>">
                            <?= safe_html($f['building_name'] . ' - Flat ' . $f['flat_no']) ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>

                <input type="number" step="0.01" name="amount" placeholder="Amount (₹)" required>
                <select name="due_status" required>
                    <option value="Due">Due</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>

            <div class="form-row">
                <input type="date" name="payment_date" placeholder="Payment Date">
                <select name="payment_mode">
                    <option value="">Payment Mode</option>
                    <option value="Cash">Cash</option>
                    <option value="Online">Online</option>
                    <option value="Cheque">Cheque</option>
                </select>
            </div>

            <div class="form-row">
                <input type="text" name="description" placeholder="Description (optional)">
            </div>

            <button type="submit" name="add_maintenance" class="btn add-btn">
                Add Record
            </button>
        </form>

        <h2 class="section-title">Maintenance Records</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Building</th>
                        <th>Flat</th>
                        <th>Amount (₹)</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Payment Date</th>
                        <th>Mode</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    while ($m = $maintenance->fetch_assoc())
                    {
                        ?>
                        <tr>
                            <td><?= $m['maintenance_id'] ?></td>
                            <td><?= safe_html($m['building_name']) ?></td>
                            <td><?= safe_html($m['flat_no']) ?></td>
                            <td><?= safe_html($m['amount']) ?></td>
                            <td><?= safe_html($m['description']) ?></td>
                            <td>
                                <?php
                                if ($m['due_status'] == 'Paid')
                                {
                                    ?>
                                    <span style="color:green;font-weight:bold;">Paid</span>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <span style="color:red;font-weight:bold;">Due</span>
                                    <?php
                                }
                                ?>
                            </td>
                            <td><?= safe_html($m['payment_date']) ?></td>
                            <td><?= safe_html($m['payment_mode']) ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="maintenance_id" value="<?= $m['maintenance_id'] ?>">
                                    <?php
                                    if ($m['due_status'] == 'Paid')
                                    {
                                        ?>
                                        <input type="hidden" name="new_status" value="Due">
                                        <button type="submit" name="update_status" class="checkout-btn">
                                            Mark Due
                                        </button>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <input type="hidden" name="new_status" value="Paid">
                                        <button type="submit" name="update_status" class="approve">
                                            Mark Paid
                                        </button>
                                        <?php
                                    }
                                    ?>
                                </form>
                            </td>
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
