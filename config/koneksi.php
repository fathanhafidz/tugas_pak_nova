<?php
// konfigurasi database
$host     = "localhost";       // nama host / server
$username = "root";            // username MySQL (default XAMPP: root)
$password = "";                // password MySQL (default XAMPP: kosong)
$database = "manajemen_gudang_fifo2"; // nama database yang sudah kamu buat

// buat koneksi
$koneksi = new mysqli($host, $username, $password, $database);

// cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi database gagal: " . $koneksi->connect_error);
}

// optional: set charset supaya aman untuk UTF-8
$koneksi->set_charset("utf8mb4");
?>
