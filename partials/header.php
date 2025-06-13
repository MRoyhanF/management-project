<?php
require_once '../config/db.php'; // Sesuaikan path jika header dipanggil dari folder admin

// session_start() tidak diperlukan lagi di sini karena sudah dipanggil di index.php
if (!isset($_SESSION['role'])) {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['role'];

if ($role === "admin") {
    $navItems = [
        ['label' => 'User', 'href' => './users/'],
        ['label' => 'Proyek', 'href' => './projects/'],
    ];
} elseif ($role === "manager") {
    $navItems = [
        ['label' => 'Proyek', 'href' => './projects/'],
        ['label' => 'Tugas', 'href' => './tasks/'],
        ['label' => 'Penugasan', 'href' => './assignments/'],
    ];
} else {
    $navItems = [
        ['label' => 'Tugas ku', 'href' => './tasks/my_tasks.php'],
    ];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Management Projects</title>
    <script src="https://unpkg.com/alpinejs"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] {
            display: none;
        }
    </style>
</head>

<body class="bg-gray-200">
    <section class="relative w-full px-8 text-gray-700 bg-white body-font" data-tails-scripts="//unpkg.com/alpinejs" {!! $attributes ?? '' !!}>
        <div class="container flex flex-col flex-wrap items-center justify-between py-2 mx-auto md:flex-row max-w-7xl">
            <a href="#_" class="relative z-10 flex items-center w-auto text-2xl font-extrabold leading-none text-black select-none">tails.</a>

            <nav class="top-0 left-0 flex items-center justify-center space-x-5 text-base md:absolute">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= htmlspecialchars($item['href']) ?>"
                        class="relative font-medium leading-6 text-gray-600 transition duration-150 ease-out hover:text-gray-900"
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
                <a href="../../logout.php"
                    class="px-4 py-2 text-white bg-blue-600 border border-blue-700 rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Logout
                </a>
            </div>
        </div>
    </section>