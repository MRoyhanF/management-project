<?php
require_once '../../config/db.php';
session_start();
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) header("Location: index.php");

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

$stmt = ($role === 'manager')
    ? $conn->prepare("SELECT * FROM projects WHERE id_project = ? AND id_manager = ?")
    : $conn->prepare("SELECT * FROM projects WHERE id_project = ?");
$role === 'manager' ? $stmt->bind_param("ii", $id, $id_user) : $stmt->bind_param("i", $id);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) header("Location: index.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_project'];
    $deskripsi = $_POST['deskripsi'];
    $mulai = $_POST['tanggal_mulai'];
    $deadline = $_POST['tanggal_deadline'];

    $stmt = $conn->prepare("UPDATE projects SET nama_project=?, deskripsi=?, tanggal_mulai=?, tanggal_deadline=? WHERE id_project=?");
    $stmt->bind_param("ssssi", $nama, $deskripsi, $mulai, $deadline, $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Proyek</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Edit Proyek</h1>
    <form method="POST" class="space-y-4 max-w-xl">
        <input type="text" name="nama_project" value="<?= htmlspecialchars($data['nama_project']) ?>" class="w-full border p-2 rounded" required>
        <textarea name="deskripsi" class="w-full border p-2 rounded" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
        <input type="date" name="tanggal_mulai" value="<?= $data['tanggal_mulai'] ?>" class="w-full border p-2 rounded" required>
        <input type="date" name="tanggal_deadline" value="<?= $data['tanggal_deadline'] ?>" class="w-full border p-2 rounded" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <a href="index.php" class="text-blue-600 ml-2">Kembali</a>
    </form>
</body>
</html>
