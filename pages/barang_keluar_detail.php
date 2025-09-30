<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';
include '../includes/appbar.php';
include '../includes/sidebar.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

if (!isset($_GET['id_keluar'])) {
    header("Location: $base_url/pages/barang_keluar.php");
    exit;
}

$id_keluar = intval($_GET['id_keluar']);

// Ambil header transaksi
$header = $koneksi->query("SELECT * FROM barang_keluar WHERE id_keluar=$id_keluar")->fetch_assoc();

// Ambil detail transaksi
$detail = $koneksi->query("
    SELECT d.*, b.nama_barang, bm.tanggal_masuk 
    FROM barang_keluar_detail d
    JOIN barang b ON d.id_barang = b.id_barang
    JOIN barang_masuk bm ON d.id_masuk = bm.id_masuk
    WHERE d.id_keluar = $id_keluar
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Detail Barang Keluar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <style>
    body {
      padding-top: 56px;
      padding-left: 250px;
    }
  </style>
</head>
<body>
<div class="container-fluid p-4">
  <h3 class="mb-3">Detail Barang Keluar #<?= $id_keluar ?></h3>
  <p><strong>Tanggal:</strong> <?= $header['tanggal_keluar'] ?> |
     <strong>Waktu:</strong> <?= $header['waktu_keluar'] ?> |
     <strong>Tujuan:</strong> <?= $header['tujuan'] ?? '-' ?> |
     <strong>Keterangan:</strong> <?= $header['keterangan'] ?? '-' ?>
  </p>

  <div class="card">
    <div class="card-header bg-dark text-white">Daftar Barang</div>
    <div class="card-body">
      <table class="table table-bordered">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Barang</th>
            <th>Batch Masuk</th>
            <th>Jumlah</th>
            <th>Harga Beli</th>
            <th>Harga Jual</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($detail->num_rows > 0): ?>
            <?php $no=1; while($d = $detail->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= $d['nama_barang'] ?></td>
                <td><?= $d['tanggal_masuk'] ?> (ID: <?= $d['id_masuk'] ?>)</td>
                <td><?= $d['jumlah'] ?></td>
                <td>Rp <?= number_format($d['harga_beli_satuan'],0,',','.') ?></td>
                <td>Rp <?= number_format($d['harga_jual_satuan'],0,',','.') ?></td>
                <td>
                  <form action="../backend/barang_keluar_detail_proses.php" method="POST" style="display:inline;">
                    <input type="hidden" name="aksi" value="hapus">
                    <input type="hidden" name="id_detail" value="<?= $d['id_detail'] ?>">
                    <input type="hidden" name="id_keluar" value="<?= $id_keluar ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus detail ini?')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center">Belum ada detail barang keluar</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
      <a href="<?= $base_url ?>/pages/barang_keluar.php" class="btn btn-secondary mt-3">Kembali</a>
    </div>
  </div>
</div>
</body>
</html>
