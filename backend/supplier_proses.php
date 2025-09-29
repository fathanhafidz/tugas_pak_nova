<?php
include '../config/koneksi.php';
include '../config/base_url.php';

if (isset($_POST['aksi']) && $_POST['aksi'] == 'tambah') {
    $nama = $_POST['nama_supplier'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];

    $query = "INSERT INTO supplier (nama_supplier, telepon, alamat) 
              VALUES ('$nama', '$telepon', '$alamat')";
    mysqli_query($koneksi, $query);

    header("Location: $base_url/pages/supplier.php");
    exit;
}

if (isset($_POST['aksi']) && $_POST['aksi'] == 'edit') {
    $id = $_POST['id_supplier'];
    $nama = $_POST['nama_supplier'];
    $telepon = $_POST['telepon'];
    $alamat = $_POST['alamat'];

    $query = "UPDATE supplier SET 
                nama_supplier='$nama',
                telepon='$telepon',
                alamat='$alamat'
              WHERE id_supplier='$id'";
    mysqli_query($koneksi, $query);

    header("Location: $base_url/pages/supplier.php");
    exit;
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM supplier WHERE id_supplier='$id'");
    header("Location: $base_url/pages/supplier.php");
    exit;
}
