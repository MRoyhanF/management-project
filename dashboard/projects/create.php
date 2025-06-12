<?php
require_once '../../config/db.php';
session_start();
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama_project'];
    $deskripsi = $_POST['deskripsi'];
    $mulai = $_POST['tanggal_mulai'];
    $deadline = $_POST['tanggal_deadline'];
    $id_manager = ($_SESSION['role'] === 'manager') ? $_SESSION['id_user'] : $_POST['id_manager'];

    if ($nama && $deskripsi && $mulai && $deadline && $id_manager) {
        $stmt = $conn->prepare("INSERT INTO projects (nama_project, deskripsi, tanggal_mulai, tanggal_deadline, id_manager) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $nama, $deskripsi, $mulai, $deadline, $id_manager);
        $stmt->execute();
        header("Location: index.php");
        exit;
    } else {
        $error = "Semua field wajib diisi.";
    }
}

$managers = $conn->query("SELECT id_user, nama_lengkap FROM users WHERE role = 'manager'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Proyek</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Tambah Proyek</h1>
    <?php if ($error): ?><div class="text-red-600 mb-4"><?= htmlspecialchars($error) ?></div><?php endif ?>
    <form method="POST" class="space-y-4 max-w-xl">
        <input type="text" name="nama_project" placeholder="Nama Proyek" class="w-full border p-2 rounded" required>
        <textarea name="deskripsi" placeholder="Deskripsi" class="w-full border p-2 rounded" required></textarea>
        <input type="date" name="tanggal_mulai" class="w-full border p-2 rounded" required>
        <input type="date" name="tanggal_deadline" class="w-full border p-2 rounded" required>

        <?php if ($_SESSION['role'] === 'admin'): ?>
        <select name="id_manager" class="w-full border p-2 rounded" required>
            <option value="">-- Pilih Manager --</option>
            <?php while ($m = $managers->fetch_assoc()): ?>
            <option value="<?= $m['id_user'] ?>"><?= htmlspecialchars($m['nama_lengkap']) ?></option>
            <?php endwhile ?>
        </select>
        <?php endif; ?>

        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
        <a href="index.php" class="text-blue-600 ml-2">Kembali</a>
    </form>
</body>
</html>
