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
    $stmt = $koneksi->prepare("INSERT INTO kategori (nama_kategori, status) VALUES (?, 'aktif')");
    $stmt->bind_param("s", $nama);
    $stmt->execute();

    // Ambil ID kategori yang baru ditambahkan
    $idBaru = $stmt->insert_id;

    // log aktivitas dengan id_data
    $aktivitas = "Tambah kategori";
    $tabel = "kategori";
    $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
    $stmtLog->bind_param("issi", $_SESSION['id_users'], $aktivitas, $tabel, $idBaru);
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

// Ubah status kategori
if (isset($_POST['ubah_status'])) {
    $id = $_POST['id_kategori'];
    $status_baru = $_POST['status'];

    // Cek apakah kategori dipakai di tabel lain (misalnya barang)
    $cek = $koneksi->prepare("SELECT COUNT(*) FROM barang WHERE id_kategori=?");
    $cek->bind_param("i", $id);
    $cek->execute();
    $cek->bind_result($jumlah);
    $cek->fetch();
    $cek->close();

    if ($status_baru == "nonaktif" && $jumlah > 0) {
        // Jika dipakai barang, tidak bisa dinonaktifkan
        $_SESSION['error'] = "Kategori sedang dipakai barang, tidak bisa dinonaktifkan.";
    } else {
        $stmt = $koneksi->prepare("UPDATE kategori SET status=? WHERE id_kategori=?");
        $stmt->bind_param("si", $status_baru, $id);
        $stmt->execute();

        $aktivitas = "Ubah status kategori menjadi $status_baru";
        $stmtLog = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, ?, ?)");
        $tabel = "kategori";
        $stmtLog->bind_param("issi", $_SESSION['id_users'], $aktivitas, $tabel, $id);
        $stmtLog->execute();
    }

    header("Location: $base_url/pages/kategori.php");
    exit;
}
?>
