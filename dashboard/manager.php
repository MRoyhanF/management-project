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

$navItems = [
    ['label' => 'Proyek ku', 'href' => './projects/'],
    ['label' => 'Tugas', 'href' => './tasks/'],
    ['label' => 'Penugasan', 'href' => './assignments/'],
];

// Ambil tugas-tugas terbaru milik manager
$tugas_terbaru = $conn->query("
    SELECT t.*, p.nama_project 
    FROM tasks t
    JOIN projects p ON t.id_project = p.id_project
    WHERE p.id_manager = $id_manager
    ORDER BY t.deadline_task ASC
    LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Management Projects</title>
    <script src="https://unpkg.com/alpinejs"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-200">
    <!-- Navbar -->
    <section class="relative w-full px-8 text-gray-700 bg-white body-font">
        <div class="container flex flex-col flex-wrap items-center justify-between py-2 mx-auto md:flex-row max-w-7xl">
            <a href="#_" class="text-2xl font-extrabold text-black">TaskApp</a>
            <nav class="flex items-center justify-center space-x-5 text-base">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>" class="font-medium text-gray-600 hover:text-gray-900">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <a href="../logout.php" class="px-4 py-2 text-white bg-blue-600 rounded hover:bg-blue-700">Logout</a>
        </div>
    </section>

    <!-- Dashboard -->
    <div class="w-full max-w-7xl mx-auto px-6 mt-6">
        <h1 class="text-2xl font-bold mb-4">Selamat datang, <?= htmlspecialchars($_SESSION['nama_lengkap']) ?> (Manager)</h1>

        <!-- Statistik -->
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

        <!-- Card Tugas Terbaru -->
        <h2 class="text-xl font-semibold mb-3">Tugas Terbaru</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php while ($row = $tugas_terbaru->fetch_assoc()): ?>
                <div class="bg-white p-4 rounded shadow border flex flex-col justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-blue-700"><?= htmlspecialchars($row['judul_task']) ?></h3>
                        <p class="text-sm text-gray-500 mb-2"><?= htmlspecialchars($row['nama_project']) ?></p>
                        <p class="text-sm text-gray-500 mb-1">Deadline: <?= htmlspecialchars($row['deadline_task']) ?></p>
                        <p class="text-sm text-gray-600 mb-2">Status: <span class="font-medium"><?= htmlspecialchars($row['status']) ?></span></p>
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                            <div class="bg-blue-600 h-3 rounded-full" style="width: <?= (int)$row['progress'] ?>%"></div>
                        </div>
                    </div>
                    <?php if (!empty($row['file'])): ?>
                        <a href="./../uploads/tasks/<?= htmlspecialchars($row['file']) ?>" target="_blank" class="text-blue-600 hover:underline mt-2 text-sm">Lihat File</a>
                    <?php else: ?>
                        <span class="text-gray-400 text-sm italic mt-2">Tidak ada file</span>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
        <div class="w-auto mx-32 mt-6 p-6 bg-white rounded shadow border">
            <h2 class="text-xl font-semibold mb-4">Statistik Visual</h2>
            <canvas id="dashboardChart" class="w-full h-64"></canvas>
        </div>
    </div>
    <!-- chart section -->

    <script>
        const ctx = document.getElementById('dashboardChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Proyek', 'Tugas', 'Anggota', 'Rata-rata Progres'],
                datasets: [{
                    label: 'Statistik Manager',
                    data: [
                        <?= json_encode((int) $projects) ?>,
                        <?= json_encode((int) $tasks) ?>,
                        <?= json_encode((int) $assignments) ?>,
                        <?= json_encode((float) $progress) ?>
                    ],
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(234, 179, 8, 0.7)',
                        'rgba(239, 68, 68, 0.7)'
                    ],
                    borderColor: [
                        'rgba(59, 130, 246, 1)',
                        'rgba(34, 197, 94, 1)',
                        'rgba(234, 179, 8, 1)',
                        'rgba(239, 68, 68, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>

</body>

</html>