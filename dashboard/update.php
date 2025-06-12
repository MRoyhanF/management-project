<?php
require_once '../config/db.php';
session_start();

if ($_SESSION['role'] !== 'anggota') {
    header("Location: ../auth/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$id_task = $_GET['id'] ?? null;

if (!$id_task) {
    header("Location: anggota.php");
    exit;
}

// Cek apakah task ini milik user yang login
$stmt = $conn->prepare("
SELECT t.judul_task, t.status, t.progress
FROM task_assignments ta
JOIN tasks t ON ta.id_task = t.id_task
WHERE ta.id_user = ? AND ta.id_task = ?
LIMIT 1
");
$stmt->bind_param("ii", $id_user, $id_task);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

if (!$task) {
    echo "Tugas tidak ditemukan atau bukan milik Anda.";
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'];
    $progress = (int)$_POST['progress'];

    if ($progress < 0 || $progress > 100) {
        $error = "Progress harus antara 0–100.";
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET status = ?, progress = ? WHERE id_task = ?");
        $stmt->bind_param("sii", $status, $progress, $id_task);
        $stmt->execute();
        header("Location: anggota.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Progres</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-6">
    <h1 class="text-xl font-bold mb-4">Update Tugas: <?= htmlspecialchars($task['judul_task']) ?></h1>

    <?php if ($error): ?>
    <div class="text-red-600 mb-4"><?= $error ?></div>
    <?php endif ?>

    <form method="POST" class="space-y-4 max-w-md">
        <label class="block">
            Status:
            <select name="status" class="w-full p-2 border rounded">
                <option value="belum mulai" <?= $task['status'] == 'belum mulai' ? 'selected' : '' ?>>Belum Mulai</option>
                <option value="proses" <?= $task['status'] == 'proses' ? 'selected' : '' ?>>Proses</option>
                <option value="selesai" <?= $task['status'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
            </select>
        </label>

        <label class="block">
            Progress (%):
            <input type="number" name="progress" min="0" max="100" class="w-full p-2 border rounded" value="<?= $task['progress'] ?>" required>
        </label>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        <a href="anggota.php" class="ml-4 text-gray-600">Kembali</a>
    </form>
</body>
</html>
