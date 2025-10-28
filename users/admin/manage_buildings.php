<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') 
{
    header("Location: ../../index.php");
    exit();
}

if(isset($_POST['add_building'])) 
{
    $name = mysqli_real_escape_string($conn, $_POST['building_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $total_flats = (int)$_POST['total_flats'];

    $check = mysqli_query($conn, "SELECT * FROM building_table WHERE building_name = '$name' LIMIT 1");
    if(mysqli_num_rows($check) > 0) 
    {
        $error = "⚠️ Building '$name' already exists. Please choose a different name.";
    } 
    else 
    {
        $insert = "INSERT INTO building_table (building_name, address, total_flats) 
                   VALUES ('$name', '$address', '$total_flats')";
        if(mysqli_query($conn, $insert)) 
        {
            header("Location: manage_buildings.php");
            exit();
        } 
        else 
        {
            $error = "❌ Unable to add building. Please try again.";
        }
    }
}

if(isset($_GET['delete_id'])) 
{
    $id = (int)$_GET['delete_id'];
    $delete = "DELETE FROM building_table WHERE building_id='$id'";

    if(mysqli_query($conn, $delete)) 
    {
        header("Location: manage_buildings.php");
        exit();
    } 
    else 
    {
        $error = "Error deleting building: " . mysqli_error($conn);
    }
}

if(isset($_POST['update_building'])) 
{
    $id = (int)$_POST['building_id'];
    $name = mysqli_real_escape_string($conn, $_POST['building_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $total_flats = (int)$_POST['total_flats'];

    $update = "UPDATE building_table SET building_name='$name', address='$address', total_flats='$total_flats' WHERE building_id='$id'";
    if(mysqli_query($conn, $update)) 
    {
        header("Location: manage_buildings.php");
        exit();
    } 
    else 
    {
        $error = "Error updating building: " . mysqli_error($conn);
    }
}

$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Buildings</title>
    <link rel="stylesheet" href="../../css/style_manage_buildings.css">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>Manage Buildings</h1>
        <a href="admin.php" class="back-btn">← Back</a>
    </div>

    <div class="add-building">
        <h2>Add New Building</h2>
        <?php if(isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="">
            <input type="text" name="building_name" placeholder="Building Name" required>
            <input type="text" name="address" placeholder="Block of Building" required>
            <input type="number" name="total_flats" placeholder="No of flats" required>
            <button type="submit" name="add_building">Add</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Building Name</th>
                <th>Address</th>
                <th>Total Flats</th>
                <th>Actions</th>
            </tr>
            <?php
            $query = "SELECT * FROM building_table ORDER BY building_id ASC";
            $result = mysqli_query($conn, $query);
            if(mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) 
                {
                    if($edit_id === (int)$row['building_id']) 
                    {
                        echo "<tr>
                                <form method='post' action=''>
                                <td>{$row['building_id']}<input type='hidden' name='building_id' value='{$row['building_id']}'></td>
                                <td><input type='text' name='building_name' value='".htmlspecialchars($row['building_name'])."' required></td>
                                <td><input type='text' name='address' value='".htmlspecialchars($row['address'])."' required></td>
                                <td><input type='number' name='total_flats' value='".htmlspecialchars($row['total_flats'])."' required></td>
                                <td>
                                    <button type='submit' name='update_building'>Update</button> | 
                                    <a href='manage_buildings.php'>Cancel</a>
                                </td>
                                </form>
                              </tr>";
                    } 
                    else 
                    {
                        echo "<tr>
                                <td>{$row['building_id']}</td>
                                <td>{$row['building_name']}</td>
                                <td>{$row['address']}</td>
                                <td>{$row['total_flats']}</td>
                                <td>
                                    <a href='manage_buildings.php?edit_id={$row['building_id']}'>Edit</a> | 
                                    <a href='manage_buildings.php?delete_id={$row['building_id']}' onclick=\"return confirm('Are you sure you want to delete this building?')\">Delete</a>
                                </td>
                              </tr>";
                    }
                }
            } 
            else 
            {
                echo "<tr><td colspan='5'>No buildings found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>
