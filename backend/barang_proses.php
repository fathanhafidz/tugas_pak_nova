<?php
include '../config/koneksi.php';
include '../config/base_url.php';

// Tambah barang
if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $id_kategori = !empty($_POST['id_kategori']) ? $_POST['id_kategori'] : "NULL";
    $id_supplier = !empty($_POST['id_supplier']) ? $_POST['id_supplier'] : "NULL";
    $harga_jual = !empty($_POST['harga_jual']) ? $_POST['harga_jual'] : "NULL";

    $query = "INSERT INTO barang (nama_barang, id_kategori, id_supplier, stok, harga_jual, status) 
              VALUES ('$nama', $id_kategori, $id_supplier, 0, $harga_jual, 'aktif')";
    mysqli_query($koneksi, $query);

    header("Location: $base_url/pages/barang.php");
    exit;
}

// Edit barang
if (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id = $_POST['id_barang'];
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_barang']);
    $id_kategori = !empty($_POST['id_kategori']) ? $_POST['id_kategori'] : "NULL";
    $id_supplier = !empty($_POST['id_supplier']) ? $_POST['id_supplier'] : "NULL";
    $harga_jual = !empty($_POST['harga_jual']) ? $_POST['harga_jual'] : "NULL";

    $query = "UPDATE barang SET 
                nama_barang='$nama',
                id_kategori=$id_kategori,
                id_supplier=$id_supplier,
                harga_jual=$harga_jual
              WHERE id_barang='$id'";
    mysqli_query($koneksi, $query);

    header("Location: $base_url/pages/barang.php");
    exit;
}

// Nonaktifkan barang (dengan validasi relasi)
if (isset($_GET['aksi']) && $_GET['aksi'] == 'nonaktif') {
    $id = $_GET['id'];

    // cek apakah barang sudah dipakai di transaksi
    $cek_masuk = mysqli_query($koneksi, "SELECT 1 FROM barang_masuk WHERE id_barang='$id' LIMIT 1");
    $cek_keluar = mysqli_query($koneksi, "SELECT 1 FROM barang_keluar_detail WHERE id_barang='$id' LIMIT 1");

    if (mysqli_num_rows($cek_masuk) > 0 || mysqli_num_rows($cek_keluar) > 0) {
        // kalau dipakai → gagal nonaktif
        header("Location: $base_url/pages/barang.php?pesan=gagal_nonaktif");
        exit;
    }

    mysqli_query($koneksi, "UPDATE barang SET status='nonaktif' WHERE id_barang='$id'");
    header("Location: $base_url/pages/barang.php");
    exit;
}


// Aktifkan kembali barang
if (isset($_GET['aksi']) && $_GET['aksi'] == 'aktif') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "UPDATE barang SET status='aktif' WHERE id_barang='$id'");
    header("Location: $base_url/pages/barang.php");
    exit;
}
