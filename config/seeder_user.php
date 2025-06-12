<?php
require_once 'db.php';

$users = [
    ['admin', 'password', 'Admin Satu', 'admin'],
    ['manager', 'password', 'Manager Proyek', 'manager'],
    ['anggota1', 'password', 'Anggota Tim A', 'anggota'],
    ['anggota2', 'password', 'Anggota Tim B', 'anggota'],
];

foreach ($users as $user) {
    [$username, $plainPassword, $namaLengkap, $role] = $user;

    $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $hashedPassword, $namaLengkap, $role);

    if ($stmt->execute()) {
        echo "User '$username' berhasil ditambahkan.<br>";
    } else {
        echo "Gagal menambahkan '$username': " . $stmt->error . "<br>";
    }

    $stmt->close();
}

$conn->close();

// runing seeeder by url
// http://localhost/project/config/seeder_users.php
// by terminal
// php seeder_users.php
