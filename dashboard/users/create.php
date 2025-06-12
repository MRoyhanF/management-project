<?php
require_once '../../config/db.php';
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $nama = trim($_POST['nama_lengkap']);
    $role = $_POST['role'];

    if ($username && $password && $nama && $role) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $username, $hashed, $nama, $role);
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
    <title>Tambah User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Tambah User</h1>
    <?php if ($error): ?>
        <div class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4 max-w-md">
        <input type="text" name="username" placeholder="Username" class="w-full border p-2 rounded" required>
        <input type="password" name="password" placeholder="Password" class="w-full border p-2 rounded" required>
        <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" class="w-full border p-2 rounded" required>
        <select name="role" class="w-full border p-2 rounded" required>
            <option value="">-- Pilih Role --</option>
            <option value="admin">Admin</option>
            <option value="manager">Manager</option>
            <option value="anggota">Anggota</option>
        </select>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        <a href="index.php" class="text-blue-600 ml-2">Kembali</a>
    </form>
</body>
</html>
