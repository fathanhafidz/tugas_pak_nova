<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/base_url.php';
$level = $_SESSION['level'];
?>

<!-- Sidebar -->
<div class="sidebar">
  <ul class="nav flex-column p-3">
    <li class="nav-item">
      <a class="nav-link" href="<?= $base_url ?>/pages/dashboard.php">📊 Dashboard</a>
    </li>
    <hr>
    <li class="nav-item">
      <a class="nav-link" href="<?= $base_url ?>/pages/barang.php">📦 Barang</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?= $base_url ?>/pages/kategori.php">🗂️ Kategori</a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="<?= $base_url ?>/pages/supplier.php">🏭 Supplier</a>
    </li>

    <?php if ($level == 'operator_masuk' || $level == 'admin') : ?>
      <hr>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/pages/barang_masuk.php">⬆️ Barang Masuk</a>
      </li>
    <?php endif; ?>

    <?php if ($level == 'operator_keluar' || $level == 'admin') : ?>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/pages/barang_keluar.php">⬇️ Barang Keluar</a>
      </li>
    <?php endif; ?>

    <?php if ($level == 'admin') : ?>
      <hr>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/pages/manajemen_users.php">👥 Manajemen User</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/pages/activity_log.php">📑 Activity Log</a>
      </li>
    <?php endif; ?>
  </ul>
</div>

<!-- Inline CSS untuk dark sidebar -->
<style>
  body {
    background: #1e1e2f;
    color: #fff;
    font-family: "Segoe UI", sans-serif;
  }

  .sidebar {
    position: fixed;
    top: 56px;
    left: 0;
    width: 250px;
    height: 100vh;
    background: linear-gradient(180deg, #6a11cb, #2575fc); /* gradient ungu → biru */
    overflow-y: auto;
    box-shadow: 2px 0 8px rgba(0, 0, 0, 0.3);
  }

  .sidebar .nav-link {
    color: #ddd;
    padding: 12px 15px;
    border-radius: 8px;
    margin-bottom: 5px;
    transition: all 0.3s ease;
  }

  .sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.15);
    color: #fff;
    text-decoration: none;
    padding-left: 20px; /* animasi geser dikit */
  }

  .sidebar hr {
    border-color: rgba(255, 255, 255, 0.2);
  }
</style>
