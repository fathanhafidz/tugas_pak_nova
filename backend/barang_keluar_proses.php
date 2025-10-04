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
if (!$id_barang || $jumlah <= 0 || !$tujuan || !$keterangan) {
    header("Location: $base_url/pages/barang_keluar.php?pesan=input_tidak_lengkap");
    exit;

    } else {
        // ambil data barang sekali saja (stok & harga_jual)
        $cek = $koneksi->query("SELECT stok, harga_jual FROM barang WHERE id_barang='$id_barang'")->fetch_assoc();

        // 2. cek harga jual
        if (!$cek || $cek['harga_jual'] <= 0) {
            header("Location: $base_url/pages/barang_keluar.php?pesan=harga_jual_belum_ditentukan");
            exit;
        } else {
            // 3. cek stok
            if ($cek['stok'] < $jumlah) {
                header("Location: $base_url/pages/barang_keluar.php?pesan=stok_kurang");
                exit;
            } else {
                // --- proses simpan barang keluar ---
                $id_barang   = $_POST['id_barang'];
                $tanggal = date('Y-m-d');
                $waktu   = date('H:i:s');
               $stmt = $koneksi->prepare("
    INSERT INTO barang_keluar (id_barang, waktu_keluar, tujuan, keterangan) 
    VALUES (?, NOW(), ?, ?)
");
$stmt->bind_param("iss", $id_barang, $tujuan, $keterangan);
$stmt->execute();
$id_keluar = $stmt->insert_id;
$stmt->close();



                $sisa_keluar = $jumlah;
               $batch = $koneksi->query("
    SELECT * FROM barang_masuk 
    WHERE id_barang='$id_barang' AND jumlah_sisa > 0 
    ORDER BY waktu_masuk ASC
");


                while ($sisa_keluar > 0 && $bm = $batch->fetch_assoc()) {
                    $ambil = min($sisa_keluar, $bm['jumlah_sisa']);

                    // kurangi jumlah_sisa di batch masuk
                    $koneksi->query("UPDATE barang_masuk SET jumlah_sisa = jumlah_sisa - $ambil WHERE id_masuk='{$bm['id_masuk']}'");

                    // simpan detail keluar
                    $stmt2 = $koneksi->prepare("INSERT INTO barang_keluar_detail 
                        (id_keluar, id_barang, id_masuk, jumlah, harga_beli_satuan, harga_jual_satuan) 
                        VALUES (?, ?, ?, ?, ?, ?)");
                    $stmt2->bind_param("iiiidd", $id_keluar, $id_barang, $bm['id_masuk'], $ambil, $bm['harga_beli_satuan'], $cek['harga_jual']);
                    $stmt2->execute();
                    $stmt2->close();

                    $sisa_keluar -= $ambil;
                }

                // kurangi stok total
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
        }
    }
}
?>
