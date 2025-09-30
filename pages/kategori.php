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

$result = $koneksi->query("SELECT * FROM kategori ORDER BY id_kategori DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Data Kategori</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
      body {
      padding-top: 56px;
      padding-left: 250px;
    }
  </style>
</head>
<body class="bg-light">

<div class="container mt-5">
  <h3 class="mb-3">Data Kategori</h3>

  <?php if (isset($_SESSION['error'])) { ?>
      <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php } ?>

  <!-- Form Tambah -->
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">Tambah Kategori</div>
    <div class="card-body">
      <form action="<?= $base_url ?>/backend/kategori_proses.php" method="post">
        <div class="mb-3">
          <label for="nama_kategori" class="form-label">Nama Kategori</label>
          <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
        </div>
        <button type="submit" name="tambah" class="btn btn-success">Simpan</button>
      </form>
    </div>
  </div>

  <!-- List Kategori -->
  <div class="card">
    <div class="card-header bg-dark text-white">Daftar Kategori</div>
    <div class="card-body">
      <table class="table table-bordered table-striped">
        <thead>
          <tr class="text-center">
            <th width="50">No</th>
            <th>Nama Kategori</th>
            <th>Status</th>
            <th width="350">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          $no = 1;
          while ($row = $result->fetch_assoc()) { ?>
            <tr>
              <td class="text-center"><?= $no++ ?></td>
              <td><?= $row['nama_kategori'] ?></td>
              <td class="text-center">
                <span class="badge <?= $row['status']=='aktif'?'bg-success':'bg-secondary' ?>">
                  <?= ucfirst($row['status']) ?>
                </span>
              </td>
              <td class="text-center">
                <!-- Form Edit -->
                <form action="<?= $base_url ?>/backend/kategori_proses.php" method="post" class="d-inline">
                  <input type="hidden" name="id_kategori" value="<?= $row['id_kategori'] ?>">
                  <input type="text" name="nama_kategori" value="<?= $row['nama_kategori'] ?>" 
                         class="form-control d-inline w-50" required>
                  <button type="submit" name="edit" class="btn btn-warning btn-sm">Edit</button>
                </form>

                <!-- Ubah Status -->
                <form action="<?= $base_url ?>/backend/kategori_proses.php" method="post" class="d-inline">
                  <input type="hidden" name="id_kategori" value="<?= $row['id_kategori'] ?>">
                  <input type="hidden" name="status" 
                         value="<?= $row['status']=='aktif'?'nonaktif':'aktif' ?>">
                  <button type="submit" name="ubah_status" 
                          class="btn <?= $row['status']=='aktif'?'btn-danger':'btn-success' ?> btn-sm">
                    <?= $row['status']=='aktif'?'Nonaktifkan':'Aktifkan' ?>
                  </button>
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
