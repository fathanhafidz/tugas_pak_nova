<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_barang   = $_POST['id_barang'] ?? null;
    $jumlah      = (int) ($_POST['jumlah'] ?? 0);
    $tujuan      = $_POST['tujuan'] ?? null;
    $keterangan  = $_POST['keterangan'] ?? null;

    // validasi input
    if (!$id_barang || $jumlah <= 0) {
        header("Location: $base_url/pages/barang_keluar.php?pesan=input_tidak_lengkap");
        exit;
    }

    // cek stok total barang
    $cek_stok = $koneksi->query("SELECT stok FROM barang WHERE id_barang='$id_barang'")->fetch_assoc();
    if (!$cek_stok || $cek_stok['stok'] < $jumlah) {
        header("Location: $base_url/pages/barang_keluar.php?pesan=stok_kurang");
        exit;
    }

    // buat header barang_keluar
    $tanggal = date('Y-m-d');
    $waktu   = date('H:i:s');
    $stmt = $koneksi->prepare("INSERT INTO barang_keluar (tanggal_keluar, waktu_keluar, tujuan, keterangan) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $tanggal, $waktu, $tujuan, $keterangan);
    $stmt->execute();
    $id_keluar = $stmt->insert_id;
    $stmt->close();

    // ambil batch barang masuk (FIFO: jumlah_sisa > 0, urut tanggal/waktu)
    $sisa_keluar = $jumlah;
    $batch = $koneksi->query("
        SELECT * FROM barang_masuk 
        WHERE id_barang='$id_barang' AND jumlah_sisa > 0 
        ORDER BY tanggal_masuk ASC, waktu_masuk ASC
    ");

    while ($sisa_keluar > 0 && $bm = $batch->fetch_assoc()) {
        $ambil = min($sisa_keluar, $bm['jumlah_sisa']);

        // kurangi jumlah_sisa di batch masuk
        $koneksi->query("UPDATE barang_masuk SET jumlah_sisa = jumlah_sisa - $ambil WHERE id_masuk='{$bm['id_masuk']}'");

        // harga jual ambil dari barang (default)
        $harga_jual = $koneksi->query("SELECT harga_jual FROM barang WHERE id_barang='$id_barang'")->fetch_assoc();
        $harga_jual_satuan = $harga_jual['harga_jual'];

        // simpan ke barang_keluar_detail
        $stmt2 = $koneksi->prepare("INSERT INTO barang_keluar_detail (id_keluar, id_barang, id_masuk, jumlah, harga_beli_satuan, harga_jual_satuan) 
                                    VALUES (?, ?, ?, ?, ?, ?)");
        $stmt2->bind_param("iiiidd", $id_keluar, $id_barang, $bm['id_masuk'], $ambil, $bm['harga_beli_satuan'], $harga_jual_satuan);
        $stmt2->execute();
        $stmt2->close();

        $sisa_keluar -= $ambil;
    }

    // kurangi stok total di tabel barang
    $koneksi->query("UPDATE barang SET stok = stok - $jumlah WHERE id_barang='$id_barang'");

    // catat ke activity_log
    $id_users = $_SESSION['id_users'];
    $aktivitas = "Mengeluarkan barang ID $id_barang sejumlah $jumlah (ID keluar: $id_keluar)";
    $stmt3 = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, 'barang_keluar', ?)");
    $stmt3->bind_param("isi", $id_users, $aktivitas, $id_keluar);
    $stmt3->execute();
    $stmt3->close();

    header("Location: $base_url/pages/barang_keluar.php?pesan=sukses");
    exit;
}
?>
