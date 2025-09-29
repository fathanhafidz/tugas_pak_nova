<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
  <div class="container-fluid">
    <a class="navbar-brand" href="../pages/dashboard.php">Manajemen Gudang FIFO</a>
    <div class="d-flex align-items-center">
      <span class="text-white me-3">
        <?php echo $_SESSION['username']; ?> (<?php echo $_SESSION['level']; ?>)
      </span>
      <form action ="../auth/logout.php" method="post" class="d-inline">
        <button type="submit" class="btn btn-danger btn-sm">Logout</button>
      </form>
    </div>
  </div>
</nav>
