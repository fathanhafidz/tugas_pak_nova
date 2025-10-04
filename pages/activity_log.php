<?php
include '../config/koneksi.php';
include '../config/base_url.php';
session_start();

// Pagination
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Filter
$filterUser = isset($_GET['user']) ? $_GET['user'] : '';
$filterAktivitas = isset($_GET['aktivitas']) ? $_GET['aktivitas'] : '';
$filterStartDate = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$filterEndDate = isset($_GET['end_date']) ? $_GET['end_date'] : '';

$where = [];
if ($filterUser != '') {
    $where[] = "l.id_users = " . intval($filterUser);
}
if ($filterAktivitas != '') {
    $where[] = "l.aktivitas LIKE '%" . $koneksi->real_escape_string($filterAktivitas) . "%'";
}
if ($filterStartDate != '' && $filterEndDate != '') {
    $where[] = "DATE(l.waktu) BETWEEN '" . $koneksi->real_escape_string($filterStartDate) . "' 
                AND '" . $koneksi->real_escape_string($filterEndDate) . "'";
} elseif ($filterStartDate != '') {
    $where[] = "DATE(l.waktu) >= '" . $koneksi->real_escape_string($filterStartDate) . "'";
} elseif ($filterEndDate != '') {
    $where[] = "DATE(l.waktu) <= '" . $koneksi->real_escape_string($filterEndDate) . "'";
}
$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

// Hitung total data
$resultTotal = $koneksi->query("SELECT COUNT(*) as total FROM activity_log l $whereSQL");
$totalData = $resultTotal->fetch_assoc()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data log
$query = "
    SELECT l.*, u.username 
    FROM activity_log l 
    LEFT JOIN users u ON l.id_users = u.id_users 
    $whereSQL
    ORDER BY l.waktu DESC 
    LIMIT $limit OFFSET $offset
";
$result = $koneksi->query($query);

