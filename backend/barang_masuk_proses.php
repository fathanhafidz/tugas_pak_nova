<?php
session_start();
include '../config/koneksi.php';

$id_barang = $_POST['id_barang'];
$jumlah = $_POST['jumlah'];
$harga_beli = $_POST['harga_beli_satuan'];
$keterangan = $_POST['keterangan'];

$tanggal = date("Y-m-d");
$waktu   = date("H:i:s");

// Insert ke barang_masuk
$stmt = $koneksi->prepare("INSERT INTO barang_masuk (id_barang, tanggal_masuk, waktu_masuk, jumlah, harga_beli_satuan, jumlah_sisa, keterangan) 
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issiiis", $id_barang, $tanggal, $waktu, $jumlah, $harga_beli, $jumlah, $keterangan);
$stmt->execute();

// Update stok barang
$stmt2 = $koneksi->prepare("UPDATE barang SET stok = stok + ? WHERE id_barang = ?");
$stmt2->bind_param("ii", $jumlah, $id_barang);
$stmt2->execute();

header("Location: ../pages/barang_masuk.php");
exit;
