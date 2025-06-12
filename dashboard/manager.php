<?php
require_once '../config/db.php';
session_start();
if ($_SESSION['role'] !== 'manager') {
    header('Location: ../login.php');
    exit;
}

$id_manager = $_SESSION['id_user'];

// Proyek milik manager
$projects = $conn->query("SELECT COUNT(*) as total FROM projects WHERE id_manager = $id_manager")->fetch_assoc()['total'];
$tasks = $conn->query("
    SELECT COUNT(*) as total 
    FROM tasks 
    WHERE id_project IN (SELECT id_project FROM projects WHERE id_manager = $id_manager)
")->fetch_assoc()['total'];
$assignments = $conn->query("
    SELECT COUNT(DISTINCT id_user) as total 
    FROM task_assignments 
    WHERE id_task IN (
        SELECT id_task FROM tasks 
        WHERE id_project IN (SELECT id_project FROM projects WHERE id_manager = $id_manager)
    )
")->fetch_assoc()['total'];
$progress = $conn->query("
    SELECT ROUND(AVG(progress),2) as rata 
    FROM tasks 
    WHERE id_project IN (SELECT id_project FROM projects WHERE id_manager = $id_manager)
")->fetch_assoc()['rata'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?> (Manager)</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Proyek Anda</p>
            <p class="text-xl font-bold"><?= $projects ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Total Tugas</p>
            <p class="text-xl font-bold"><?= $tasks ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Anggota Dilibatkan</p>
            <p class="text-xl font-bold"><?= $assignments ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Rata-rata Progres</p>
            <p class="text-xl font-bold"><?= $progress ?>%</p>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <a href="../logout.php" class="text-blue-600 hover:underline">Logout</a>
        <a href="./projects/" class="text-blue-600 hover:underline">Kelola Proyek</a>
        <a href="./tasks/" class="text-blue-600 hover:underline">Kelola Tugas</a>
        <a href="./assignments/" class="text-blue-600 hover:underline">Penugasan</a>
        <a href="../laporan/project.php" class="text-blue-600 hover:underline">Laporan Proyek</a>
    </div>
</body>

</html>