// Ambil semua user & aktivitas untuk filter
$users = $koneksi->query("SELECT id_users, username FROM users ORDER BY username ASC");
$aktivitasList = $koneksi->query("SELECT DISTINCT aktivitas FROM activity_log ORDER BY aktivitas ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Activity Log</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <style>
    body { padding-top: 56px; padding-left: 250px; }
  </style>
</head>
<body>

<?php include '../includes/appbar.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<div class="container-fluid p-4">
  <h3>📑 Activity Log</h3>

  <!-- Tombol Export & Hapus -->
  <div class="mb-3">
    <a href="../backend/activity_log_proses.php?aksi=export" class="btn btn-success">Export Excel</a>
    <a href="../backend/activity_log_proses.php?aksi=hapus_semua" class="btn btn-danger"
   onclick="return confirm('Yakin hapus semua log?')">Hapus Semua</a>

  </div>

  <!-- Filter -->
  <form method="GET" class="row g-3 mb-3">
      <div class="col-md-2">
          <select name="user" class="form-select">
              <option value="">-- Semua User --</option>
              <?php while ($u = $users->fetch_assoc()): ?>
                  <option value="<?= $u['id_users'] ?>" <?= ($filterUser == $u['id_users']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($u['username']); ?>
                  </option>
              <?php endwhile; ?>
          </select>
      </div>
      <div class="col-md-2">
          <select name="aktivitas" class="form-select">
              <option value="">-- Semua Aktivitas --</option>
              <?php while ($a = $aktivitasList->fetch_assoc()): ?>
                  <option value="<?= htmlspecialchars($a['aktivitas']); ?>" <?= ($filterAktivitas == $a['aktivitas']) ? 'selected' : '' ?>>
                      <?= htmlspecialchars($a['aktivitas']); ?>
                  </option>
              <?php endwhile; ?>
          </select>
      </div>
      <div class="col-md-2">
          <input type="date" name="start_date" value="<?= $filterStartDate ?>" class="form-control">
      </div>
      <div class="col-md-2">
          <input type="date" name="end_date" value="<?= $filterEndDate ?>" class="form-control">
      </div>
      <div class="col-md-1">
          <button type="submit" class="btn btn-primary">Filter</button>
      </div>
      <div class="col-md-2">
          <a href="activity_log.php" class="btn btn-secondary">Reset</a>
      </div>
  </form>

  <!-- Form hapus per tanggal -->
  <form method="POST" action="../backend/activity_log_proses.php" class="row g-3 mb-3">
      <div class="col-md-3">
          <input type="date" name="hapus_tanggal" class="form-control" required>
      </div>
      <div class="col-md-3">
          <button type="submit" name="hapus_per_tanggal" class="btn btn-warning">Hapus Per Tanggal</button>
      </div>
  </form>

  <!-- Form hapus sebelum tanggal -->
  <form method="POST" action="../backend/activity_log_proses.php" class="row g-3 mb-3">
      <div class="col-md-3">
          <input type="date" name="hapus_sebelum_tanggal" class="form-control" required>
      </div>
      <div class="col-md-3">
          <button type="submit" name="hapus_sebelum" class="btn btn-danger">Hapus Sebelum Tanggal</button>
      </div>
  </form>

  <!-- Tabel Log -->
  <div class="card">
   <div class="card-header bg-dark text-white">Daftar log</div>
    <div class="card-body">
    <table class="table table-bordered table-hover">
      <thead class="table-light">
        <tr>
          <th>No</th>
          <th>User</th>
          <th>Aktivitas</th>
          <th>Tabel</th>
          <th>ID Data</th>
          <th>Waktu</th>
        </tr>
      </thead>
     <tbody>
  <?php if ($result->num_rows > 0): ?>
    <?php $no = $offset + 1; while ($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?= $no++; ?></td>
      <td><?= htmlspecialchars($row['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?= htmlspecialchars($row['aktivitas'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?= htmlspecialchars($row['tabel'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?= htmlspecialchars($row['id_data'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
      <td><?= htmlspecialchars($row['waktu'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
    </tr>
    <?php endwhile; ?>
  <?php else: ?>
    <tr><td colspan="6" class="text-center">Tidak ada log</td></tr>
  <?php endif; ?>
</tbody>

    </table>
    </div>
  </div>
  </div>

  <!-- Pagination -->
  <nav>
    <ul class="pagination p-4">
      <?php if ($page > 1): ?>
        <li class="page-item"><a class="page-link" href="?page=1&user=<?= $filterUser ?>&aktivitas=<?= $filterAktivitas ?>&start_date=<?= $filterStartDate ?>&end_date=<?= $filterEndDate ?>">⏮ First</a></li>
        <li class="page-item"><a class="page-link" href="?page=<?= $page-1 ?>&user=<?= $filterUser ?>&aktivitas=<?= $filterAktivitas ?>&start_date=<?= $filterStartDate ?>&end_date=<?= $filterEndDate ?>">⬅ Prev</a></li>
      <?php endif; ?>

      <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
          <a class="page-link" href="?page=<?= $i ?>&user=<?= $filterUser ?>&aktivitas=<?= $filterAktivitas ?>&start_date=<?= $filterStartDate ?>&end_date=<?= $filterEndDate ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>

      <?php if ($page < $totalPages): ?>
        <li class="page-item"><a class="page-link" href="?page=<?= $page+1 ?>&user=<?= $filterUser ?>&aktivitas=<?= $filterAktivitas ?>&start_date=<?= $filterStartDate ?>&end_date=<?= $filterEndDate ?>">Next ➡</a></li>
        <li class="page-item"><a class="page-link" href="?page=<?= $totalPages ?>&user=<?= $filterUser ?>&aktivitas=<?= $filterAktivitas ?>&start_date=<?= $filterStartDate ?>&end_date=<?= $filterEndDate ?>">Last ⏭</a></li>
      <?php endif; ?>
    </ul>
  </nav>

</div>
</body>
</html>
