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
    ['label' => 'Proyek', 'href' => './projects/'],
    ['label' => 'Tugas', 'href' => './tasks/'],
    ['label' => 'Penugasan', 'href' => './assignments/'],
];
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Projects</title>
    <style>
        [x-cloak] {
            display: none
        }
    </style>
    <script src="https://unpkg.com/alpinejs"></script>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-200">
    <!-- Navbar Section -->
    <section class="relative w-full px-8 text-gray-700 bg-white body-font" data-tails-scripts="//unpkg.com/alpinejs" {!! $attributes ?? '' !!}>
        <div class="container flex flex-col flex-wrap items-center justify-between py-2 mx-auto md:flex-row max-w-7xl">
            <a href="#_" class="relative z-10 flex items-center w-auto text-2xl font-extrabold leading-none text-black select-none">TaskApp</a>
            <nav class="top-0 left-0 z-0 flex items-center justify-center w-full h-full py-5 -ml-0 space-x-5 text-base md:-ml-5 md:py-0 md:absolute">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>" class="relative font-medium leading-6 text-gray-600 transition duration-150 ease-out hover:text-gray-900"
                        x-data="{ hover: false }" @mouseenter="hover = true" @mouseleave="hover = false">
                        <span class="block"><?= htmlspecialchars($item['label']) ?></span>
                        <span class="absolute bottom-0 left-0 inline-block w-full h-0.5 -mb-1 overflow-hidden">
                            <span x-show="hover" class="absolute inset-0 inline-block w-full h-1 h-full transform bg-gray-900"
                                x-transition:enter="transition ease duration-200"
                                x-transition:enter-start="scale-0"
                                x-transition:enter-end="scale-100"
                                x-transition:leave="transition ease-out duration-300"
                                x-transition:leave-start="scale-100"
                                x-transition:leave-end="scale-0">
                            </span>
                        </span>
                    </a>
                <?php endforeach; ?>
            </nav>
            <div class="relative z-10 inline-flex items-center space-x-3 md:ml-5 lg:justify-end">
                <span class="inline-flex rounded-md shadow-sm">
                    <a href="../logout.php" class="inline-flex items-center justify-center px-4 py-2 text-base font-medium leading-6 text-white whitespace-no-wrap bg-blue-600 border border-blue-700 rounded-md shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" data-rounded="rounded-md" data-primary="blue-600">
                        logOut
                    </a>
                </span>
            </div>
        </div>
    </section>
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
</body>

</html>