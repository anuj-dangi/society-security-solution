<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin') 
{
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['add_resident'])) 
{
    $flat_id = (int)$_POST['flat_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'Resident';

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) 
    {
        $error = "⚠️ Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and special character.";
    }
    else
    {
        $check = mysqli_query($conn, "SELECT * FROM resident_details WHERE username='$username' LIMIT 1");
        if (mysqli_num_rows($check) > 0) 
        {
            $error = "⚠️ Username '$username' already exists. Please choose a different one.";
        } 
        else 
        {
            $insert = "INSERT INTO resident_details (flat_id, name, phone_no, email, username, password, role) 
                       VALUES ('$flat_id', '$name', '$phone_no', '$email', '$username', '$password', '$role')";
            if (mysqli_query($conn, $insert)) 
            {
                header("Location: manage_residents.php");
                exit();
            } 
            else 
            {
                $error = "❌ Unable to add resident. Please try again.";
            }
        }
    }
}

if (isset($_GET['delete_id'])) 
{
    $id = (int)$_GET['delete_id'];
    $delete = "DELETE FROM resident_details WHERE resident_id='$id'";
    if (mysqli_query($conn, $delete)) 
    {
        header("Location: manage_residents.php");
        exit();
    } 
    else 
    {
        $error = "Error deleting resident: " . mysqli_error($conn);
    }
}

if (isset($_POST['update_resident'])) 
{
    $id = (int)$_POST['resident_id'];
    $flat_id = (int)$_POST['flat_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $role = 'Resident';

    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) 
    {
        $error = "⚠️ Password must be at least 8 characters long and include an uppercase letter, lowercase letter, number, and special character.";
    }
    else
    {
        $update = "UPDATE resident_details 
                   SET flat_id='$flat_id', name='$name', phone_no='$phone_no', email='$email', username='$username', password='$password', role='$role' 
                   WHERE resident_id='$id'";
        if (mysqli_query($conn, $update)) 
        {
            header("Location: manage_residents.php");
            exit();
        } 
        else 
        {
            $error = "Error updating resident: " . mysqli_error($conn);
        }
    }
}

$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Residents</title>
    <link rel="stylesheet" href="../../css/style_manage_residents.css">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>Manage Residents</h1>
        <a href="admin.php" class="back-btn">← Back</a>
    </div>

    <div class="add-building">
        <h2>Add New Resident</h2>
        <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
        <form method="post" action="">
            <select name="flat_id" required>
                <option value="">Select Flat</option>
                <?php
                $flats = mysqli_query($conn, "SELECT f.flat_id, f.flat_no, b.building_name FROM flat_details f 
                                             LEFT JOIN building_table b ON f.building_id=b.building_id 
                                             ORDER BY b.building_name, f.flat_no ASC");
                while ($f = mysqli_fetch_assoc($flats)) 
                {
                    echo "<option value='{$f['flat_id']}'>{$f['building_name']} - {$f['flat_no']}</option>";
                }
                ?>
            </select>
            <input type="text" name="name" placeholder="Resident Name" required>
            <input type="text" name="phone_no" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="text" name="password" placeholder="Password" required>
            <input type="hidden" name="role" value="Resident">
            <button type="submit" name="add_resident">Add</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Flat</th>
                <th>Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Username</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            <?php
            $query = "SELECT r.*, f.flat_no, b.building_name FROM resident_details r
                      LEFT JOIN flat_details f ON r.flat_id=f.flat_id
                      LEFT JOIN building_table b ON f.building_id=b.building_id
                      WHERE r.role='Resident'
                      ORDER BY r.resident_id ASC";
            $result = mysqli_query($conn, $query);
            if (mysqli_num_rows($result) > 0) 
            {
                while ($row = mysqli_fetch_assoc($result)) 
                {
                    $flat_label = ($row['building_name'] && $row['flat_no']) ? $row['building_name'].' - '.$row['flat_no'] : '-';

                    if ($edit_id === (int)$row['resident_id']) 
                    {
                        echo "<tr>
                                <form method='post' action=''>
                                <td>{$row['resident_id']}<input type='hidden' name='resident_id' value='{$row['resident_id']}'></td>
                                <td>
                                    <select name='flat_id' required>";
                                        $flats2 = mysqli_query($conn, "SELECT f.flat_id, f.flat_no, b.building_name FROM flat_details f 
                                                                      LEFT JOIN building_table b ON f.building_id=b.building_id 
                                                                      ORDER BY b.building_name, f.flat_no ASC");
                                        while ($f2 = mysqli_fetch_assoc($flats2)) 
                                        {
                                            $selected = ($f2['flat_id'] == $row['flat_id']) ? 'selected' : '';
                                            echo "<option value='{$f2['flat_id']}' $selected>{$f2['building_name']} - {$f2['flat_no']}</option>";
                                        }
                        echo "      </select>
                                </td>
                                <td><input type='text' name='name' value='".htmlspecialchars($row['name'])."' required></td>
                                <td><input type='text' name='phone_no' value='".htmlspecialchars($row['phone_no'])."' required></td>
                                <td><input type='email' name='email' value='".htmlspecialchars($row['email'])."' required></td>
                                <td><input type='text' name='username' value='".htmlspecialchars($row['username'])."' required></td>
                                <td><input type='text' name='password' placeholder='New Password' required></td>
                                <td><input type='hidden' name='role' value='Resident'>Resident</td>
                                <td>
                                    <button type='submit' name='update_resident'>Update</button> | 
                                    <a href='manage_residents.php'>Cancel</a>
                                </td>
                                </form>
                              </tr>";
                    } 
                    else 
                    {
                        echo "<tr>
                                <td>{$row['resident_id']}</td>
                                <td>{$flat_label}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['phone_no']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['username']}</td>
                                <td>{$row['role']}</td>
                                <td>
                                    <a href='manage_residents.php?edit_id={$row['resident_id']}'>Edit</a> | 
                                    <a href='manage_residents.php?delete_id={$row['resident_id']}' onclick=\"return confirm('Are you sure you want to delete this resident?')\">Delete</a>
                                </td>
                              </tr>";
                    }
                }
            } 
            else 
            {
                echo "<tr><td colspan='8'>No residents found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>
