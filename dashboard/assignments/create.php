<?php
require_once '../../config/db.php';
session_start();

if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Ambil tugas yang ada
$tasks = $conn->query("SELECT id_task, judul_task FROM tasks");

// Ambil user anggota
$users = $conn->query("SELECT id_user, nama_lengkap FROM users WHERE role = 'anggota'");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_task = $_POST['id_task'];
    $id_user = $_POST['id_user'];
    $tanggal = $_POST['tanggal_ditugaskan'];

    if ($id_task && $id_user && $tanggal) {
        $stmt = $conn->prepare("INSERT INTO task_assignments (id_task, id_user, tanggal_ditugaskan) VALUES (?, ?, ?)");
        $stmt->bind_param("iis", $id_task, $id_user, $tanggal);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $error = "Semua kolom wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Penugasan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6">
    <h1 class="text-xl font-bold mb-4">Tambah Penugasan Tugas</h1>
    <?php if ($error): ?><div class="text-red-600 mb-4"><?= $error ?></div><?php endif ?>
    <form method="POST" class="space-y-4 max-w-xl">
        <select name="id_task" class="w-full border p-2 rounded" required>
            <option value="">-- Pilih Tugas --</option>
            <?php while ($task = $tasks->fetch_assoc()): ?>
                <option value="<?= $task['id_task'] ?>"><?= htmlspecialchars($task['judul_task']) ?></option>
            <?php endwhile ?>
        </select>

        <select name="id_user" class="w-full border p-2 rounded" required>
            <option value="">-- Pilih Anggota --</option>
            <?php while ($user = $users->fetch_assoc()): ?>
                <option value="<?= $user['id_user'] ?>"><?= htmlspecialchars($user['nama_lengkap']) ?></option>
            <?php endwhile ?>
        </select>

        <input type="date" name="tanggal_ditugaskan" class="w-full border p-2 rounded" required>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
</body>
</html>
