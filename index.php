<?php
session_start();

// Jika user sudah login, arahkan ke dashboard sesuai role
if (isset($_SESSION['role'])) {
    header("Location: dashboard/{$_SESSION['role']}.php");
    exit;
}

// Jika belum login, arahkan ke halaman login
header("Location: login.php");
exit;
