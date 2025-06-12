<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'anggota') {
    header('Location: ../login.php');
    exit;
}

require_once '../../config/db.php';

$id_user = $_SESSION['id_user'];

// Handle update progress
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_progress'])) {
    $progress = max(0, min(100, intval($_POST['progress'])));
    $id_task = intval($_POST['id_task']);

    // Pastikan user memang ditugaskan ke task ini
    $stmt = $conn->prepare("SELECT 1 FROM task_assignments WHERE id_task = ? AND id_user = ?");
    $stmt->bind_param("ii", $id_task, $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $update = $conn->prepare("UPDATE tasks SET progress = ?, status = 
            CASE 
                WHEN ? = 100 THEN 'selesai' 
                WHEN ? > 0 THEN 'proses' 
                ELSE 'belum mulai' 
            END
            WHERE id_task = ?");
        $update->bind_param("iiii", $progress, $progress, $progress, $id_task);
        $update->execute();
    }
}

// Ambil tugas yang ditugaskan ke user ini
$stmt = $conn->prepare("
    SELECT 
        t.id_task,
        t.judul_task, 
        t.deskripsi_task, 
        t.deadline_task, 
        t.status, 
        t.progress,
        p.nama_project
    FROM task_assignments ta
    JOIN tasks t ON ta.id_task = t.id_task
    JOIN projects p ON t.id_project = p.id_project
    WHERE ta.id_user = ?
");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tugas Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="p-8">
    <h1 class="text-xl font-bold mb-4">Tugas Saya</h1>
    <a href="./index.php" class="text-blue-600 hover:underline mb-4 inline-block">← Kembali ke Dashboard</a>

    <div class="space-y-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="p-4 border rounded-lg shadow">
                <h2 class="font-semibold text-lg"><?= htmlspecialchars($row['judul_task']) ?> (<?= htmlspecialchars($row['nama_project']) ?>)</h2>
                <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($row['deskripsi_task'])) ?></p>
                <p class="text-sm text-gray-500">Deadline: <?= htmlspecialchars($row['deadline_task']) ?></p>
                <p class="text-sm">Status: <?= htmlspecialchars($row['status']) ?></p>
                <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?= (int)$row['progress'] ?>%"></div>
                </div>
                <p class="text-sm mt-1">Progress: <?= $row['progress'] ?>%</p>
                <button onclick="openModal(<?= $row['id_task'] ?>, <?= $row['progress'] ?>)" class="mt-2 px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700">Update Progress</button>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal -->
    <div id="modal" class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center hidden">
        <div class="bg-white p-6 rounded shadow w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Update Progress</h2>
            <form method="POST">
                <input type="hidden" name="id_task" id="modal-task-id">
                <input type="hidden" name="update_progress" value="1">
                <label for="progress" class="block text-sm mb-1">Progress (%)</label>
                <input type="number" name="progress" id="modal-progress" class="w-full border rounded p-2 mb-4" min="0" max="100" required>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeModal()" class="px-3 py-1 bg-gray-300 rounded hover:bg-gray-400">Batal</button>
                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal(taskId, progress) {
            document.getElementById('modal-task-id').value = taskId;
            document.getElementById('modal-progress').value = progress;
            document.getElementById('modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('modal').classList.add('hidden');
        }
    </script>
</body>

</html>