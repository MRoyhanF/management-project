<?php
require_once '../config/db.php';
session_start();
if ($_SESSION['role'] !== 'anggota') {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

$total = $conn->query("SELECT COUNT(*) as total FROM task_assignments WHERE id_user = $id_user")->fetch_assoc()['total'];
$belum = $conn->query("
    SELECT COUNT(*) as total 
    FROM task_assignments ta
    JOIN tasks t ON t.id_task = ta.id_task
    WHERE ta.id_user = $id_user AND t.status = 'belum mulai'
")->fetch_assoc()['total'];

$proses = $conn->query("
    SELECT COUNT(*) as total 
    FROM task_assignments ta
    JOIN tasks t ON t.id_task = ta.id_task
    WHERE ta.id_user = $id_user AND t.status = 'proses'
")->fetch_assoc()['total'];

$selesai = $conn->query("
    SELECT COUNT(*) as total 
    FROM task_assignments ta
    JOIN tasks t ON t.id_task = ta.id_task
    WHERE ta.id_user = $id_user AND t.status = 'selesai'
")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Anggota</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?> (Anggota)</h1>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Total Tugas</p>
            <p class="text-xl font-bold"><?= $total ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Belum Mulai</p>
            <p class="text-xl font-bold"><?= $belum ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Sedang Proses</p>
            <p class="text-xl font-bold"><?= $proses ?></p>
        </div>
        <div class="p-4 bg-white shadow rounded border">
            <p class="text-gray-600">Selesai</p>
            <p class="text-xl font-bold"><?= $selesai ?></p>
        </div>
    </div>

    <div class="flex flex-col gap-2">
        <a href="../logout.php" class="text-blue-600 hover:underline">Logout</a>
        <a href="./tasks/my_tasks.php" class="text-blue-600 hover:underline">Lihat Tugas Saya</a>
    </div>
</body>

</html>