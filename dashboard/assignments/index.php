<?php
require_once '../../config/db.php';
session_start();

if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../auth/login.php");
    exit;
}

$query = "
SELECT ta.id_assignment, t.judul_task, u.nama_lengkap, ta.tanggal_ditugaskan
FROM task_assignments ta
JOIN tasks t ON ta.id_task = t.id_task
JOIN users u ON ta.id_user = u.id_user
ORDER BY ta.tanggal_ditugaskan DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>

<head>
    <title>Daftar Penugasan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-6">
    <h1 class="text-xl font-bold mb-4">Daftar Penugasan</h1>
    <!-- <a href="create.php" class="inline-block bg-green-600 text-white px-4 py-2 rounded mb-4">Tambah Penugasan</a> -->

    <a href="../admin.php" class="bg-green-600 hover:bg-green-800 text-white px-4 py-2 rounded mb-4 inline-block">← Kembali ke Dashboard</a>
    <a href="create.php" class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">+ Tambah Penugasan</a>

    <table class="table-auto w-full border">
        <thead>
            <tr class="bg-gray-200">
                <th class="p-2 border">No</th>
                <th class="p-2 border">Tugas</th>
                <th class="p-2 border">Ditugaskan ke</th>
                <th class="p-2 border">Tanggal</th>
                <th class="p-2 border">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-gray-100">
                    <td class="p-2 border"><?= $no++ ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($row['judul_task']) ?></td>
                    <td class="p-2 border"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                    <td class="p-2 border"><?= $row['tanggal_ditugaskan'] ?></td>
                    <td class="p-2 border">
                        <a href="delete.php?id=<?= $row['id_assignment'] ?>" onclick="return confirm('Hapus penugasan ini?')" class="text-sm hover:text-gray-50 bg-red-400 hover:bg-red-600 px-2 py-1 rounded-lg shadow">Hapus</a>
                    </td>
                </tr>
            <?php endwhile ?>
        </tbody>
    </table>
</body>

</html>