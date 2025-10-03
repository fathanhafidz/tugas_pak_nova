<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

$aksi = $_POST['aksi'] ?? $_GET['aksi'] ?? null;

// =========================
// TAMBAH USER
// =========================
if ($aksi == 'tambah') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $level    = $_POST['level'] ?? '';

    if ($username && $password && $level) {
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
        $new_id = $stmt->insert_id;
        $stmt->close();

        // catat log
        $id_users = $_SESSION['id_users'];
        $aktivitas = "Menambah user baru ($username)";
        $log = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, 'users', ?)");
        $log->bind_param("isi", $id_users, $aktivitas, $new_id);
        $log->execute();
        $log->close();

        header("Location: $base_url/pages/manajemen_users.php?pesan=User berhasil ditambahkan");
    } else {
        header("Location: $base_url/pages/manajemen_users.php?pesan=Input tidak lengkap");
    }
    exit;
}

// =========================
// EDIT USER
// =========================
if ($aksi == 'edit') {
    $id_user_edit = (int) ($_POST['id_users'] ?? 0);
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $level    = $_POST['level'] ?? '';

    if ($id_user_edit && $username && $level) {
        // cek username duplikat kecuali milik user yg sedang diedit
        $cek = $koneksi->prepare("SELECT id_users FROM users WHERE username=? AND id_users<>?");
        $cek->bind_param("si", $username, $id_user_edit);
        $cek->execute();
        $cek->store_result();
        if ($cek->num_rows > 0) {
            $cek->close();
            header("Location: $base_url/pages/manajemen_users.php?pesan=Username sudah digunakan");
            exit;
        }
        $cek->close();

        if ($password) {
            if (strlen($password) < 6) {
                header("Location: $base_url/pages/manajemen_users.php?pesan=Password minimal 6 karakter");
                exit;
            }
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $koneksi->prepare("UPDATE users SET username=?, password=?, level=? WHERE id_users=?");
            $stmt->bind_param("sssi", $username, $hash, $level, $id_user_edit);
        } else {
            $stmt = $koneksi->prepare("UPDATE users SET username=?, level=? WHERE id_users=?");
            $stmt->bind_param("ssi", $username, $level, $id_user_edit);
        }
        $stmt->execute();
        $stmt->close();

        // catat log
        $id_users = $_SESSION['id_users'];
        $aktivitas = "Mengedit user ID $id_user_edit ($username)";
        $log = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, 'users', ?)");
        $log->bind_param("isi", $id_users, $aktivitas, $id_user_edit);
        $log->execute();
        $log->close();

        header("Location: $base_url/pages/manajemen_users.php?pesan=User berhasil diupdate");
    } else {
        header("Location: $base_url/pages/manajemen_users.php?pesan=Input tidak lengkap untuk edit");
    }
    exit;
}

// =========================
// HAPUS USER
// =========================
if ($aksi == 'hapus' && isset($_GET['id'])) {
    $id_hapus = (int) $_GET['id'];

    // ambil username dulu buat log
    $res = $koneksi->query("SELECT username FROM users WHERE id_users=$id_hapus");
    $userData = $res->fetch_assoc();
    $username_hapus = $userData['username'] ?? '';

    $koneksi->query("DELETE FROM users WHERE id_users=$id_hapus");

    // catat log
    $id_users = $_SESSION['id_users'];
    $aktivitas = "Menghapus user ID $id_hapus ($username_hapus)";
    $log = $koneksi->prepare("INSERT INTO activity_log (id_users, aktivitas, tabel, id_data) VALUES (?, ?, 'users', ?)");
    $log->bind_param("isi", $id_users, $aktivitas, $id_hapus);
    $log->execute();
    $log->close();

    header("Location: $base_url/pages/manajemen_users.php?pesan=User berhasil dihapus");
    exit;
}

header("Location: $base_url/pages/manajemen_users.php?pesan=Aksi tidak valid");
exit;
