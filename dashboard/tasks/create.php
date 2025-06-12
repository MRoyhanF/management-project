<?php
require_once '../../config/db.php';
session_start();
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_project = $_POST['id_project'];
    $judul = $_POST['judul_task'];
    $deskripsi = $_POST['deskripsi_task'];
    $deadline = $_POST['deadline_task'];
    $status = $_POST['status'];
    $progress = intval($_POST['progress']);

    if ($id_project && $judul && $deadline && $status !== '' && $progress >= 0) {
        $stmt = $conn->prepare("INSERT INTO tasks (id_project, judul_task, deskripsi_task, deadline_task, status, progress) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issssi", $id_project, $judul, $deskripsi, $deadline, $status, $progress);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $error = "Harap lengkapi semua kolom!";
    }
}

if ($role === 'manager') {
    $projects = $conn->prepare("SELECT * FROM projects WHERE id_manager = ?");
    $projects->bind_param("i", $id_user);
    $projects->execute();
    $result = $projects->get_result();
} else {
    $result = $conn->query("SELECT * FROM projects");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Tambah Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-xl font-bold mb-4">Tambah Tugas</h1>
    <?php if ($error): ?><div class="text-red-600 mb-4"><?= $error ?></div><?php endif ?>
    <form method="POST" class="space-y-4 max-w-xl">
        <select name="id_project" class="w-full border p-2 rounded" required>
            <option value="">-- Pilih Proyek --</option>
            <?php while ($p = $result->fetch_assoc()): ?>
                <option value="<?= $p['id_project'] ?>"><?= htmlspecialchars($p['nama_project']) ?></option>
            <?php endwhile ?>
        </select>
        <input type="text" name="judul_task" placeholder="Judul Tugas" class="w-full border p-2 rounded" required>
        <textarea name="deskripsi_task" placeholder="Deskripsi" class="w-full border p-2 rounded"></textarea>
        <input type="date" name="deadline_task" class="w-full border p-2 rounded" required>
        <select name="status" class="w-full border p-2 rounded" required>
            <option value="belum mulai">Belum Mulai</option>
            <option value="proses">Proses</option>
            <option value="selesai">Selesai</option>
        </select>
        <input type="number" name="progress" min="0" max="100" class="w-full border p-2 rounded" placeholder="Progress (0–100)" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
    </form>
</body>

</html>