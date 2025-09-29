<?php
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<div class="bg-white vh-100 p-3 border-end" style="width: 220px; position: fixed;">
  <h5 class="mb-4 text-dark">Manajemen Gudang FIFO</h5>
  <ul class="nav flex-column">

    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/dashboard.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'dashboard.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-speedometer2 me-2"></i> Dashboard
      </a>
    </li>

    <hr>

    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/produk.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'produk.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-box me-2"></i> Barang
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/kategori.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'kategori.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-folder me-2"></i> Kategori
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/supplier.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'supplier.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-bar-chart me-2"></i> Supplier
      </a>
    </li>

    <hr>

    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/barang_masuk.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'barang_masuk.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-arrow-up-circle me-2"></i> Barang Masuk
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/barang_keluar.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'barang_keluar.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-arrow-down-circle me-2"></i> Barang Keluar
      </a>
    </li>

    <hr>

    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/user.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'user.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-people me-2"></i> Manajemen User
      </a>
    </li>
    <li class="nav-item mb-2">
      <a href="<?= $base_url ?>/pages/activity_log.php" 
         class="nav-link d-flex align-items-center <?php echo ($current_page == 'activity_log.php') ? 'fw-bold text-primary bg-light rounded' : 'text-primary'; ?>">
         <i class="bi bi-journal-text me-2"></i> Activity Log
      </a>
    </li>

  </ul>
</div>
