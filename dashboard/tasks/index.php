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
    $stmt = $conn->prepare("SELECT t.*, p.nama_project 
                            FROM tasks t 
                            JOIN projects p ON t.id_project = p.id_project 
                            WHERE p.id_manager = ?");
    $stmt->bind_param("i", $id_user);
} else {
    $stmt = $conn->prepare("SELECT t.*, p.nama_project 
                            FROM tasks t 
                            JOIN projects p ON t.id_project = p.id_project");
}
$stmt->execute();
$tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Daftar Tugas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Daftar Tugas</h1>
    <a href="../admin.php" class="bg-green-600 hover:bg-green-800 text-white px-4 py-2 rounded mb-4 inline-block">← Kembali ke Dashboard</a>
    <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Tambah Tugas</a>
    <table class="w-full border">
        <thead>
            <tr class="bg-gray-100">
                <th class="border p-2">#</th>
                <th class="border p-2">Judul</th>
                <th class="border p-2">Project</th>
                <th class="border p-2">Deadline</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Progress</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($t = $tasks->fetch_assoc()): ?>
                <tr>
                    <td class="border p-2"><?= $t['id_task'] ?></td>
                    <td class="border p-2"><?= htmlspecialchars($t['judul_task']) ?></td>
                    <td class="border p-2"><?= htmlspecialchars($t['nama_project']) ?></td>
                    <td class="border p-2"><?= $t['deadline_task'] ?></td>
                    <td class="border p-2"><?= $t['status'] ?></td>
                    <td class="border p-2"><?= $t['progress'] ?>%</td>
                    <td class="border p-2">
                        <a href="edit.php?id=<?= $t['id_task'] ?>" class="text-sm hover:text-gray-50 bg-yellow-400 hover:bg-yellow-600 px-2 py-1 rounded-lg shadow">Edit</a>
                        <a href="delete.php?id=<?= $t['id_task'] ?>" onclick="return confirm('Hapus?')" class="text-sm hover:text-gray-50 bg-red-400 hover:bg-red-600 px-2 py-1 rounded-lg shadow">Hapus</a>
                    </td>
                </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</body>

</html>