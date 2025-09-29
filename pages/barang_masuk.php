<?php
session_start();
include '../config/base_url.php';
include '../config/koneksi.php';
include '../includes/appbar.php';
include '../includes/sidebar.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

$barang = $koneksi->query("SELECT * FROM barang");
$barang_masuk = $koneksi->query("
    SELECT bm.*, b.nama_barang 
    FROM barang_masuk bm 
    JOIN barang b ON bm.id_barang = b.id_barang 
    ORDER BY bm.tanggal_masuk DESC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Barang Masuk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      padding-top: 56px;   /* biar konten tidak ketiban navbar */
      padding-left: 250px; /* biar konten tidak ketiban sidebar */
    }
  </style>
</head>
<body>
<div class="container-fluid p-4">
  <h3>Barang Masuk</h3>

  <!-- Form Tambah Barang Masuk -->
  <form action="../backend/barang_masuk_proses.php" method="post" class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">Barang</label>
      <select name="id_barang" class="form-control" required>
        <option value="">-- Pilih Barang --</option>
        <?php while($row = $barang->fetch_assoc()){ ?>
          <option value="<?= $row['id_barang'] ?>"><?= $row['nama_barang'] ?></option>
        <?php } ?>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Jumlah</label>
      <input type="number" name="jumlah" class="form-control" required>
    </div>
    <div class="col-md-2">
      <label class="form-label">Harga Beli</label>
      <input type="number" step="0.01" name="harga_beli_satuan" class="form-control" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Keterangan</label>
      <input type="text" name="keterangan" class="form-control">
    </div>
    <div class="col-md-1 d-flex align-items-end">
      <button type="submit" class="btn btn-success w-100">Tambah</button>
    </div>
  </form>

  <!-- Tabel Data Barang Masuk -->
  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>ID</th>
        <th>Barang</th>
        <th>Jumlah</th>
        <th>Harga Beli</th>
        <th>Tanggal</th>
        <th>Sisa</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      <?php while($row = $barang_masuk->fetch_assoc()){ ?>
      <tr>
        <td><?= $row['id_masuk'] ?></td>
        <td><?= $row['nama_barang'] ?></td>
        <td><?= $row['jumlah'] ?></td>
        <td>Rp <?= number_format($row['harga_beli_satuan'], 0, ',', '.') ?></td>
        <td><?= $row['tanggal_masuk'] ?></td>
        <td><?= $row['jumlah_sisa'] ?></td>
        <td><?= $row['keterangan'] ?></td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>
</body>
</html>
