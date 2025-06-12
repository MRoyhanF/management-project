<?php
require_once '../../config/db.php';
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
$error = '';

if (!$id) {
    header("Location: index.php");
    exit;
}

// Ambil data
$stmt = $conn->prepare("SELECT * FROM users WHERE id_user = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nama = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];

    if ($username && $nama && $role) {
        $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=?, role=? WHERE id_user=?");
        $stmt->bind_param("sssi", $username, $nama, $role, $id);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $error = "Semua field wajib diisi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Edit User</h1>
    <?php if ($error): ?>
        <div class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4 max-w-md">
        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" class="w-full border p-2 rounded" required>
        <input type="text" name="nama_lengkap" value="<?= htmlspecialchars($user['nama_lengkap']) ?>" class="w-full border p-2 rounded" required>
        <select name="role" class="w-full border p-2 rounded" required>
            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
            <option value="anggota" <?= $user['role'] === 'anggota' ? 'selected' : '' ?>>Anggota</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Update</button>
        <a href="index.php" class="text-blue-600 ml-2">Kembali</a>
    </form>
</body>
</html>
