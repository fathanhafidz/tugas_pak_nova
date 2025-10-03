<?php
session_start();
include '../config/base_url.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

include '../includes/appbar.php';
include '../includes/sidebar.php';
include '../config/koneksi.php';

$supplier = mysqli_query($koneksi, "SELECT * FROM supplier ORDER BY id_supplier DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Manajemen Supplier</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <style>
    body {
      padding-top: 56px;
      padding-left: 250px;
    }
  </style>
</head>
<body>
  <div class="container-fluid p-4">
    <h2 class="mb-4">Manajemen Supplier</h2>

    <?php if (isset($_SESSION['error'])) { ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php } ?>

    <!-- Form tambah supplier -->
    <div class="card mb-4">
      <div class="card-header bg-primary text-white">Tambah Supplier</div>
      <div class="card-body">
        <form action="../backend/supplier_proses.php" method="POST">
          <div class="row mb-3">
            <div class="col-md-6">
              <label class="form-label">Nama Supplier</label>
              <input type="text" name="nama_supplier" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Telepon</label>
              <input type="text" name="telepon" class="form-control" required>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control" required></textarea>
          </div>
          <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
        </form>
      </div>
    </div>

    <!-- Tabel daftar supplier -->
    <div class="card">
      <div class="card-header bg-dark text-white">Daftar Supplier</div>
      <div class="card-body">
        <table class="table table-bordered table-striped">
          <thead>
            <tr class="text-center">
              <th>No</th>
              <th>Nama Supplier</th>
              <th>Telepon</th>
              <th>Alamat</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $no = 1;
            while ($row = mysqli_fetch_assoc($supplier)) { ?>
              <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td><?= $row['nama_supplier'] ?></td>
                <td><?= $row['telepon'] ?></td>
                <td><?= $row['alamat'] ?></td>
                <td class="text-center">
                  <span class="badge <?= $row['status']=='aktif'?'bg-success':'bg-secondary' ?>">
                    <?= ucfirst($row['status']) ?>
                  </span>
                </td>
                <td class="text-center">
                  <!-- Tombol Edit -->
                  <button class="btn btn-sm btn-warning" 
                          data-bs-toggle="modal" 
                          data-bs-target="#editModal<?= $row['id_supplier'] ?>">
                    <i class="bi bi-pencil"></i>
                  </button>

                  <!-- Ubah Status -->
                  <form action="../backend/supplier_proses.php" method="POST" class="d-inline">
                    <input type="hidden" name="id_supplier" value="<?= $row['id_supplier'] ?>">
                    <input type="hidden" name="status" 
                           value="<?= $row['status']=='aktif'?'nonaktif':'aktif' ?>">
                    <button type="submit" name="ubah_status" class="btn ... btn-sm" 
                            class="btn <?= $row['status']=='aktif'?'btn-danger':'btn-success' ?> btn-sm">
                      <?= $row['status']=='aktif'?'Nonaktifkan':'Aktifkan' ?>
                    </button>
                  </form>
                </td>
              </tr>

              <!-- Modal Edit Supplier -->
              <div class="modal fade" id="editModal<?= $row['id_supplier'] ?>" tabindex="-1">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <form action="../backend/supplier_proses.php" method="POST">
                      <input type="hidden" name="id_supplier" value="<?= $row['id_supplier'] ?>">
                      <div class="modal-header">
                        <h5 class="modal-title">Edit Supplier</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>
                      <div class="modal-body">
                        <div class="mb-3">
                          <label class="form-label">Nama Supplier</label>
                          <input type="text" name="nama_supplier" class="form-control" value="<?= $row['nama_supplier'] ?>" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Telepon</label>
                          <input type="text" name="telepon" class="form-control" value="<?= $row['telepon'] ?>" required>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Alamat</label>
                          <textarea name="alamat" class="form-control" required><?= $row['alamat'] ?></textarea>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
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
</body>
</html>
