<?php
require_once '../config/db.php';
session_start();

if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: ../auth/login.php");
    exit;
}

// Filter
$filter_nama = $_GET['nama'] ?? '';
$filter_deadline = $_GET['deadline'] ?? '';

$query = "
SELECT
  p.id_project,
  p.nama_project,
  p.tanggal_deadline,
  u.nama_lengkap AS nama_manager,
  COUNT(t.id_task) AS total_tugas,
  COALESCE(ROUND(AVG(t.progress), 2), 0) AS rata_progress
FROM projects p
LEFT JOIN users u ON p.id_manager = u.id_user
LEFT JOIN tasks t ON p.id_project = t.id_project
WHERE u.nama_lengkap LIKE ? AND p.tanggal_deadline LIKE ?
GROUP BY p.id_project
ORDER BY p.tanggal_deadline ASC
";

$stmt = $conn->prepare($query);
$filter_nama = "%$filter_nama%";
$filter_deadline = "%$filter_deadline%";
$stmt->bind_param("ss", $filter_nama, $filter_deadline);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Laporan Proyek</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="p-6">
  <h1 class="text-2xl font-bold mb-4">Laporan Proyek</h1>

  <form method="GET" class="flex flex-wrap gap-4 mb-6">
    <input type="text" name="nama" placeholder="Cari Nama Manager" class="border p-2 rounded" value="<?= htmlspecialchars($_GET['nama'] ?? '') ?>">
    <input type="date" name="deadline" class="border p-2 rounded" value="<?= htmlspecialchars($_GET['deadline'] ?? '') ?>">
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Filter</button>
  </form>

  <?php
  $labels = [];
  $progressData = [];

  while ($row = $result->fetch_assoc()):
    $labels[] = $row['nama_project'];
    $progressData[] = $row['rata_progress'];
  ?>
    <div class="border rounded p-4 mb-4 shadow">
      <h2 class="text-xl font-semibold"><?= htmlspecialchars($row['nama_project']) ?></h2>
      <p class="text-sm text-gray-600">Manager: <?= htmlspecialchars($row['nama_manager']) ?> | Deadline: <?= $row['tanggal_deadline'] ?></p>
      <p>Total Tugas: <?= $row['total_tugas'] ?> | Rata-rata Progres: <?= $row['rata_progress'] ?>%</p>
      <div class="w-full bg-gray-200 h-3 rounded mt-2">
        <div class="bg-green-600 h-3 rounded" style="width: <?= $row['rata_progress'] ?>%"></div>
      </div>
    </div>
  <?php endwhile ?>

  <div class="mt-10">
    <h2 class="text-xl font-bold mb-2">Visualisasi Rata-rata Progres Proyek</h2>
    <canvas id="chartProyek" height="120"></canvas>
  </div>

  <script>
    const ctx = document.getElementById('chartProyek').getContext('2d');
    const chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($labels) ?>,
        datasets: [{
          label: 'Rata-rata Progress (%)',
          data: <?= json_encode($progressData) ?>,
          backgroundColor: 'rgba(34, 197, 94, 0.7)',
          borderColor: 'rgba(34, 197, 94, 1)',
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, max: 100 }
        }
      }
    });
  </script>
</body>
</html>
