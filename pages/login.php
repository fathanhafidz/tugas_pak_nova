<?php
include '../config/base_url.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">

<div class="container mt-5">
  <div class="row justify-content-center">
    <div class="col-md-5">
      <div class="card shadow-lg">
        <div class="card-header text-center bg-primary text-white">
          <h4>Login</h4>
        </div>
        <div class="card-body">
          <form action="../auth/login_proses.php" method="post">
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" name="username" id="username" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
          </form>

          <?php if (isset($_GET['pesan']) && $_GET['pesan'] == 'gagal') { ?>
            <div class="alert alert-danger mt-3" role="alert">
              Username atau Password salah!
            </div>
          <?php } ?>
        </div>
        <div class="card-footer text-center">
          <p class="mb-0">Belum punya akun? <a href="./register.php">Daftar di sini</a></p>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>
