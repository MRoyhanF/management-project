<?php
require_once '../../config/db.php';
session_start();
if (!in_array($_SESSION['role'], ['admin', 'manager'])) {
    header('Location: ../login.php');
    exit;
}

$id = $_GET['id'] ?? null;
$role = $_SESSION['role'];
$id_user = $_SESSION['id_user'];

if ($id) {
    if ($role === 'manager') {
        $stmt = $conn->prepare("DELETE FROM projects WHERE id_project = ? AND id_manager = ?");
        $stmt->bind_param("ii", $id, $id_user);
    } else {
        $stmt = $conn->prepare("DELETE FROM projects WHERE id_project = ?");
        $stmt->bind_param("i", $id);
    }
    $stmt->execute();
}
header("Location: index.php");
exit;
