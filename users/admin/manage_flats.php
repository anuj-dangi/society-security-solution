<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') 
{
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['add_flat'])) 
{
    $building_id = (int)$_POST['building_id'];
    $flat_no = mysqli_real_escape_string($conn, $_POST['flat_no']);
    $flat_area = (float)$_POST['flat_area'];
    $ownership_status = $_POST['ownership_status'];

    $check = mysqli_query($conn, "SELECT * FROM flat_details WHERE flat_no = '$flat_no' LIMIT 1");
    if (mysqli_num_rows($check) > 0) 
    {
        $error = "⚠️ Flat number '$flat_no' already exists. Please use a different one.";
    } 
    else 
    {
        $insert = "INSERT INTO flat_details (building_id, flat_no, flat_area, ownership_status)
                   VALUES ('$building_id', '$flat_no', '$flat_area', '$ownership_status')";
        if (mysqli_query($conn, $insert)) 
        {
            header("Location: manage_flats.php");
            exit();
        } 
        else 
        {
            $error = "❌ Unable to add flat. Please try again.";
        }
    }
}

if (isset($_GET['delete_id'])) 
{
    $id = (int)$_GET['delete_id'];
    if ($id != 1 && $id != 2) 
    {
        $delete = "DELETE FROM flat_details WHERE flat_id='$id'";
        if (mysqli_query($conn, $delete)) 
        {
            header("Location: manage_flats.php");
            exit();
        } 
        else 
        {
            $error = "Error deleting flat: " . mysqli_error($conn);
        }
    } 
    else 
    {
        $error = "You cannot delete admin or supervisor flats.";
    }
}

if (isset($_POST['update_flat'])) 
{
    $id = (int)$_POST['flat_id'];
    $building_id = (int)$_POST['building_id'];
    $flat_no = mysqli_real_escape_string($conn, $_POST['flat_no']);
    $flat_area = (float)$_POST['flat_area'];
    $ownership_status = $_POST['ownership_status'];

    $update = "UPDATE flat_details 
               SET building_id='$building_id', flat_no='$flat_no', flat_area='$flat_area', ownership_status='$ownership_status'
               WHERE flat_id='$id'";
    if (mysqli_query($conn, $update)) 
    {
        header("Location: manage_flats.php");
        exit();
    } 
    else 
    {
        $error = "Error updating flat: " . mysqli_error($conn);
    }
}

$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;

function get_owner($conn, $flat_id) 
{
    $res = mysqli_query($conn, "SELECT name FROM resident_details WHERE flat_id='$flat_id' ORDER BY resident_id ASC LIMIT 1");
    if ($r = mysqli_fetch_assoc($res)) 
    {
        return $r['name'];
    }
    return '';
}

function get_floor($flat_no) 
{
    if (preg_match('/\d{3,}/', $flat_no, $matches)) 
    {
        $num = intval($matches[0]);
        return intval(floor(($num % 1000) / 100));
    }
    return '-';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Flats</title>
    <link rel="stylesheet" href="../../css/style_manage_flats.css">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>Manage Flats</h1>
        <a href="admin.php" class="back-btn">← Back</a>
    </div>

    <div class="add-building">
        <h2>Add New Flat</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post" action="">
            <select name="building_id" required>
                <option value="">Select Building</option>
                <?php
                $bldgs = mysqli_query($conn, "SELECT * FROM building_table ORDER BY building_name ASC");
                while ($b = mysqli_fetch_assoc($bldgs)) 
                {
                    echo "<option value='{$b['building_id']}'>{$b['building_name']}</option>";
                }
                ?>
            </select>
            <input type="text" name="flat_no" placeholder="Flat Number (e.g., S-101)" required>
            <input type="number" name="flat_area" placeholder="Flat Area (sq ft)" required>
            <select name="ownership_status" required>
                <option value="">Select Ownership</option>
                <option value="Owner">Owner</option>
                <option value="Tenant">Tenant</option>
            </select>
            <button type="submit" name="add_flat">Add</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Building</th>
                <th>Flat No</th>
                <th>Floor</th>
                <th>Flat Area</th>
                <th>Owner</th>
                <th>Ownership</th>
                <th>Actions</th>
            </tr>
            <?php
            $query = "SELECT f.*, b.building_name FROM flat_details f 
                      LEFT JOIN building_table b ON f.building_id = b.building_id
                      WHERE f.flat_id NOT IN (1,2)
                      ORDER BY f.flat_id ASC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0) 
            {
                while ($row = mysqli_fetch_assoc($result)) 
                {
                    $floor = get_floor($row['flat_no']);
                    $owner = get_owner($conn, $row['flat_id']);

                    if ($edit_id === (int)$row['flat_id']) 
                    {
                        echo "<tr>
                            <form method='post' action=''>
                            <td>{$row['flat_id']}<input type='hidden' name='flat_id' value='{$row['flat_id']}'></td>
                            <td>
                                <select name='building_id' required>";
                                    $bldgs2 = mysqli_query($conn, "SELECT * FROM building_table ORDER BY building_name ASC");
                                    while ($b = mysqli_fetch_assoc($bldgs2)) 
                                    {
                                        $sel = ($b['building_id'] == $row['building_id']) ? 'selected' : '';
                                        echo "<option value='{$b['building_id']}' $sel>{$b['building_name']}</option>";
                                    }
                        echo "</select></td>
                            <td><input type='text' name='flat_no' value='".htmlspecialchars($row['flat_no'])."' required></td>
                            <td>{$floor}</td>
                            <td><input type='number' name='flat_area' value='".htmlspecialchars($row['flat_area'])."' required></td>
                            <td>{$owner}</td>
                            <td>
                                <select name='ownership_status' required>
                                    <option value='Owner' ".($row['ownership_status']=='Owner'?'selected':'').">Owner</option>
                                    <option value='Tenant' ".($row['ownership_status']=='Tenant'?'selected':'').">Tenant</option>
                                </select>
                            </td>
                            <td>
                                <button type='submit' name='update_flat'>Update</button> | 
                                <a href='manage_flats.php'>Cancel</a>
                            </td>
                            </form>
                        </tr>";
                    } 
                    else 
                    {
                        echo "<tr>
                            <td>{$row['flat_id']}</td>
                            <td>{$row['building_name']}</td>
                            <td>{$row['flat_no']}</td>
                            <td>{$floor}</td>
                            <td>{$row['flat_area']}</td>
                            <td>{$owner}</td>
                            <td>{$row['ownership_status']}</td>
                            <td>
                                <a href='manage_flats.php?edit_id={$row['flat_id']}'>Edit</a> | 
                                <a href='manage_flats.php?delete_id={$row['flat_id']}' onclick=\"return confirm('Are you sure you want to delete this flat?')\">Delete</a>
                            </td>
                        </tr>";
                    }
                }
            } 
            else 
            {
                echo "<tr><td colspan='8'>No flats found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>
