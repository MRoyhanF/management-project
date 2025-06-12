<?php
$host = 'localhost';
$user = 'root';
$password = ''; 
$database = 'manajemen_proyek';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}
?>
