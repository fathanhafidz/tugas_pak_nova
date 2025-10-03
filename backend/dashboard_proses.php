<?php
include '../config/koneksi.php';
header('Content-Type: application/json');

// ==========================
// Summary Cards
// ==========================

// total barang
$total_barang = $koneksi->query("SELECT COUNT(*) as total FROM barang")->fetch_assoc()['total'];

// total kategori
$total_kategori = $koneksi->query("SELECT COUNT(*) as total FROM kategori")->fetch_assoc()['total'];

// total supplier
$total_supplier = $koneksi->query("SELECT COUNT(*) as total FROM supplier")->fetch_assoc()['total'];

// barang masuk bulan ini
$barang_masuk_bulan = $koneksi->query("
    SELECT COALESCE(SUM(jumlah),0) as total 
    FROM barang_masuk 
    WHERE MONTH(tanggal_masuk)=MONTH(CURDATE()) 
      AND YEAR(tanggal_masuk)=YEAR(CURDATE())
")->fetch_assoc()['total'];

// barang keluar bulan ini
$barang_keluar_bulan = $koneksi->query("
    SELECT COALESCE(SUM(bkd.jumlah),0) as total 
    FROM barang_keluar_detail bkd
    JOIN barang_keluar bk ON bk.id_keluar=bkd.id_keluar
    WHERE MONTH(bk.tanggal_keluar)=MONTH(CURDATE()) 
      AND YEAR(bk.tanggal_keluar)=YEAR(CURDATE())
")->fetch_assoc()['total'];

// total laba bulan ini
$laba_bulan = $koneksi->query("
    SELECT COALESCE(SUM((bkd.harga_jual_satuan - bkd.harga_beli_satuan) * bkd.jumlah),0) as laba
    FROM barang_keluar_detail bkd
    JOIN barang_keluar bk ON bk.id_keluar=bkd.id_keluar
    WHERE MONTH(bk.tanggal_keluar)=MONTH(CURDATE()) 
      AND YEAR(bk.tanggal_keluar)=YEAR(CURDATE())
")->fetch_assoc()['laba'];

// ==========================
// Grafik Barang Masuk vs Keluar per bulan
// ==========================
$grafik = [];
for ($i=1; $i<=12; $i++) {
    $masuk = $koneksi->query("SELECT COALESCE(SUM(jumlah),0) as total FROM barang_masuk WHERE MONTH(tanggal_masuk)=$i AND YEAR(tanggal_masuk)=YEAR(CURDATE())")->fetch_assoc()['total'];
    $keluar = $koneksi->query("SELECT COALESCE(SUM(bkd.jumlah),0) as total FROM barang_keluar_detail bkd JOIN barang_keluar bk ON bk.id_keluar=bkd.id_keluar WHERE MONTH(bk.tanggal_keluar)=$i AND YEAR(bk.tanggal_keluar)=YEAR(CURDATE())")->fetch_assoc()['total'];
    $grafik[] = ["bulan"=>date("F", mktime(0,0,0,$i,1)), "masuk"=>$masuk, "keluar"=>$keluar];
}

// ==========================
// Grafik Laba per bulan
// ==========================
$grafik_laba = [];
for ($i=1; $i<=12; $i++) {
    $laba = $koneksi->query("SELECT COALESCE(SUM((bkd.harga_jual_satuan - bkd.harga_beli_satuan) * bkd.jumlah),0) as laba FROM barang_keluar_detail bkd JOIN barang_keluar bk ON bk.id_keluar=bkd.id_keluar WHERE MONTH(bk.tanggal_keluar)=$i AND YEAR(bk.tanggal_keluar)=YEAR(CURDATE())")->fetch_assoc()['laba'];
    $grafik_laba[] = ["bulan"=>date("F", mktime(0,0,0,$i,1)), "laba"=>$laba];
}

// ==========================
// Transaksi terbaru
// ==========================

// barang masuk terbaru
$masuk_terbaru = [];
$qMT = $koneksi->query("SELECT bm.tanggal_masuk, b.nama_barang, bm.jumlah FROM barang_masuk bm JOIN barang b ON b.id_barang=bm.id_barang ORDER BY bm.tanggal_masuk DESC, bm.waktu_masuk DESC LIMIT 5");
while ($row = $qMT->fetch_assoc()) {
    $masuk_terbaru[] = $row;
}

// barang keluar terbaru
$keluar_terbaru = [];
$qKT = $koneksi->query("
    SELECT bk.tanggal_keluar, b.nama_barang, bkd.jumlah 
    FROM barang_keluar bk
    JOIN barang_keluar_detail bkd ON bkd.id_keluar=bk.id_keluar
    JOIN barang b ON b.id_barang=bkd.id_barang
    ORDER BY bk.tanggal_keluar DESC, bk.waktu_keluar DESC
    LIMIT 5
");
while ($row = $qKT->fetch_assoc()) {
    $keluar_terbaru[] = $row;
}

// activity log terbaru
$log_terbaru = [];
$qLT = $koneksi->query("SELECT al.waktu, u.username, al.aktivitas FROM activity_log al LEFT JOIN users u ON u.id_users=al.id_users ORDER BY al.waktu DESC LIMIT 5");
while ($row = $qLT->fetch_assoc()) {
    $log_terbaru[] = $row;
}

// ==========================
// Output JSON
// ==========================
echo json_encode([
    "summary" => [
        "total_barang"=>$total_barang,
        "total_kategori"=>$total_kategori,
        "total_supplier"=>$total_supplier,
        "barang_masuk_bulan"=>$barang_masuk_bulan,
        "barang_keluar_bulan"=>$barang_keluar_bulan,
        "laba_bulan"=>$laba_bulan
    ],
    "grafik"=>$grafik,
    "grafik_laba"=>$grafik_laba,
    "masuk_terbaru"=>$masuk_terbaru,
    "keluar_terbaru"=>$keluar_terbaru,
    "log_terbaru"=>$log_terbaru
]);
