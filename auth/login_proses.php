<?php
session_start();
include '../config/koneksi.php';

$username = trim($_POST['username']);
$password = trim($_POST['password']);

$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        // simpan session
        $_SESSION['id_users'] = $user['id_users'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['level']    = $user['level'];

        // catat log aktivitas
        $aktivitas = "Login ke sistem";
        $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas) VALUES (?, ?)");
        $stmtLog->bind_param("is", $user['id_users'], $aktivitas);
        $stmtLog->execute();

        header("Location: ../pages/dashboard.php");
        exit;
    } else {
        header("Location: ../pages/login.php?pesan=gaal");
        exit;
    }
} else {
    header("Location: ../pages/login.php?pesan=gagal");
    exit;
}
