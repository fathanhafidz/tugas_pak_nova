<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

// catat log aktivitas sebelum session dihapus
if (isset($_SESSION['id_users'])) {
    $id_users = $_SESSION['id_users'];
    $aktivitas = "Logout dari sistem";
    $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas) VALUES (?, ?)");
    $stmtLog->bind_param("is", $id_users, $aktivitas);
    $stmtLog->execute();
}

session_unset();
session_destroy();
header("Location: ../pages/login.php");
exit;
