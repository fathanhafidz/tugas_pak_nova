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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <style>
    body {
      padding-top: 56px;
      padding-left: 250px;
      background-color: #ede7f6; /* pastel lavender */
    }

    .card {
      border-radius: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
      background-color: #ffffff;
    }

    .card-header {
      border-top-left-radius: 12px !important;
      border-top-right-radius: 12px !important;
      background: linear-gradient(90deg, #6a11cb, #2575fc);
      color: #fff !important;
    }

    table {
      background: #ffffff;
    }

    th {
      background: #f3e5f5;
    }

    /* Tombol custom senada sidebar */
    .btn-custom {
      background: linear-gradient(90deg, #6a11cb, #2575fc);
      color: #fff;
      border: none;
    }
    .btn-custom:hover {
      opacity: 0.9;
      color: #fff;
    }

    /* Badge aktif & nonaktif pakai gradasi */
    .badge-aktif {
      background: linear-gradient(90deg, #6a11cb, #2575fc);
    }
    .badge-nonaktif {
      background: #9e9e9e;
    }
  </style>
</head>
<body>

<div class="container mt-5">
  <h3 class="mb-4 fw-bold text-dark">Data Kategori</h3>

  <?php if (isset($_SESSION['error'])) { ?>
      <div class="alert alert-warning alert-dismissible fade show">
        <strong>Peringatan!</strong> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
  <?php } ?>

  <!-- Form Tambah -->
  <div class="card mb-4">
    <div class="card-header">Tambah Kategori</div>
    <div class="card-body">
      <form action="<?= $base_url ?>/backend/kategori_proses.php" method="post">
        <div class="mb-3">
          <label for="nama_kategori" class="form-label">Nama Kategori</label>
          <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" required>
        </div>
        <button type="submit" name="tambah" class="btn btn-custom">Simpan</button>
      </form>
    </div>
  </div>

  <!-- List Kategori -->
  <div class="card">
    <div class="card-header">Daftar Kategori</div>
    <div class="card-body">
      <table class="table table-bordered table-striped align-middle">
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
                <span class="badge <?= $row['status']=='aktif'?'badge-aktif':'badge-nonaktif' ?>">
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
                          class="btn btn-custom btn-sm">
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
