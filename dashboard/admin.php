<?php
require_once '../config/db.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Statistik
$users = $conn->query("SELECT COUNT(*) as total FROM users")->fetch_assoc()['total'];
$projects = $conn->query("SELECT COUNT(*) as total FROM projects")->fetch_assoc()['total'];
$tasks = $conn->query("SELECT COUNT(*) as total FROM tasks")->fetch_assoc()['total'];
$assignments = $conn->query("SELECT COUNT(*) as total FROM task_assignments")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?> (Admin)</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Total Users</p>
            <p class="text-xl font-bold"><?= $users ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Total Projects</p>
            <p class="text-xl font-bold"><?= $projects ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Total Tasks</p>
            <p class="text-xl font-bold"><?= $tasks ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Total Assignments</p>
            <p class="text-xl font-bold"><?= $assignments ?></p>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <a href="../logout.php" class="text-blue-600 hover:underline">Logout</a>
        <a href="./users/" class="text-blue-600 hover:underline">Manajemen User</a>
        <a href="./projects/" class="text-blue-600 hover:underline">Manajemen Proyek</a>
        <a href="./tasks/" class="text-blue-600 hover:underline">Manajemen Tugas</a>
        <a href="./assignments/" class="text-blue-600 hover:underline">Manajemen Penugasan</a>
        <a href="../laporan/project.php" class="text-blue-600 hover:underline">Laporan Proyek</a>
    </div>
</body>

</html>