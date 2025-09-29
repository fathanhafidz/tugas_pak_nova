<?php
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $level    = trim($_POST['level']);

    // hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // simpan ke database
    $sql = "INSERT INTO users (username, password, level) VALUES (?, ?, ?)";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("sss", $username, $hashedPassword, $level);

    if ($stmt->execute()) {
        header("Location: ../pages/login.php?pesan=registrasi_sukses");
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
