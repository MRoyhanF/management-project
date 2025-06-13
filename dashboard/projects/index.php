<?php
require_once '../../config/db.php';
session_start();
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

if ($role === 'manager') {
    $stmt = $conn->prepare("SELECT p.*, u.nama_lengkap AS nama_manager 
                            FROM projects p 
                            JOIN users u ON p.id_manager = u.id_user 
                            WHERE p.id_manager = ?");
    $stmt->bind_param("i", $id_user);
} else {
    $stmt = $conn->prepare("SELECT p.*, u.nama_lengkap AS nama_manager 
                            FROM projects p 
                            JOIN users u ON p.id_manager = u.id_user");
}
$stmt->execute();
$projects = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Daftar Proyek</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Manajemen Proyek</h1>
    <a href="../admin.php" class="bg-green-600 hover:bg-green-800 text-white px-4 py-2 rounded mb-4 inline-block">← Kembali ke Dashboard</a>
    <?php if ($role === 'admin'): ?>
        <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Tambah Proyek</a>
    <?php endif; ?>
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">#</th>
                <th class="border p-2">Nama Proyek</th>
                <th class="border p-2">Deskripsi</th>
                <th class="border p-2">Tanggal Mulai</th>
                <th class="border p-2">Deadline</th>
                <th class="border p-2">Manager</th>
                <?php if ($role === 'admin'): ?>
                    <th class="border p-2">Aksi</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php while ($p = $projects->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?= $p['id_project'] ?></td>
                    <td class="border p-2"><?= htmlspecialchars($p['nama_project']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($p['deskripsi']) ?></td>
                    <td class="border p-2"><?= $p['tanggal_mulai'] ?></td>
                    <td class="border p-2"><?= $p['tanggal_deadline'] ?></td>
                    <td class="border p-2"><?= htmlspecialchars($p['nama_manager']) ?></td>
                    <?php if ($role === 'admin'): ?>
                        <td class="border p-2">
                            <a href="edit.php?id=<?= $p['id_project'] ?>" class="text-sm hover:text-gray-50 bg-yellow-400 hover:bg-yellow-600 px-2 py-1 rounded-lg shadow">Edit</a>
                            <a href="delete.php?id=<?= $p['id_project'] ?>" onclick="return confirm('Yakin ingin hapus?')" class="text-sm hover:text-gray-50 bg-red-400 hover:bg-red-600 px-2 py-1 rounded-lg shadow">Hapus</a>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</body>

</html>