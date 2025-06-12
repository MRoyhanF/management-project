<?php
require_once '../../config/db.php';
session_start();

if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../login.php");
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: index.php");
    exit;
}

$error = '';

// Ambil data tugas
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id_task = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    echo "Tugas tidak ditemukan";
    exit;
}

// Ambil data project
$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

if ($role === 'manager') {
    $projects = $conn->prepare("SELECT * FROM projects WHERE id_manager = ?");
    $projects->bind_param("i", $id_user);
    $projects->execute();
    $resultProject = $projects->get_result();
} else {
    $resultProject = $conn->query("SELECT * FROM projects");
}

// Jika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_project = $_POST['id_project'];
    $judul = $_POST['judul_task'];
    $deskripsi = $_POST['deskripsi_task'];
    $deadline = $_POST['deadline_task'];
    $status = $_POST['status'];
    $progress = intval($_POST['progress']);

    if ($id_project && $judul && $deadline && $status !== '' && $progress >= 0) {
        $stmt = $conn->prepare("UPDATE tasks SET id_project = ?, judul_task = ?, deskripsi_task = ?, deadline_task = ?, status = ?, progress = ? WHERE id_task = ?");
        $stmt->bind_param("issssii", $id_project, $judul, $deskripsi, $deadline, $status, $progress, $id);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $error = "Harap lengkapi semua kolom!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-xl font-bold mb-4">Edit Tugas</h1>
    <?php if ($error): ?><div class="text-red-600 mb-4"><?= $error ?></div><?php endif ?>
    <form method="POST" class="space-y-4 max-w-xl">
        <select name="id_project" class="w-full border p-2 rounded" required>
            <option value="">-- Pilih Proyek --</option>
            <?php while ($p = $resultProject->fetch_assoc()): ?>
            <option value="<?= $p['id_project'] ?>" <?= $p['id_project'] == $task['id_project'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nama_project']) ?>
            </option>
            <?php endwhile ?>
        </select>
        <input type="text" name="judul_task" value="<?= htmlspecialchars($task['judul_task']) ?>" class="w-full border p-2 rounded" required>
        <textarea name="deskripsi_task" class="w-full border p-2 rounded"><?= htmlspecialchars($task['deskripsi_task']) ?></textarea>
        <input type="date" name="deadline_task" value="<?= $task['deadline_task'] ?>" class="w-full border p-2 rounded" required>
        <select name="status" class="w-full border p-2 rounded" required>
            <option value="belum mulai" <?= $task['status'] == 'belum mulai' ? 'selected' : '' ?>>Belum Mulai</option>
            <option value="proses" <?= $task['status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
            <option value="selesai" <?= $task['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
        </select>
        <input type="number" name="progress" min="0" max="100" value="<?= $task['progress'] ?>" class="w-full border p-2 rounded" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan Perubahan</button>
    </form>
</body>
</html>
