<?php
session_start();
include '../config/base_url.php';

// cek login
if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

// include layout
include '../includes/appbar.php';
include '../includes/sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

  <style>
    body {
      padding-top: 56px;   /* supaya konten tidak ketiban navbar */
      padding-left: 250px; /* supaya konten tidak ketiban sidebar */
    }
  </style>
</head>
<body>
  <div class="container-fluid p-4">
    <h2>Selamat datang, <?php echo $_SESSION['username']; ?></h2>
    <p>Level Anda: <?php echo $_SESSION['level']; ?></p>

    <p style="height:2000px;">⬇️ Scroll ke bawah untuk tes... Sidebar & Appbar tetap di tempat.</p>
  </div>
</body>
</html>
