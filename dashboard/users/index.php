<?php
require_once '../../config/db.php';
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Manajemen User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Daftar User</h1>
    <a href="../admin.php" class="bg-green-600 text-white px-4 py-2 rounded mb-4 inline-block">Dashboard</a>
    <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Tambah User</a>

    <table class="w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-100 text-left">
                <th class="p-2 border">#</th>
                <th class="p-2 border">Username</th>
                <th class="p-2 border">Nama Lengkap</th>
                <th class="p-2 border">Role</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="border-t">
                    <td class="p-2 border"><?= $row['id_user'] ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($row['username']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                    <td class="p-2 border"><?= $row['role'] ?></td>
                    <td class="p-2 border">
                        <a href="edit.php?id=<?= $row['id_user'] ?>" class="text-blue-600">Edit</a> |
                        <a href="delete.php?id=<?= $row['id_user'] ?>" class="text-red-600" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>

</html>