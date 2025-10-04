<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

// pastikan request POST dan ada id_detail
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_detail'], $_POST['id_keluar'])) {
    $id_detail = intval($_POST['id_detail']);
    $id_keluar = intval($_POST['id_keluar']);

    // ambil data detail
    $detail = $koneksi->query("
    SELECT d.*, bm.id_barang 
    FROM barang_keluar_detail d
    JOIN barang_masuk bm ON d.id_masuk = bm.id_masuk
    WHERE d.id_detail = $id_detail
")->fetch_assoc();
    if ($detail) {
        $id_barang = $detail['id_barang'];
        $jumlah   = $detail['jumlah'];
        $id_masuk = $detail['id_masuk'];

        // kembalikan stok ke tabel barang
        $koneksi->query("UPDATE barang SET stok = stok + $jumlah WHERE id_barang=$id_barang");

        // kembalikan jumlah_sisa ke batch masuk
        $koneksi->query("UPDATE barang_masuk SET jumlah_sisa = jumlah_sisa + $jumlah WHERE id_masuk=$id_masuk");

        // hapus detail
        $koneksi->query("DELETE FROM barang_keluar_detail WHERE id_detail=$id_detail");

        // catat log
        $id_users  = $_SESSION['id_users'];
        $aktivitas = "Menghapus detail barang keluar (id_detail=$id_detail)";
        $stmt = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, 'barang_keluar_detail', ?)");
        $stmt->bind_param("isi", $id_users, $aktivitas, $id_detail);
        $stmt->execute();
    }

    header("Location: $base_url/pages/barang_keluar_detail.php?id_keluar=$id_keluar&pesan=hapus_sukses");
    exit;
} else {
    // kalau tidak ada data yang dikirim
    header("Location: $base_url/pages/barang_keluar.php?pesan=hapus_gagal");
    exit;
}
?>
