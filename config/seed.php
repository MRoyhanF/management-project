<?php
require_once 'db.php';

// ==================== Seeder USERS ====================
echo "<h3>Seeder: Users</h3>";

$users = [
    ['admin', 'password', 'Admin Satu', 'admin'],
    ['manager', 'password', 'Manager Proyek', 'manager'],
    ['fulan1', 'password', 'Anggota Tim A', 'anggota'],
    ['fulan2', 'password', 'Anggota Tim B', 'anggota'],
];

foreach ($users as $user) {
    [$username, $plainPassword, $namaLengkap, $role] = $user;
    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashedPassword, $namaLengkap, $role);

    if ($stmt->execute()) {
        echo "User '$username' berhasil ditambahkan.<br>";
    } else {
        echo "Gagal menambahkan '$username': " . $stmt->error . "<br>";
    }
    $stmt->close();
}

// ==================== Seeder PROJECTS ====================
echo "<h3>Seeder: Projects</h3>";

// Ambil ID user dengan role manager
$result = $conn->query("SELECT id_user FROM users WHERE role = 'manager' LIMIT 1");
$manager = $result->fetch_assoc();
$idManager = $manager ? $manager['id_user'] : null;

if (!$idManager) {
    die("Manager belum ada di tabel users.<br>");
}

$projects = [
    ['Proyek Website', 'Pengembangan sistem manajemen proyek berbasis web.', '2025-06-01', '2025-07-31'],
    ['Proyek Mobile', 'Aplikasi mobile pendukung monitoring proyek.', '2025-06-15', '2025-08-15'],
];

$stmt = $conn->prepare("INSERT INTO projects (nama_project, deskripsi, tanggal_mulai, tanggal_deadline, id_manager) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $nama, $deskripsi, $mulai, $deadline, $idManager);

foreach ($projects as $p) {
    [$nama, $deskripsi, $mulai, $deadline] = $p;

    if ($stmt->execute()) {
        echo "Project '$nama' berhasil ditambahkan.<br>";
    } else {
        echo "Gagal menambahkan project '$nama': " . $stmt->error . "<br>";
    }
}
$stmt->close();

// ==================== Seeder TASKS ====================
echo "<h3>Seeder: Tasks</h3>";

$result = $conn->query("SELECT id_project FROM projects");
$projects = $result->fetch_all(MYSQLI_ASSOC);

$tasks = [
    ['Desain UI', 'Membuat desain UI awal.', '2025-06-10'],
    ['Setup Database', 'Menyiapkan skema database awal.', '2025-06-15'],
    ['API Backend', 'Membangun REST API untuk task management.', '2025-06-20'],
];

$stmt = $conn->prepare("INSERT INTO tasks (id_project, judul_task, deskripsi_task, deadline_task) VALUES (?, ?, ?, ?)");
$stmt->bind_param("isss", $idProject, $judul, $deskripsi, $deadline);

foreach ($projects as $p) {
    $idProject = $p['id_project'];

    foreach ($tasks as $t) {
        [$judul, $deskripsi, $deadline] = $t;

        if ($stmt->execute()) {
            echo "Task '$judul' berhasil ditambahkan untuk Project ID $idProject.<br>";
        } else {
            echo "Gagal menambahkan task '$judul': " . $stmt->error . "<br>";
        }
    }
}
$stmt->close();

// ==================== Seeder TASK ASSIGNMENTS ====================
echo "<h3>Seeder: Task Assignments</h3>";

$taskResult = $conn->query("SELECT id_task FROM tasks");
$tasks = $taskResult->fetch_all(MYSQLI_ASSOC);

$userResult = $conn->query("SELECT id_user FROM users WHERE role = 'anggota'");
$anggota = $userResult->fetch_all(MYSQLI_ASSOC);

if (empty($tasks) || empty($anggota)) {
    die("Tasks atau Anggota kosong!<br>");
}

$stmt = $conn->prepare("INSERT INTO task_assignments (id_task, id_user, tanggal_ditugaskan) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $idTask, $idUser, $tanggalDitugaskan);

foreach ($tasks as $index => $task) {
    $idTask = $task['id_task'];
    $idUser = $anggota[$index % count($anggota)]['id_user']; // Rotasi antar anggota
    $tanggalDitugaskan = date('Y-m-d');

    if ($stmt->execute()) {
        echo "Task ID $idTask berhasil ditugaskan ke User ID $idUser.<br>";
    } else {
        echo "Gagal menugaskan task ID $idTask: " . $stmt->error . "<br>";
    }
}

$stmt->close();
$conn->close();

echo "<h3>✅ Seeder selesai dijalankan.</h3>";
