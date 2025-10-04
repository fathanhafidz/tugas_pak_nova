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
    ORDER BY bm.waktu_masuk DESC
");

?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Barang Masuk</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      background: #ede7f6;   /* warna background */
      padding-top: 56px;     /* biar konten tidak ketiban navbar */
      padding-left: 250px;   /* biar konten tidak ketiban sidebar */
    }
    .card {
      border-radius: 12px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    .card-header {
      background: linear-gradient(90deg,#6a11cb,#2575fc);
      color: white;
      font-weight: 500;
    }
    .modal-content {
      background: #ede7f6;
    }
    h3 {
      color: #222;
      font-weight: 600;
    }
    .btn-success {
      background-color: #6a1b9a;
      border: none;
    }
    .btn-success:hover {
      background-color: #4a148c;
    }
    .btn-primary {
      background-color: #4527a0;
      border: none;
    }
    .btn-primary:hover {
      background-color: #311b92;
    }
    .btn-warning {
      background-color: #ffb300;
      border: none;
    }
    .btn-warning:hover {
      background-color: #ffa000;
    }
    .btn-danger {
      background-color: #d32f2f;
      border: none;
    }
    .btn-danger:hover {
      background-color: #b71c1c;
    }
     .bg-gradient-primary   { background: linear-gradient(90deg,#6a11cb,#2575fc); }
  </style>
</head>
<body>
<div class="container-fluid p-4">
  <h3>Barang Masuk</h3>

  <!-- Form Tambah Barang Masuk -->
  <form action="../backend/barang_masuk_proses.php" method="post" class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">barang</label>
      <select name="id_barang" class="form-control" required>
        <option value="">-- Pilih Barang --</option>
        <?php 
        $barang_opt = mysqli_query($koneksi, "SELECT * FROM barang WHERE status='aktif' ORDER BY nama_barang ASC");
        while ($row = mysqli_fetch_assoc($barang_opt)) { ?>
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
      <button type="submit" name="tambah" class="btn bg-gradient-primary">Tambah</button>
    </div>
  </form>

  <!-- Tabel Data Barang Masuk -->
  <div class="card">
    <div class="card-header">Daftar Barang Masuk</div>
    <div class="card-body table-responsive">
      <table class="table table-bordered table-hover">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Barang</th>
            <th>Jumlah</th>
            <th>Harga Beli</th>
            <th>Tanggal</th>
            <th>jam</th>
            <th>Sisa</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php while($row = $barang_masuk->fetch_assoc()){ ?>
          <tr>
            <td><?= $row['id_masuk'] ?></td>
            <td><?= $row['nama_barang'] ?></td>
            <td><?= $row['jumlah'] ?></td>
            <td>Rp <?= number_format($row['harga_beli_satuan'], 0, ',', '.') ?></td>
            <td><?= date('d-m-Y', strtotime($row['waktu_masuk'])) ?></td>
            <td><?= date('H:i:s', strtotime($row['waktu_masuk'])) ?></td>
            <td><?= $row['jumlah_sisa'] ?></td>
            <td><?= $row['keterangan'] ?></td>
            <td class="text-center">
              <!-- Tombol Edit Modal -->
              <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $row['id_masuk'] ?>">Edit</button>

              <!-- Modal Edit -->
              <div class="modal fade" id="editModal<?= $row['id_masuk'] ?>" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="../backend/barang_masuk_proses.php" method="post">
                      <div class="modal-header bg-gradient-primary">
                        <h5 class="modal-title">Edit Barang Masuk</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                      </div>
                      <div class="modal-body">
                        <input type="hidden" name="id_masuk" value="<?= $row['id_masuk'] ?>">
                        <div class="mb-3">
                          <label class="form-label">Jumlah</label>
                          <input type="number" name="jumlah" value="<?= $row['jumlah'] ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Harga Beli</label>
                          <input type="number" step="0.01" name="harga_beli_satuan" value="<?= $row['harga_beli_satuan'] ?>" class="form-control" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Keterangan</label>
                          <input type="text" name="keterangan" value="<?= $row['keterangan'] ?>" class="form-control">
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit" class="btn ">Simpan</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Tombol Hapus -->
              <form action="../backend/barang_masuk_proses.php" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?')">
                <input type="hidden" name="id_masuk" value="<?= $row['id_masuk'] ?>">
                <button type="submit" name="hapus" class="btn btn-danger btn-sm">Hapus</button>
              </form>
            </td>
          </tr>
          <?php } ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
</body>
</html>
