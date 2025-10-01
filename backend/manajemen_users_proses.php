<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? null;

if ($aksi == 'tambah') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $level    = $_POST['level'] ?? '';

    if ($username && $password && $level) {
        // validasi panjang password
        if (strlen($password) < 6) {
            header("Location: $base_url/pages/manajemen_users.php?pesan=Password minimal 6 karakter");
            exit;
        }

        // cek username duplikat
        $cek = $koneksi->prepare("SELECT id_users FROM users WHERE username=?");
        $cek->bind_param("s", $username);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $cek->close();
            header("Location: $base_url/pages/manajemen_users.php?pesan=Username sudah digunakan");
            exit;
        }
        $cek->close();

        // insert user baru
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $koneksi->prepare("INSERT INTO users (username, password, level) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $hash, $level);
        $stmt->execute();
        $stmt->close();

        header("Location: $base_url/pages/manajemen_users.php?pesan=User berhasil ditambahkan");
    } else {
        header("Location: $base_url/pages/manajemen_users.php?pesan=Input tidak lengkap");
    }
    exit;
}

if ($aksi == 'edit') {
    $id_users = (int) ($_POST['id_users'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $level    = $_POST['level'] ?? '';

    if ($id_users && $username && $level) {
        // cek username duplikat (kecuali milik user yg sedang diedit)
        $cek = $koneksi->prepare("SELECT id_users FROM users WHERE username=? AND id_users<>?");
        $cek->bind_param("si", $username, $id_users);
        $cek->execute();
        $cek->store_result();

        if ($cek->num_rows > 0) {
            $cek->close();
            header("Location: $base_url/pages/manajemen_users.php?pesan=Username sudah digunakan");
            exit;
        }
        $cek->close();

        if ($password) {
            // validasi panjang password baru
            if (strlen($password) < 6) {
                header("Location: $base_url/pages/manajemen_users.php?pesan=Password minimal 6 karakter");
                exit;
            }

            // update dengan password baru
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $koneksi->prepare("UPDATE users SET username=?, password=?, level=? WHERE id_users=?");
            $stmt->bind_param("sssi", $username, $hash, $level, $id_users);
        } else {
            // update tanpa ubah password
            $stmt = $koneksi->prepare("UPDATE users SET username=?, level=? WHERE id_users=?");
            $stmt->bind_param("ssi", $username, $level, $id_users);
        }
        $stmt->execute();
        $stmt->close();

        header("Location: $base_url/pages/manajemen_users.php?pesan=User berhasil diupdate");
    } else {
        header("Location: $base_url/pages/manajemen_users.php?pesan=Input tidak lengkap untuk edit");
    }
    exit;
}

if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $koneksi->query("DELETE FROM users WHERE id_users=$id");
    header("Location: $base_url/pages/manajemen_users.php?pesan=User berhasil dihapus");
    exit;
}

header("Location: $base_url/pages/manajemen_users.php?pesan=Aksi tidak valid");
exit;
