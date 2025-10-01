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

// ambil data users
$users = $koneksi->query("SELECT * FROM users ORDER BY id_users DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Manajemen User</title>
   <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
      body {
        padding-top: 56px;   /* biar isi gak ketiban navbar */
        
      }
    </style>
</head>
<body>


<div class="container-fluid p-4">
   <div class="row">
      <!-- Sidebar -->
      <nav class="col-md-1  d-none d-md-block bg-light sidebar">
        <?php include '../includes/sidebar.php'; ?>
      </nav>
      <main class="col-md-10 ms-sm-auto px-md-4 mt-4">
    <h3>Manajemen User</h3>

    <!-- Alert Pesan -->
    <?php if (isset($_GET['pesan'])): ?>
      <?php if (stripos($_GET['pesan'], 'berhasil') !== false): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <strong>Berhasil!</strong> <?= htmlspecialchars($_GET['pesan']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif (stripos($_GET['pesan'], 'Password minimal') !== false): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Peringatan!</strong> <?= htmlspecialchars($_GET['pesan']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif (stripos($_GET['pesan'], 'Username sudah') !== false): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Peringatan!</strong> <?= htmlspecialchars($_GET['pesan']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php elseif (stripos($_GET['pesan'], 'Input tidak lengkap') !== false): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
          <strong>Peringatan!</strong> <?= htmlspecialchars($_GET['pesan']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php else: ?>
        <div class="alert alert-info alert-dismissible fade show" role="alert">
          <?= htmlspecialchars($_GET['pesan']) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      <?php endif; ?>
    <?php endif; ?>


    <!-- form tambah user -->
    <div class="card mb-3">
        <div class="card-header">Tambah User</div>
        <div class="card-body">
            <form action="<?= $base_url ?>/backend/manajemen_users_proses.php" method="POST">
                <input type="hidden" name="aksi" value="tambah">
                <div class="row mb-2">
                    <div class="col-md-4">
                        <label>Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label>Level</label>
                        <select name="level" class="form-control" required>
                            <option value="admin">Admin</option>
                            <option value="operator_masuk">Operator Masuk</option>
                            <option value="operator_keluar">Operator Keluar</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>

    <!-- daftar user -->
    <div class="card">
        <div class="card-header">Daftar User</div>
        <div class="card-body">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['id_users'] ?></td>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= $row['level'] ?></td>
                        <td>
                            <!-- tombol edit -->
                            <button class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editModal<?= $row['id_users'] ?>">
                                Edit
                            </button>

                            <!-- tombol hapus -->
                            <a href="../backend/manajemen_users_proses.php?aksi=hapus&id=<?= $row['id_users'] ?>" 
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin hapus user ini?')">
                               Hapus
                            </a>
                        </td>
                    </tr>

                    <!-- Modal Edit -->
                    <div class="modal fade" id="editModal<?= $row['id_users'] ?>" tabindex="-1">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="../backend/manajemen_users_proses.php" method="POST">
                            <div class="modal-header">
                              <h5 class="modal-title">Edit User</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="aksi" value="edit">
                                <input type="hidden" name="id_users" value="<?= $row['id_users'] ?>">

                                <div class="mb-2">
                                    <label>Username</label>
                                    <input type="text" name="username" value="<?= htmlspecialchars($row['username']) ?>" class="form-control" required>
                                </div>
                                <div class="mb-2">
                                    <label>Password (kosongkan jika tidak diubah)</label>
                                    <input type="password" name="password" class="form-control">
                                </div>
                                <div class="mb-2">
                                    <label>Level</label>
                                    <select name="level" class="form-control" required>
                                        <option value="admin" <?= $row['level']=="admin"?"selected":"" ?>>Admin</option>
                                        <option value="operator_masuk" <?= $row['level']=="operator_masuk"?"selected":"" ?>>Operator Masuk</option>
                                        <option value="operator_keluar" <?= $row['level']=="operator_keluar"?"selected":"" ?>>Operator Keluar</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                              <button type="submit" class="btn btn-primary">Simpan</button>
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>

                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
      </main>
   </div>
    </div>
</div>
</body>
</html>
