<?php
session_start();
include '../config/base_url.php';

// cek login
if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

// include layout
include '../includes/appbar.php';
include '../includes/sidebar.php';
include '../config/koneksi.php';

// ambil data barang
$barang = mysqli_query($koneksi, "SELECT b.*, k.nama_kategori, s.nama_supplier 
                                  FROM barang b
                                  LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
                                  LEFT JOIN supplier s ON b.id_supplier = s.id_supplier
                                  ORDER BY b.id_barang DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
 <style>
    body {
      padding-top: 56px;
      padding-left: 250px;
      background: #ede7f6; /* pastel ungu soft */
      color: #333;
    }
    h2 {
      font-weight: 600;
      color: #222;
    }
    .card {
      border: none;
      border-radius: 12px;
      background: #fff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card-header {
      border-radius: 12px 12px 0 0;
      font-weight: 600;
      background: linear-gradient(90deg,#6a11cb,#2575fc);
      color: #fff;
    }
    .table {
      background: #fff;
      vertical-align: middle;
    }
    .table thead {
      background: #f3e5f5;
    }
    .table thead th {
      color: #333;
    }
    .form-control, .form-select {
      background-color: #fff;
      border: 1px solid #ccc;
      color: #333;
    }
    .form-control:focus, .form-select:focus {
      border-color: #6a11cb;
      box-shadow: 0 0 0 0.2rem rgba(106,17,203,.25);
    }
    /* tombol custom senada sidebar */
    .btn-custom {
      background: linear-gradient(90deg,#6a11cb,#2575fc);
      border: none;
      color: #fff;
    }
    .btn-custom:hover {
      opacity: 0.9;
      color: #fff;
    }
    .btn-warning {
      background: #ffc107;
      border: none;
      color: #000;
    }
    .btn-secondary {
      background: #6c757d;
      border: none;
      color: #fff;
    }
    .badge-aktif {
      background: linear-gradient(90deg,#6a11cb,#2575fc);
    }
    .badge-nonaktif {
      background: #9e9e9e;
    }
    .modal-content {
      background: #fff;
      color: #333;
      border-radius: 12px;
    }
    .modal-header {
      border-bottom: 1px solid #ddd;
    }
    .modal-footer {
      border-top: 1px solid #ddd;
    }
  </style>
</head>
<body>
  <div class="container-fluid p-4">
    <h2 class="mb-4">📦 Manajemen Barang</h2>

    <!-- Alert pesan -->
    <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal_nonaktif') { ?>
      <div class="alert alert-warning">
        Barang ini sudah dipakai di transaksi, tidak bisa dinonaktifkan.
      </div>
    <?php } ?>

    <!-- Form tambah barang -->
    <div class="card mb-4">
      <div class="card-header">Tambah Barang</div>
      <div class="card-body">
        <form action="../backend/barang_proses.php" method="POST">
          <input type="hidden" name="aksi" value="tambah">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Barang</label>
              <input type="text" name="nama_barang" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label">Kategori</label>
              <select name="id_kategori" class="form-select" required>
                <option value="">-- Pilih Kategori --</option>
                <?php 
                $kategori_opt = mysqli_query($koneksi, "SELECT * FROM kategori WHERE status='aktif' ORDER BY nama_kategori ASC");
                while ($row = mysqli_fetch_assoc($kategori_opt)) { ?>
                  <option value="<?= $row['id_kategori'] ?>"><?= $row['nama_kategori'] ?></option>
                <?php } ?>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Supplier</label>
              <select name="id_supplier" class="form-select" required>
                <option value="">-- Pilih Supplier --</option>
                <?php 
                $supplier_opt = mysqli_query($koneksi, "SELECT * FROM supplier WHERE status='aktif' ORDER BY nama_supplier ASC");
                while ($row = mysqli_fetch_assoc($supplier_opt)) { ?>
                  <option value="<?= $row['id_supplier'] ?>"><?= $row['nama_supplier'] ?></option>
                <?php } ?>
              </select>
            </div>
          </div>

          <div class="row mb-3">
            <div class="col-md-4">
              <label class="form-label">Harga Jual</label>
              <input type="number" step="0.01" name="harga_jual" class="form-control" value="0.00">
              <small class="text-muted">Biarkan 0.00 jika belum ditentukan.</small>
            </div>
          </div>

          <button type="submit" class="btn btn-custom">💾 Simpan</button>
        </form>
      </div>
    </div>

    <!-- Tabel daftar barang -->
    <div class="card">
      <div class="card-header">Daftar Barang</div>
      <div class="card-body table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr class="text-center">
              <th>No</th>
              <th>Nama Barang</th>
              <th>Kategori</th>
              <th>Supplier</th>
              <th>Stok</th>
              <th>Harga Jual</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($barang)) { ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $row['nama_barang'] ?></td>
                <td><?= $row['nama_kategori'] ?? '-' ?></td>
                <td><?= $row['nama_supplier'] ?? '-' ?></td>
                <td class="text-center"><?= $row['stok'] ?></td>
                <td>Rp <?= number_format((float) $row['harga_jual'], 2, ',', '.') ?></td>

                <td class="text-center">
                  <?php if ($row['status'] == 'aktif') { ?>
                    <span class="badge badge-aktif">Aktif</span>
                  <?php } else { ?>
                    <span class="badge badge-nonaktif">Nonaktif</span>
                  <?php } ?>
                </td>
                <td class="text-center">
                  <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editBarang<?= $row['id_barang'] ?>">
                    <i class="bi bi-pencil"></i>
                  </button>
                  <?php if ($row['status'] == 'aktif') { ?>
                    <a href="../backend/barang_proses.php?aksi=nonaktif&id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-secondary">
                       <i class="bi bi-eye-slash"></i>
                    </a>
                  <?php } else { ?>
                    <a href="../backend/barang_proses.php?aksi=aktif&id=<?= $row['id_barang'] ?>" class="btn btn-sm btn-custom">
                       <i class="bi bi-eye"></i>
                    </a>
                  <?php } ?>
                </td>
              </tr>

              <!-- Modal Edit Barang -->
              <div class="modal fade" id="editBarang<?= $row['id_barang'] ?>" tabindex="-1">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">
                    <form action="../backend/barang_proses.php" method="POST">
                      <input type="hidden" name="aksi" value="edit">
                      <input type="hidden" name="id_barang" value="<?= $row['id_barang'] ?>">
                      <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">Edit Barang</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label class="form-label">Nama Barang</label>
                            <input type="text" name="nama_barang" value="<?= $row['nama_barang'] ?>" class="form-control" required>
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Kategori</label>
                            <select name="id_kategori" class="form-select" required>
                              <option value="">-- Pilih Kategori --</option>
                              <?php 
                              $kategori_opt = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY nama_kategori ASC");
                              while ($opt = mysqli_fetch_assoc($kategori_opt)) { ?>
                                <option value="<?= $opt['id_kategori'] ?>" <?= ($opt['id_kategori']==$row['id_kategori'])?'selected':'' ?>>
                                  <?= $opt['nama_kategori'] ?>
                                </option>
                              <?php } ?>
                            </select>
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Supplier</label>
                            <select name="id_supplier" class="form-select" required>
                              <option value="">-- Pilih Supplier --</option>
                              <?php 
                              $supplier_opt = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY nama_supplier ASC");
                              while ($opt = mysqli_fetch_assoc($supplier_opt)) { ?>
                                <option value="<?= $opt['id_supplier'] ?>" <?= ($opt['id_supplier']==$row['id_supplier'])?'selected':'' ?>>
                                  <?= $opt['nama_supplier'] ?>
                                </option>
                              <?php } ?>
                            </select>
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-4">
                            <label class="form-label">Harga Jual</label>
                            <input type="number" step="0.01" name="harga_jual" value="<?= $row['harga_jual'] ?>" class="form-control" required>
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="submit" class="btn btn-custom">Update</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            <?php } ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
