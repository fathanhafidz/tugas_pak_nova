<?php
session_start();
include '../config/koneksi.php';

// ✅ Tambah barang masuk
if (isset($_POST['id_barang']) && !isset($_POST['edit']) && !isset($_POST['hapus'])) {
    $id_barang   = $_POST['id_barang'];
    $jumlah      = $_POST['jumlah'];
    $harga_beli  = $_POST['harga_beli_satuan'];
    $keterangan  = $_POST['keterangan'];

    $tanggal = date("Y-m-d");
    $waktu   = date("H:i:s");

    // cek status barang
    $cek = $koneksi->prepare("SELECT status FROM barang WHERE id_barang=?");
    $cek->bind_param("i", $id_barang);
    $cek->execute();
    $res = $cek->get_result()->fetch_assoc();
    if (!$res || $res['status'] !== 'aktif') {
        $_SESSION['error'] = "Barang tidak aktif!";
        header("Location: ../pages/barang_masuk.php");
        exit;
    }

    // insert barang masuk
    $stmt = $koneksi->prepare("INSERT INTO barang_masuk 
        (id_barang, tanggal_masuk, waktu_masuk, jumlah, harga_beli_satuan, jumlah_sisa, keterangan) 
        VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiiis", $id_barang, $tanggal, $waktu, $jumlah, $harga_beli, $jumlah, $keterangan);
    $stmt->execute();

    // update stok
    $stmt2 = $koneksi->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang=?");
    $stmt2->bind_param("ii", $jumlah, $id_barang);
    $stmt2->execute();

    header("Location: ../pages/barang_masuk.php");
    exit;
}

// ✅ Edit barang masuk
if (isset($_POST['edit'])) {
    $id_masuk    = $_POST['id_masuk'];
    $jumlah      = $_POST['jumlah'];
    $harga_beli  = $_POST['harga_beli_satuan'];
    $keterangan  = $_POST['keterangan'];

    // ambil data lama
    $cek = $koneksi->prepare("SELECT id_barang, jumlah FROM barang_masuk WHERE id_masuk=?");
    $cek->bind_param("i", $id_masuk);
    $cek->execute();
    $lama = $cek->get_result()->fetch_assoc();

    if ($lama) {
        $selisih = $jumlah - $lama['jumlah'];

        // update barang_masuk
        $stmt = $koneksi->prepare("UPDATE barang_masuk 
            SET jumlah=?, harga_beli_satuan=?, jumlah_sisa=?, keterangan=? 
            WHERE id_masuk=?");
        $stmt->bind_param("iiisi", $jumlah, $harga_beli, $jumlah, $keterangan, $id_masuk);
        $stmt->execute();

        // update stok barang
        $stmt2 = $koneksi->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang=?");
        $stmt2->bind_param("ii", $selisih, $lama['id_barang']);
        $stmt2->execute();
    }

    header("Location: ../pages/barang_masuk.php");
    exit;
}

// ✅ Hapus barang masuk
if (isset($_POST['hapus'])) {
    $id_masuk = $_POST['id_masuk'];

    // ambil data
    $cek = $koneksi->prepare("SELECT id_barang, jumlah FROM barang_masuk WHERE id_masuk=?");
    $cek->bind_param("i", $id_masuk);
    $cek->execute();
    $lama = $cek->get_result()->fetch_assoc();

    if ($lama) {
        // kurangi stok
        $stmt2 = $koneksi->prepare("UPDATE barang SET stok = stok - ? WHERE id_barang=?");
        $stmt2->bind_param("ii", $lama['jumlah'], $lama['id_barang']);
        $stmt2->execute();

        // hapus data
        $stmt = $koneksi->prepare("DELETE FROM barang_masuk WHERE id_masuk=?");
        $stmt->bind_param("i", $id_masuk);
        $stmt->execute();
    }

    header("Location: ../pages/barang_masuk.php");
    exit;
}
