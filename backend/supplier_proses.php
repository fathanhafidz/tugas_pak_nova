<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

// Tambah supplier
if (isset($_POST['tambah'])) {
    $nama = trim($_POST['nama_supplier']);
    $telepon = trim($_POST['telepon']);
    $alamat = trim($_POST['alamat']);

    $stmt = $koneksi->prepare("INSERT INTO supplier (nama_supplier, telepon, alamat, status) VALUES (?, ?, ?, 'aktif')");
    $stmt->bind_param("sss", $nama, $telepon, $alamat);
    $stmt->execute();

    // catat ke activity log
    $id_users = $_SESSION['id_users'] ?? null;
    $id_data = $koneksi->insert_id;
    $aktivitas = "Tambah supplier";
    $tabel = "supplier";
    $log = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
    $log->bind_param("issi", $id_users, $aktivitas, $tabel, $id_data);
    $log->execute();

    header("Location: $base_url/pages/supplier.php");
    exit;
}

// Edit supplier
if (isset($_POST['edit'])) {
    $id = $_POST['id_supplier'];
    $nama = trim($_POST['nama_supplier']);
    $telepon = trim($_POST['telepon']);
    $alamat = trim($_POST['alamat']);

    $stmt = $koneksi->prepare("UPDATE supplier SET nama_supplier=?, telepon=?, alamat=? WHERE id_supplier=?");
    $stmt->bind_param("sssi", $nama, $telepon, $alamat, $id);
    $stmt->execute();

    // catat ke activity log
    $id_users = $_SESSION['id_users'] ?? null;
    $aktivitas = "Edit supplier";
    $tabel = "supplier";
    $log = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
    $log->bind_param("issi", $id_users, $aktivitas, $tabel, $id);
    $log->execute();

    header("Location: $base_url/pages/supplier.php");
    exit;
}

// Ubah status supplier
if (isset($_POST['ubah_status'])) {
    $id = $_POST['id_supplier'];
    $status_baru = $_POST['status'];

    // cek apakah supplier dipakai
    $cek = $koneksi->prepare("
        SELECT 
            (SELECT COUNT(*) FROM barang WHERE id_supplier=?) +
            (SELECT COUNT(*) FROM barang WHERE id_supplier=?) AS jumlah
    ");
    $cek->bind_param("ii", $id, $id);
    $cek->execute();
    $cek->bind_result($jumlah);
    $cek->fetch();
    $cek->close();

    if ($status_baru == "nonaktif" && $jumlah > 0) {
        $_SESSION['error'] = "Supplier sedang dipakai transaksi, tidak bisa dinonaktifkan.";
    } else {
        $stmt = $koneksi->prepare("UPDATE supplier SET status=? WHERE id_supplier=?");
        $stmt->bind_param("si", $status_baru, $id);
        $stmt->execute();

        // catat ke activity log
        $id_users = $_SESSION['id_users'] ?? null;
        $aktivitas = "Ubah status supplier ($status_baru)";
        $tabel = "supplier";
        $log = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
        $log->bind_param("issi", $id_users, $aktivitas, $tabel, $id);
        $log->execute();
    }

    header("Location: $base_url/pages/supplier.php");
    exit;
}
