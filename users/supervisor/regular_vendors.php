<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor') 
{
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['existing_checkin'])) 
{
    $vendor_id = (int)$_POST['vendor_id'];
    $security_code = mysqli_real_escape_string($conn, $_POST['security_code']);
    $check_in = date('Y-m-d H:i:s');

    $vendorRes = $conn->query("SELECT * FROM regular_vendors WHERE vendor_id = $vendor_id ORDER BY vendor_id DESC LIMIT 1");
    if ($vendorRes->num_rows > 0) 
    {
        $vendor = $vendorRes->fetch_assoc();

        if ($vendor['security_code'] === $security_code) 
        {
            $stmt = $conn->prepare("
                INSERT INTO regular_vendors (name, work_type, security_code, flat_id, check_in)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("sssis", $vendor['name'], $vendor['work_type'], $vendor['security_code'], $vendor['flat_id'], $check_in);
            $stmt->execute();

            $msg = "✅ Vendor checked in successfully.";
        } 
        else 
        {
            $error = "❌ Incorrect security code.";
        }
    }
}

/* ───────────────────────────────
   Handle new vendor addition
──────────────────────────────── */
if (isset($_POST['add_vendor'])) 
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $work_type = mysqli_real_escape_string($conn, $_POST['work_type']);
    $security_code = mysqli_real_escape_string($conn, $_POST['security_code']);
    $building_id = $_POST['building_id'];
    $flat_id = $_POST['flat_id'];
    $check_in = date('Y-m-d H:i:s');

    $insert = $conn->prepare("
        INSERT INTO regular_vendors (name, work_type, security_code, flat_id, check_in)
        VALUES (?, ?, ?, ?, ?)
    ");
    $insert->bind_param("sssis", $name, $work_type, $security_code, $flat_id, $check_in);
    $insert->execute();

    $msg = "✅ New vendor added and checked in.";
}

/* ───────────────────────────────
   Handle vendor checkout
──────────────────────────────── */
if (isset($_POST['checkout'])) 
{
    $vendor_id = $_POST['vendor_id'];
    $checkout_time = date('Y-m-d H:i:s');

    $conn->query("UPDATE regular_vendors SET check_out='$checkout_time' WHERE vendor_id=$vendor_id");
    $msg = "✅ Vendor checked out successfully.";
}

/* ───────────────────────────────
   Fetch data
──────────────────────────────── */
$buildings = $conn->query("SELECT building_id, building_name FROM building_table ORDER BY building_name");
$flats = $conn->query("SELECT flat_id, flat_no, building_id FROM flat_details ORDER BY flat_no");
$existingVendors = $conn->query("SELECT DISTINCT name, vendor_id, work_type FROM regular_vendors ORDER BY name ASC");

$currentVendors = $conn->query("
    SELECT rv.vendor_id, rv.name, rv.work_type, rv.security_code, fd.flat_no, b.building_name, rv.check_in
    FROM regular_vendors rv
    JOIN flat_details fd ON rv.flat_id = fd.flat_id
    JOIN building_table b ON fd.building_id = b.building_id
    WHERE rv.check_out IS NULL
    ORDER BY rv.check_in DESC
");

$allVendors = $conn->query("
    SELECT rv.vendor_id, rv.name, rv.work_type, rv.security_code, fd.flat_no, b.building_name, rv.check_in, rv.check_out
    FROM regular_vendors rv
    JOIN flat_details fd ON rv.flat_id = fd.flat_id
    JOIN building_table b ON fd.building_id = b.building_id
    ORDER BY rv.check_in DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor - Manage Regular Vendors</title>
    <link rel="stylesheet" href="../../css/style_regular_vendors.css">
</head>

<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Regular Vendor Management</h1>
            <div>
                <a href="./supervisor.php" class="back-btn">← Back</a>
                <a href="../../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <?php 
            if (!empty($msg)) 
            {
                echo "<p style='color:green;font-weight:bold;'>$msg</p>";
            }

            if (!empty($error)) 
            {
                echo "<p style='color:red;font-weight:bold;'>$error</p>";
            }
        ?>

        <!-- Existing Vendor Check-In -->
        <h2 class="section-title">Check-In Existing Vendor</h2>
        <form method="POST" class="add-form">
            <div class="form-row">
                <select name="vendor_id" required>
                    <option value="">Select Vendor</option>
                    <?php 
                        while ($v = $existingVendors->fetch_assoc()) 
                        { 
                            ?>
                            <option value="<?= $v['vendor_id'] ?>">
                                <?= htmlspecialchars($v['name']) ?> (<?= htmlspecialchars($v['work_type']) ?>)
                            </option>
                            <?php 
                        } 
                    ?>
                </select>

                <input type="text" name="security_code" placeholder="Enter Security Code" required>
            </div>

            <button type="submit" name="existing_checkin" class="btn add-btn">
                Check-In Vendor
            </button>
        </form>

        <!-- Add New Vendor -->
        <h2 class="section-title">Add New Vendor</h2>
        <form method="POST" class="add-form">
            <div class="form-row">
                <input type="text" name="name" placeholder="Vendor Name" required>
                <input type="text" name="work_type" placeholder="Work Type (e.g., Cleaner, Plumber)" required>
            </div>

            <div class="form-row">
                <input type="text" name="security_code" placeholder="Security Code" required>

                <select name="building_id" id="building_id" required>
                    <option value="">Select Building</option>
                    <?php 
                        while ($b = $buildings->fetch_assoc()) 
                        { 
                            ?>
                            <option value="<?= $b['building_id'] ?>">
                                <?= htmlspecialchars($b['building_name']) ?>
                            </option>
                            <?php 
                        } 
                    ?>
                </select>

                <select name="flat_id" id="flat_id" required>
                    <option value="">Select Flat</option>
                    <?php
                        $flats->data_seek(0);
                        while ($f = $flats->fetch_assoc()) 
                        { 
                            ?>
                            <option value="<?= $f['flat_id'] ?>" data-building="<?= $f['building_id'] ?>">
                                <?= htmlspecialchars($f['flat_no']) ?>
                            </option>
                            <?php 
                        } 
                    ?>
                </select>
            </div>

            <button type="submit" name="add_vendor" class="btn add-btn">
                Add Vendor
            </button>
        </form>

        <!-- Current Vendors -->
        <h2 class="section-title">Vendors Currently Inside</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Work Type</th>
                    <th>Security Code</th>
                    <th>Building</th>
                    <th>Flat</th>
                    <th>Check-In</th>
                    <th>Action</th>
                </tr>

                <?php 
                    while ($v = $currentVendors->fetch_assoc()) 
                    { 
                        ?>
                        <tr>
                            <td><?= $v['vendor_id'] ?></td>
                            <td><?= htmlspecialchars($v['name']) ?></td>
                            <td><?= htmlspecialchars($v['work_type']) ?></td>
                            <td><?= htmlspecialchars($v['security_code']) ?></td>
                            <td><?= htmlspecialchars($v['building_name']) ?></td>
                            <td><?= htmlspecialchars($v['flat_no']) ?></td>
                            <td><?= $v['check_in'] ?></td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="vendor_id" value="<?= $v['vendor_id'] ?>">
                                    <button type="submit" name="checkout" class="btn checkout-btn">
                                        Check Out
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php 
                    } 
                ?>
            </table>
        </div>

        <!-- Vendor History -->
        <h2 class="section-title">Vendor History</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Work Type</th>
                    <th>Security Code</th>
                    <th>Building</th>
                    <th>Flat</th>
                    <th>Check-In</th>
                    <th>Check-Out</th>
                </tr>

                <?php 
                    while ($v = $allVendors->fetch_assoc()) 
                    { 
                        ?>
                        <tr>
                            <td><?= $v['vendor_id'] ?></td>
                            <td><?= htmlspecialchars($v['name']) ?></td>
                            <td><?= htmlspecialchars($v['work_type']) ?></td>
                            <td><?= htmlspecialchars($v['security_code']) ?></td>
                            <td><?= htmlspecialchars($v['building_name']) ?></td>
                            <td><?= htmlspecialchars($v['flat_no']) ?></td>
                            <td><?= $v['check_in'] ?></td>
                            <td><?= $v['check_out'] ? $v['check_out'] : '—' ?></td>
                        </tr>
                        <?php 
                    } 
                ?>
            </table>
        </div>
    </div>

    <script>
        const buildingSelect = document.getElementById('building_id');
        const flatSelect = document.getElementById('flat_id');
        const flatOptions = flatSelect.querySelectorAll('option');

        buildingSelect.addEventListener('change', function() 
        {
            const selectedBuilding = this.value;
            flatSelect.value = "";

            flatOptions.forEach(opt => 
            {
                if (opt.value === "") 
                {
                    opt.style.display = "block";
                    return;
                }

                if (!selectedBuilding || opt.dataset.building === selectedBuilding) 
                {
                    opt.style.display = "block";
                } 
                else 
                {
                    opt.style.display = "none";
                }
            });
        });
    </script>
</body>
</html>
