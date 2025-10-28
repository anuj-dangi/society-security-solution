<?php
require('../../utils/db_conn.php');
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'Supervisor')
{
    header("Location: ../../index.php");
    exit();
}

$buildings = $conn->query("SELECT building_id, building_name FROM building_table ORDER BY building_name");
$flats = $conn->query("SELECT flat_id, building_id, flat_no FROM flat_details ORDER BY flat_no");

$residents = $conn->query("
    SELECT rd.resident_id, rd.name, rd.phone_no, rd.email, rd.flat_id, fd.flat_no, fd.building_id, b.building_name
    FROM resident_details rd
    JOIN flat_details fd ON rd.flat_id = fd.flat_id
    JOIN building_table b ON fd.building_id = b.building_id
    ORDER BY rd.name
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supervisor - Flat & Resident Lookup</title>
    <link rel="stylesheet" href="../../css/style_manage_visitors.css">
</head>
<body>
<div class="dashboard-container">
    <div class="header">
        <h1>Flat & Resident Lookup</h1>
        <div>
            <a href="./supervisor.php" class="back-btn">← Back</a>
            <a href="../../logout.php" class="logout-btn">Logout</a>
        </div>
    </div>

    <h2 class="section-title">Select Building & Flat</h2>
    <div class="add-form">
        <div class="form-row">
            <select id="building_select">
                <option value="">Select Building</option>
                <?php while($b = $buildings->fetch_assoc()) { ?>
                    <option value="<?= $b['building_id'] ?>"><?= htmlspecialchars($b['building_name']) ?></option>
                <?php } ?>
            </select>

            <select id="flat_select">
                <option value="">Select Flat</option>
                <?php 
                $flats->data_seek(0);
                while($f = $flats->fetch_assoc())
                { ?>
                    <option value="<?= $f['flat_id'] ?>" data-building="<?= $f['building_id'] ?>">
                        <?= htmlspecialchars($f['flat_no']) ?>
                    </option>
                <?php } ?>
            </select>
        </div>
    </div>

    <h2 class="section-title">Residents</h2>
    <div class="table-container">
        <table id="resident_table">
            <thead>
                <tr>
                    <th>Resident ID</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Building</th>
                    <th>Flat No</th>
                </tr>
            </thead>
            <tbody>
                <?php while($r = $residents->fetch_assoc())
                { ?>
                    <tr data-building="<?= $r['building_id'] ?>" data-flat="<?= $r['flat_id'] ?>">
                        <td><?= $r['resident_id'] ?></td>
                        <td><?= htmlspecialchars($r['name']) ?></td>
                        <td><?= htmlspecialchars($r['phone_no']) ?></td>
                        <td><?= htmlspecialchars($r['email']) ?></td>
                        <td><?= htmlspecialchars($r['building_name']) ?></td>
                        <td><?= htmlspecialchars($r['flat_no']) ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const buildingSelect = document.getElementById('building_select');
const flatSelect = document.getElementById('flat_select');
const residentRows = document.querySelectorAll('#resident_table tbody tr');
const flatOptions = flatSelect.querySelectorAll('option');

buildingSelect.addEventListener('change', () =>
{
    const selectedBuilding = buildingSelect.value;
    flatSelect.value = "";

    flatOptions.forEach(opt =>
    {
        if (opt.value === "") return;
        opt.style.display = (!selectedBuilding || opt.dataset.building === selectedBuilding) ? "block" : "none";
    });

    filterResidents();
});

flatSelect.addEventListener('change', filterResidents);

function filterResidents()
{
    const selectedBuilding = buildingSelect.value;
    const selectedFlat = flatSelect.value;

    residentRows.forEach(row =>
    {
        const rowBuilding = row.dataset.building;
        const rowFlat = row.dataset.flat;

        if ((selectedBuilding === "" || rowBuilding === selectedBuilding) &&
            (selectedFlat === "" || rowFlat === selectedFlat))
        {
            row.style.display = "";
        }
        else
        {
            row.style.display = "none";
        }
    });
}

filterResidents();
</script>
</body>
</html>
