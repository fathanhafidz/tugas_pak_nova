<?php
include '../config/koneksi.php';
include '../config/base_url.php';

if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_supplier = $_POST['id_supplier'];
    $stok = $_POST['stok'];
    $harga_jual = $_POST['harga_jual'];

    $query = "INSERT INTO barang (nama_barang, id_kategori, id_supplier, stok, harga_jual) 
              VALUES ('$nama', '$id_kategori', '$id_supplier', '$stok', '$harga_jual')";
    mysqli_query($koneksi, $query);

    header("Location: $base_url/pages/barang.php");
    exit;
}

if (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id = $_POST['id_barang'];
    $nama = $_POST['nama_barang'];
    $id_kategori = $_POST['id_kategori'];
    $id_supplier = $_POST['id_supplier'];
    $stok = $_POST['stok'];
    $harga_jual = $_POST['harga_jual'];

    $query = "UPDATE barang SET 
                nama_barang='$nama',
                id_kategori='$id_kategori',
                id_supplier='$id_supplier',
                stok='$stok',
                harga_jual='$harga_jual'
              WHERE id_barang='$id'";
    mysqli_query($koneksi, $query);

    header("Location: $base_url/pages/barang.php");
    exit;
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM barang WHERE id_barang='$id'");
    header("Location: $base_url/pages/barang.php");
    exit;
}
