<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

$aksi = isset($_GET['aksi']) ? $_GET['aksi'] : '';

// Hapus semua log
if ($aksi == "hapus_semua") {
    $koneksi->query("TRUNCATE TABLE activity_log");
    header("Location: $base_url/pages/activity_log.php");
    exit;
}

// Export ke Excel
if ($aksi == "export") {
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=activity_log.xls");

    $result = $koneksi->query("SELECT log.*, u.username 
                               FROM activity_log log 
                               LEFT JOIN users u ON log.id_users = u.id_users 
                               ORDER BY log.waktu DESC");

    echo "No\tUser\tAktivitas\tTabel\tID Data\tWaktu\n";
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        echo $no++ . "\t" . $row['username'] . "\t" . $row['aktivitas'] . "\t" . 
             $row['tabel'] . "\t" . $row['id_data'] . "\t" . $row['waktu'] . "\n";
    }
    exit;
}

// Hapus log per tanggal
if (isset($_POST['hapus_per_tanggal'])) {
    $tanggal = $_POST['hapus_tanggal'];
    $stmt = $koneksi->prepare("DELETE FROM activity_log WHERE DATE(waktu) = ?");
    $stmt->bind_param("s", $tanggal);
    $stmt->execute();

    header("Location: $base_url/pages/activity_log.php");
    exit;
}

// Hapus log sebelum tanggal tertentu
if (isset($_POST['hapus_sebelum'])) {
    $tanggal = $_POST['hapus_sebelum_tanggal'];
    $stmt = $koneksi->prepare("DELETE FROM activity_log WHERE DATE(waktu) < ?");
    $stmt->bind_param("s", $tanggal);
    $stmt->execute();

    header("Location: $base_url/pages/activity_log.php");
    exit;
}
?>
