<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg fixed-top" style="background: #1e1e2f; border-bottom: 1px solid rgba(255,255,255,0.1);">
  <div class="container-fluid">
    <!-- Judul / Brand -->
    <a class="navbar-brand fw-bold" href="../pages/dashboard.php" style="color: #fff;">
      🚀 Manajemen Gudang FIFO
    </a>

    <!-- Bagian kanan -->
    <div class="d-flex align-items-center">
      <span class="me-3" style="color: #bbb;">
        <?php echo $_SESSION['username']; ?> 
        <small style="color:#6a11cb;">(<?php echo $_SESSION['level']; ?>)</small>
      </span>
      <form action ="../auth/logout.php" method="post" class="d-inline">
        <button type="submit" class="btn btn-sm" 
          style="background: linear-gradient(90deg,#ff416c,#ff4b2b); border:none; color:#fff; border-radius:8px;">
          🔒 Logout
        </button>
      </form>
    </div>
  </div>
</nav>
