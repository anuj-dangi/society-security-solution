<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor') 
{
    header("Location: ../../index.php");
    exit();
}

if (isset($_POST['add_visitor'])) 
{
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone_no']);
    $purpose = mysqli_real_escape_string($conn, $_POST['purpose']);
    $building_id = $_POST['building_id'];
    $flat_no = mysqli_real_escape_string($conn, $_POST['flat_no']);

    if (empty($building_id) && !empty($flat_no)) 
    {
        $res = $conn->query("SELECT building_id FROM flat_details WHERE flat_no = '$flat_no' LIMIT 1");
        if ($res && $res->num_rows > 0) 
        {
            $row = $res->fetch_assoc();
            $building_id = $row['building_id'];
        }
    }

    if ($building_id) 
    {
        $buildingRes = $conn->query("SELECT building_name FROM building_table WHERE building_id = $building_id");
        $building = $buildingRes->fetch_assoc();
        $building_name = $building['building_name'];
    } 
    else 
    {
        $building_name = '';
    }

    $visit_time = date('Y-m-d H:i:s');

    $insertQuery = "
        INSERT INTO normal_visitor (name, phone_no, purpose, building_name, flat_no, status, visit_time)
        VALUES (?, ?, ?, ?, ?, 'Pending', ?)
    ";

    $stmt = $conn->prepare($insertQuery);
    $stmt->bind_param("ssssss", $name, $phone, $purpose, $building_name, $flat_no, $visit_time);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor - Manage Visitors</title>
    <link rel="stylesheet" href="../../css/style_manage_visitors.css">

    <style>
        .table-container 
        {
            transition: opacity 0.3s ease-in-out;
        }

        .fade-in 
        {
            animation: fadeIn 0.4s ease-in;
        }

        @keyframes fadeIn 
        {
            from 
            { 
                opacity: 0; 
                transform: scale(0.98); 
            }

            to 
            { 
                opacity: 1; 
                transform: scale(1); 
            }
        }
    </style>
</head>

<body>
    <div class="dashboard-container">
        <div class="header">
            <h1>Visitor Management</h1>
            <div>
                <a href="./supervisor.php" 
                   class="back-btn" 
                   style="background-color:#3498db;color:#fff;padding:10px 20px;
                          border-radius:8px;text-decoration:none;font-weight:bold;margin-left:10px;">
                    ← Back
                </a>

                <a href="../../logout.php" class="logout-btn">Logout</a>
            </div>
        </div>

        <h2 class="section-title">Add New Visitor</h2>

        <form method="POST" class="add-form">
            <div class="form-row">
                <input type="text" name="name" placeholder="Visitor Name" required>
                <input type="text" name="phone_no" placeholder="Phone Number" required pattern="[0-9]{10}">
            </div>

            <div class="form-row">
                <input type="text" name="purpose" placeholder="Purpose" required>

                <select name="building_id" id="building_id">
                    <option value="">Select Building (Optional)</option>
                    <?php
                        $buildings = $conn->query("SELECT building_id, building_name FROM building_table ORDER BY building_name");
                        while ($building = $buildings->fetch_assoc()) 
                        {
                            ?>
                            <option value="<?= $building['building_id'] ?>">
                                <?= htmlspecialchars($building['building_name']) ?>
                            </option>
                            <?php
                        }
                    ?>
                </select>

                <select name="flat_no" id="flat_no" required>
                    <option value="">Select Flat</option>
                    <?php
                        $flats = $conn->query("SELECT flat_no, building_id FROM flat_details ORDER BY flat_no");
                        while ($flat = $flats->fetch_assoc()) 
                        {
                            ?>
                            <option value="<?= htmlspecialchars($flat['flat_no']) ?>" 
                                    data-building="<?= $flat['building_id'] ?>">
                                <?= htmlspecialchars($flat['flat_no']) ?>
                            </option>
                            <?php
                        }
                    ?>
                </select>
            </div>

            <button type="submit" name="add_visitor" class="btn add-btn">
                Add Visitor
            </button>
        </form>

        <h2 class="section-title">Pending Visitors</h2>
        <div class="table-container" id="pendingVisitors"></div>

        <h2 class="section-title">Visitor History</h2>
        <div class="table-container" id="visitorHistory"></div>
    </div>

    <script>
        const buildingSelect = document.getElementById('building_id');
        const flatSelect = document.getElementById('flat_no');
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

        async function loadVisitors() 
        {
            try 
            {
                const response = await fetch("load_visitors.php");
                const html = await response.text();

                const tempDiv = document.createElement("div");
                tempDiv.innerHTML = html;

                const newPending = tempDiv.querySelector("#pendingVisitors")?.innerHTML?.trim();
                const newHistory = tempDiv.querySelector("#visitorHistory")?.innerHTML?.trim();

                const pendingDiv = document.getElementById("pendingVisitors");
                const historyDiv = document.getElementById("visitorHistory");

                if (newPending && newPending !== pendingDiv.innerHTML.trim()) 
                {
                    pendingDiv.style.opacity = "0.6";
                    setTimeout(() => 
                    {
                        pendingDiv.innerHTML = newPending;
                        pendingDiv.style.opacity = "1";
                        pendingDiv.classList.add("fade-in");
                    }, 150);
                }

                if (newHistory && newHistory !== historyDiv.innerHTML.trim()) 
                {
                    historyDiv.style.opacity = "0.6";
                    setTimeout(() => 
                    {
                        historyDiv.innerHTML = newHistory;
                        historyDiv.style.opacity = "1";
                        historyDiv.classList.add("fade-in");
                    }, 150);
                }

                setTimeout(() => 
                {
                    pendingDiv.classList.remove("fade-in");
                    historyDiv.classList.remove("fade-in");
                }, 700);
            } 
            catch (err) 
            {
                console.error("Error loading visitors:", err);
            }
        }

        loadVisitors();
        setInterval(loadVisitors, 5000);
    </script>
</body>
</html>
