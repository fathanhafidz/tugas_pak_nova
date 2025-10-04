<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';
include '../includes/appbar.php';
include '../includes/sidebar.php';

// cek login
if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

// ambil data barang (untuk form tambah)
$barang = $koneksi->query("SELECT * FROM barang ORDER BY nama_barang ASC");

// ambil data barang keluar (header transaksi saja)
$barang_keluar = $koneksi->query("
    SELECT bk.id_keluar, bk.waktu_keluar, bk.tujuan, bk.keterangan,
           b.nama_barang
    FROM barang_keluar bk
    LEFT JOIN barang b ON bk.id_barang = b.id_barang
    ORDER BY bk.waktu_keluar DESC
");


?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Barang Keluar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  <style>
    body {
      padding-top: 56px;   /* biar tidak ketiban navbar */
      padding-left: 250px; /* biar tidak ketiban sidebar */
    }
  </style>
</head>
<body>
<div class="container-fluid p-4">

  <h3 class="mb-4">Barang Keluar</h3>

  <!-- Alert Pesan -->
 <?php if (isset($_GET['pesan'])): ?>
  <?php if ($_GET['pesan'] == 'harga_jual_belum_ditentukan'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>Peringatan!</strong> Harga jual barang belum ditentukan. 
      Silakan atur harga jual terlebih dahulu di menu <b>Barang</b>.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  
  <?php elseif ($_GET['pesan'] == 'stok_kurang'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>Peringatan!</strong> Jumlah yang diminta melebihi stok tersedia.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  
  <?php elseif ($_GET['pesan'] == 'input_tidak_lengkap'): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong>Peringatan!</strong> Data yang Anda isi belum lengkap, silakan periksa kembali formulir Anda.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

  <?php elseif ($_GET['pesan'] == 'sukses'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <strong>Berhasil!</strong> Data barang keluar berhasil disimpan.
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  <?php endif; ?>
<?php endif; ?>


  <!-- Form Tambah Barang Keluar -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah Barang Keluar</div>
    <div class="card-body">
      <form action="../backend/barang_keluar_proses.php" method="POST">
        <div class="row mb-3">
          <div class="col-md-4">
            <label class="form-label">Barang</label>
            <select name="id_barang" class="form-select" required>
              <option value="">-- Pilih Barang --</option>
              <?php while ($b = $barang->fetch_assoc()): ?>
                <option value="<?= $b['id_barang'] ?>">
                  <?= $b['nama_barang'] ?> (Stok: <?= $b['stok'] ?>)
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Jumlah</label>
            <input type="number" name="jumlah" class="form-control" required min="1">
          </div>
          <div class="col-md-3">
            <label class="form-label">Tujuan</label>
            <input type="text" name="tujuan" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">Keterangan</label>
            <input type="text" name="keterangan" class="form-control">
          </div>
        </div>
        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> Simpan
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Tabel Barang Keluar -->
  <div class="card">
    <div class="card-header bg-dark text-white">Daftar Transaksi Barang Keluar</div>
    <div class="card-body">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Barang</th>
            <th>Tanggal</th>
            <th>jam</th>
            <th>Tujuan</th>
            <th>Keterangan</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($barang_keluar->num_rows > 0): ?>
            <?php $no=1; while ($bk = $barang_keluar->fetch_assoc()): ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlspecialchars($bk['nama_barang'] ?? '-') ?></td>
                <td><?= date('d-m-Y', strtotime($bk['waktu_keluar'])) ?></td>
                <td><?= date('H:i:s', strtotime($bk['waktu_keluar'])) ?></td>
                <td><?= $bk['tujuan'] ?? '-' ?></td>
                <td><?= $bk['keterangan'] ?? '-' ?></td>
                <td>
                  <!-- Tombol Lihat Detail -->
                  <a href="barang_keluar_detail.php?id_keluar=<?= $bk['id_keluar'] ?>" 
                     class="btn btn-sm btn-info">
                     <i class="bi bi-eye"></i> Detail
                  </a>

                  <!-- Tombol Hapus -->
                  <form action="../backend/barang_keluar_proses.php" method="POST" style="display:inline;">
                    <input type="hidden" name="aksi" value="hapus">
                    <input type="hidden" name="id_keluar" value="<?= $bk['id_keluar'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" 
                            onclick="return confirm('Hapus transaksi ini? Semua detail ikut terhapus.')">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">Belum ada barang keluar</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
