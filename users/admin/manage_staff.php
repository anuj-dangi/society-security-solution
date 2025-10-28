<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Admin')
{
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['add_staff']))
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $shift_time = mysqli_real_escape_string($conn, $_POST['shift_time']);

    $insert = "INSERT INTO staff_details (name, role, phone_no, email, shift_time) 
               VALUES ('$name', '$role', '$phone_no', '$email', '$shift_time')";
    if (mysqli_query($conn, $insert))
    {
        header("Location: manage_staff.php");
        exit();
    }
    else
    {
        $error = "❌ Unable to add staff. Please try again.";
    }
}

if (isset($_GET['delete_id']))
{
    $id = (int)$_GET['delete_id'];
    $delete = "DELETE FROM staff_details WHERE staff_id='$id'";
    if (mysqli_query($conn, $delete))
    {
        header("Location: manage_staff.php");
        exit();
    }
    else
    {
        $error = "Error deleting staff: " . mysqli_error($conn);
    }
}

if (isset($_POST['update_staff']))
{
    $id = (int)$_POST['staff_id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone_no = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $shift_time = mysqli_real_escape_string($conn, $_POST['shift_time']);

    $update = "UPDATE staff_details SET name='$name', role='$role', phone_no='$phone_no', email='$email', shift_time='$shift_time' 
               WHERE staff_id='$id'";
    if (mysqli_query($conn, $update))
    {
        header("Location: manage_staff.php");
        exit();
    }
    else
    {
        $error = "Error updating staff: " . mysqli_error($conn);
    }
}

$edit_id = isset($_GET['edit_id']) ? (int)$_GET['edit_id'] : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff</title>
    <link rel="stylesheet" href="../../css/style_manage_staff.css">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>Manage Staff</h1>
        <a href="admin.php" class="back-btn">← Back</a>
    </div>

    <div class="add-building">
        <h2>Add New Staff</h2>
        <?php
        if (isset($error))
        {
            echo "<p class='error'>$error</p>";
        }
        ?>
        <form method="post" action="">
            <input type="text" name="name" placeholder="Staff Name" required>
            <select name="role" required>
                <option value="">Select Role</option>
                <option value="Security">Security</option>
                <option value="Cleaning">Cleaning</option>
                <option value="Gardener">Gardener</option>
                <option value="Temple">Temple</option>
                <option value="Clubhouse">Clubhouse</option>
                <option value="Other">Other</option>
            </select>
            <input type="text" name="phone_no" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="text" name="shift_time" placeholder="Shift Time" required>
            <button type="submit" name="add_staff">Add</button>
        </form>
    </div>

    <div class="table-container">
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Shift</th>
                <th>Actions</th>
            </tr>
            <?php
            $query = "SELECT * FROM staff_details ORDER BY staff_id ASC";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) > 0)
            {
                while ($row = mysqli_fetch_assoc($result))
                {
                    if ($edit_id === (int)$row['staff_id'])
                    {
                        echo "<tr>
                                <form method='post' action=''>
                                <td>{$row['staff_id']}<input type='hidden' name='staff_id' value='{$row['staff_id']}'></td>
                                <td><input type='text' name='name' value='".htmlspecialchars($row['name'])."' required></td>
                                <td>
                                    <select name='role' required>
                                        <option value='Security' ".($row['role']=='Security' ? 'selected' : '').">Security</option>
                                        <option value='Cleaning' ".($row['role']=='Cleaning' ? 'selected' : '').">Cleaning</option>
                                        <option value='Gardener' ".($row['role']=='Gardener' ? 'selected' : '').">Gardener</option>
                                        <option value='Temple' ".($row['role']=='Temple' ? 'selected' : '').">Temple</option>
                                        <option value='Clubhouse' ".($row['role']=='Clubhouse' ? 'selected' : '').">Clubhouse</option>
                                        <option value='Other' ".($row['role']=='Other' ? 'selected' : '').">Other</option>
                                    </select>
                                </td>
                                <td><input type='text' name='phone_no' value='".htmlspecialchars($row['phone_no'])."' required></td>
                                <td><input type='email' name='email' value='".htmlspecialchars($row['email'])."' required></td>
                                <td><input type='text' name='shift_time' value='".htmlspecialchars($row['shift_time'])."' required></td>
                                <td>
                                    <button type='submit' name='update_staff'>Update</button> | 
                                    <a href='manage_staff.php'>Cancel</a>
                                </td>
                                </form>
                              </tr>";
                    }
                    else
                    {
                        echo "<tr>
                                <td>{$row['staff_id']}</td>
                                <td>{$row['name']}</td>
                                <td>{$row['role']}</td>
                                <td>{$row['phone_no']}</td>
                                <td>{$row['email']}</td>
                                <td>{$row['shift_time']}</td>
                                <td>
                                    <a href='manage_staff.php?edit_id={$row['staff_id']}'>Edit</a> | 
                                    <a href='manage_staff.php?delete_id={$row['staff_id']}' onclick=\"return confirm('Are you sure you want to delete this staff?')\">Delete</a>
                                </td>
                              </tr>";
                    }
                }
            }
            else
            {
                echo "<tr><td colspan='7'>No staff found.</td></tr>";
            }
            ?>
        </table>
    </div>
</div>
</body>
</html>
