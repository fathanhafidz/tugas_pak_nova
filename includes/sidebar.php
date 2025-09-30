<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include '../config/base_url.php';
$level = $_SESSION['level'];
?>

<div class="bg-light border-end position-fixed" style="top: 56px; left: 0; width: 250px; height: 100vh; overflow-y: auto;">
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
        <a class="nav-link" href="<?= $base_url ?>/pages/users.php">👥 Manajemen User</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="<?= $base_url ?>/pages/activity_log.php">📑 Activity Log</a>
      </li>
    <?php endif; ?>
  </ul>
</div>
