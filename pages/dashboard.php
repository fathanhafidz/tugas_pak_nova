<?php
session_start();
include '../config/koneksi.php';
include '../config/base_url.php';

// cek login
if (!isset($_SESSION['id_users'])) {
    header("Location: $base_url/pages/login.php");
    exit;
}

include '../includes/appbar.php';
include '../includes/sidebar.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      padding-top: 56px;
      padding-left: 250px;
    }
  </style>
</head>
<body>
  <div class="container-fluid p-4">
    <h2 class="mb-4">Dashboard</h2>

    <!-- Summary Card -->
    <div class="row mb-4">
      <div class="col-md-2">
        <div class="card text-bg-primary shadow">
          <div class="card-body">
            <h6>Total Barang</h6>
            <h4 id="totalBarang">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-bg-success shadow">
          <div class="card-body">
            <h6>Total Kategori</h6>
            <h4 id="totalKategori">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-bg-warning shadow">
          <div class="card-body">
            <h6>Total Supplier</h6>
            <h4 id="totalSupplier">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-bg-info shadow">
          <div class="card-body">
            <h6>Barang Masuk Bulan Ini</h6>
            <h4 id="barangMasukBulan">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-bg-secondary shadow">
          <div class="card-body">
            <h6>Barang Keluar Bulan Ini</h6>
            <h4 id="barangKeluarBulan">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card text-bg-danger shadow">
          <div class="card-body">
            <h6>Total Laba Bulan Ini</h6>
            <h4 id="labaBulan">0</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Grafik -->
    <div class="row mb-4">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header bg-dark text-white">Barang Masuk vs Keluar</div>
          <div class="card-body">
            <canvas id="grafikMasukKeluar"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header bg-dark text-white">Laba per Bulan</div>
          <div class="card-body">
            <canvas id="grafikLaba"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Ringkas -->
    <div class="row">
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-header bg-primary text-white">Barang Masuk Terbaru</div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped" id="tableMasuk"></table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-header bg-success text-white">Barang Keluar Terbaru</div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped" id="tableKeluar"></table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-header bg-secondary text-white">Aktivitas Terakhir</div>
          <div class="card-body table-responsive">
            <table class="table table-sm table-striped" id="tableLog"></table>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
fetch("../backend/dashboard_proses.php")
  .then(res => res.json())
  .then(data => {
    // ===== Summary =====
    document.getElementById('totalBarang').innerText = data.summary.total_barang;
    document.getElementById('totalKategori').innerText = data.summary.total_kategori;
    document.getElementById('totalSupplier').innerText = data.summary.total_supplier;
    document.getElementById('barangMasukBulan').innerText = data.summary.barang_masuk_bulan;
    document.getElementById('barangKeluarBulan').innerText = data.summary.barang_keluar_bulan;
    document.getElementById('labaBulan').innerText = data.summary.laba_bulan;

    // ===== Grafik Masuk vs Keluar =====
    new Chart(document.getElementById('grafikMasukKeluar'), {
      type: 'line',
      data: {
        labels: data.grafik.map(item => item.bulan),
        datasets: [
          { label: 'Masuk', data: data.grafik.map(item => item.masuk), borderColor: 'blue', fill: false },
          { label: 'Keluar', data: data.grafik.map(item => item.keluar), borderColor: 'red', fill: false }
        ]
      }
    });

    // ===== Grafik Laba =====
    new Chart(document.getElementById('grafikLaba'), {
      type: 'bar',
      data: {
        labels: data.grafik_laba.map(item => item.bulan),
        datasets: [{
          label: 'Laba',
          data: data.grafik_laba.map(item => item.laba),
          backgroundColor: 'rgba(75,192,192,0.7)'
        }]
      }
    });

    // ===== Tabel Masuk =====
    let masukHTML = "<tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th></tr>";
    data.masuk_terbaru.forEach(row => {
      masukHTML += `<tr><td>${row.tanggal_masuk}</td><td>${row.nama_barang}</td><td>${row.jumlah}</td></tr>`;
    });
    document.getElementById('tableMasuk').innerHTML = masukHTML;

    // ===== Tabel Keluar =====
    let keluarHTML = "<tr><th>Tanggal</th><th>Barang</th><th>Jumlah</th></tr>";
    data.keluar_terbaru.forEach(row => {
      keluarHTML += `<tr><td>${row.tanggal_keluar}</td><td>${row.nama_barang ?? 'N/A'}</td><td>${row.jumlah_item ?? 0}</td></tr>`;
    });
    document.getElementById('tableKeluar').innerHTML = keluarHTML;

    // ===== Tabel Log =====
    let logHTML = "<tr><th>Tanggal</th><th>User</th><th>Aktivitas</th></tr>";
    data.log_terbaru.forEach(row => {
      logHTML += `<tr><td>${row.waktu}</td><td>${row.username}</td><td>${row.aktivitas}</td></tr>`;
    });
    document.getElementById('tableLog').innerHTML = logHTML;
  })
  .catch(err => console.error("Error fetching dashboard data:", err));
</script>

</body>
</html>
