<?php

session_start();
include "./utils/db_conn.php";

if (isset($_POST['submit'])) 
{
    function validate($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    $uname = validate($_POST['username']);
    $pass = validate($_POST['password']);

    if (empty($uname)) 
    {
        header("Location: login.php?error=User Name is required");
        exit();
    }
    else if(empty($pass))
    {
        header("Location: login.php?error=Password is required");
        exit();
    }
    else
    {
        $sql = "SELECT * FROM resident_details WHERE username='$uname' AND
        password='$pass'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) === 1) 
        {
            $row = mysqli_fetch_assoc($result);
            if ($row['username'] === $uname && $row['password'] === $pass) 
            {
                echo "Logged in!";
                $_SESSION['username'] = $row['username'];
                $_SESSION['name'] = $row['name'];
                $_SESSION['resident_id'] = $row['resident_id'];

                if($row['role'] === 'Admin')
                {
                    header("Location: users/admin.php");
                    exit();
                }
                else if($row['role'] === 'Supervisor')
                {
                    header("Location: users/supervisor.php");
                    exit();
                }
                else
                {
                    header("Location: users/resident.php");
                    exit();
                }
            }
        }
        else
        {
            header("Location: login.php?error=Incorect User name or password");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>LOGIN</title>
<link rel="stylesheet" type="text/css" href="./css/style_login.css">
</head>
<body>
<form action="login.php" method="post">
<h2>LOGIN</h2>
<?php if (isset($_GET['error'])) { ?>
<p class="error"><?php echo $_GET['error']; ?></p>
<?php } ?>

<label>User Name</label>
<input type="text" name="username" placeholder="User Name"><br>
<label>Password</label>
<input type="password" name="password" placeholder="Password"><br>
<button type="submit" name="submit">Login</button>
</form>
</body>
</html>

