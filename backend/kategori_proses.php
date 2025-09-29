<?php
include '../config/koneksi.php';
include '../config/base_url.php';
session_start();

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

// Tambah kategori
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama_kategori']);
    $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori) VALUES (?)");
    $stmt->bind_param("s", $nama);
    $stmt->execute();

    // log aktivitas
    $aktivitas = "Tambah kategori";
    $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel) VALUES (?, ?, ?)");
    $tabel = "kategori";
    $stmtLog->bind_param("iss", $_SESSION['id_users'], $aktivitas, $tabel);
    $stmtLog->execute();

    header("Location: $base_url/pages/kategori.php");
    exit;
}

// Edit kategori
if (isset($_POST['edit'])) {
    $id = $_POST['id_kategori'];
    $nama = trim($_POST['nama_kategori']);
    $stmt = $koneksi->prepare("UPDATE kategori SET nama_kategori=? WHERE id_kategori=?");
    $stmt->bind_param("si", $nama, $id);
    $stmt->execute();

    $aktivitas = "Edit kategori";
    $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
    $tabel = "kategori";
    $stmtLog->bind_param("issi", $_SESSION['id_users'], $aktivitas, $tabel, $id);
    $stmtLog->execute();

    header("Location: $base_url/pages/kategori.php");
    exit;
}

// Hapus kategori
if (isset($_POST['hapus'])) {
    $id = $_POST['id_kategori'];
    $stmt = $koneksi->prepare("DELETE FROM kategori WHERE id_kategori=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $aktivitas = "Hapus kategori";
    $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
    $tabel = "kategori";
    $stmtLog->bind_param("issi", $_SESSION['id_users'], $aktivitas, $tabel, $id);
    $stmtLog->execute();

    header("Location: $base_url/pages/kategori.php");
    exit;
}
