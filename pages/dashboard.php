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
      background: #ede7f6; /* sama seperti barang.php */
      color: #fff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    h2 {
      color: #222;
      font-weight: 600;
    }

    .card {
      background: #2a2a40;
      border: none;
      border-radius: 12px;
      color: #fff;
      box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }

    .card-header {
      border-radius: 12px 12px 0 0;
      font-weight: 600;
      border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .card-body h6 {
      font-size: 0.9rem;
      color: #ddd;
    }

    .card-body h4 {
      font-size: 1.4rem;
      font-weight: bold;
      color: #fff;
    }

    /* Warna khusus summary */
    .bg-gradient-primary   { background: linear-gradient(90deg,#6a11cb,#2575fc); }
    .bg-gradient-success   { background: linear-gradient(90deg,#11998e,#38ef7d); }
    .bg-gradient-warning   { background: linear-gradient(90deg,#f7971e,#ffd200); color:#222; }
    .bg-gradient-info      { background: linear-gradient(90deg,#00c6ff,#0072ff); }
    .bg-gradient-danger    { background: linear-gradient(90deg,#ff416c,#ff4b2b); }
    .bg-gradient-secondary { background: linear-gradient(90deg,#757f9a,#d7dde8); color:#222; }

    /* Tabel dark elegan */
    .table {
      color: #fff;
      vertical-align: middle;
    }
    .table thead {
      background: #34344e;
    }
    .table thead th {
      color: #ddd;
    }
    .table-striped > tbody > tr:nth-of-type(odd) {
      background-color: rgba(255,255,255,0.05);
    }
  </style>
</head>
<body>
  <div class="container-fluid p-4">
    <h2 class="mb-4">📊 Dashboard</h2>

    <!-- Summary Card -->
    <div class="row mb-4 g-3">
      <div class="col-md-2">
        <div class="card shadow bg-gradient-primary">
          <div class="card-body">
            <h6>Total Barang</h6>
            <h4 id="totalBarang">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card shadow bg-gradient-primary">
          <div class="card-body">
            <h6>Total Kategori</h6>
            <h4 id="totalKategori">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card shadow bg-gradient-primary">
          <div class="card-body">
            <h6>Total Supplier</h6>
            <h4 id="totalSupplier">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card shadow bg-gradient-primary">
          <div class="card-body">
            <h6>Barang Masuk Bulan Ini</h6>
            <h4 id="barangMasukBulan">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card shadow bg-gradient-primary">
          <div class="card-body">
            <h6>Barang Keluar Bulan Ini</h6>
            <h4 id="barangKeluarBulan">0</h4>
          </div>
        </div>
      </div>
      <div class="col-md-2">
        <div class="card shadow bg-gradient-primary">
          <div class="card-body">
            <h6>Total Laba Bulan Ini</h6>
            <h4 id="labaBulan">0</h4>
          </div>
        </div>
      </div>
    </div>

    <!-- Grafik -->
    <div class="row mb-4 g-3">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header">📈 Barang Masuk vs Keluar</div>
          <div class="card-body">
            <canvas id="grafikMasukKeluar"></canvas>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-header">💰 Laba per Bulan</div>
          <div class="card-body">
            <canvas id="grafikLaba"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel Ringkas -->
    <div class="row g-3">
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-header bg-gradient-info">Barang Masuk Terbaru</div>
          <div class="card-body table-responsive bg-white">
            <table class="table table-sm table-striped" id="tableMasuk"></table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-header bg-gradient-info">Barang Keluar Terbaru</div>
          <div class="card-body table-responsive bg-white">
            <table class="table table-sm table-striped" id="tableKeluar"></table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow">
          <div class="card-header bg-gradient-info">Aktivitas Terakhir</div>
          <div class="card-body table-responsive bg-white">
            <table class="table table-sm table-striped" id="tableLog"></table>
          </div>
        </div>
      </div>
    </div>
  </div>

<script>
   const base_url = "<?php echo $base_url; ?>";
fetch(`${base_url}/backend/dashboard_proses.php`)
  .then(res => res.json())
  .then(data => {
    // Summary
    document.getElementById('totalBarang').innerText = data.summary.total_barang;
    document.getElementById('totalKategori').innerText = data.summary.total_kategori;
    document.getElementById('totalSupplier').innerText = data.summary.total_supplier;
    document.getElementById('barangMasukBulan').innerText = data.summary.barang_masuk_bulan;
    document.getElementById('barangKeluarBulan').innerText = data.summary.barang_keluar_bulan;
    document.getElementById('labaBulan').innerText = data.summary.laba_bulan;

    // Grafik Masuk vs Keluar
    new Chart(document.getElementById('grafikMasukKeluar'), {
      type: 'line',
      data: {
        labels: data.grafik.map(item => item.bulan),
        datasets: [
          { label: 'Masuk', data: data.grafik.map(item => item.masuk), borderColor: '#00c6ff', backgroundColor:'#00c6ff', fill: false },
          { label: 'Keluar', data: data.grafik.map(item => item.keluar), borderColor: '#ff416c', backgroundColor:'#ff416c', fill: false }
        ]
      },
      options: { plugins:{ legend:{ labels:{ color:'#fff' } } }, scales:{ x:{ ticks:{color:'#fff'}}, y:{ticks:{color:'#fff'}} } }
    });

    // Grafik Laba
    new Chart(document.getElementById('grafikLaba'), {
      type: 'bar',
      data: {
        labels: data.grafik_laba.map(item => item.bulan),
        datasets: [{
          label: 'Laba',
          data: data.grafik_laba.map(item => item.laba),
          backgroundColor: 'rgba(75,192,192,0.7)'
        }]
      },
      options: { plugins:{ legend:{ labels:{ color:'#fff' } } }, scales:{ x:{ ticks:{color:'#fff'}}, y:{ticks:{color:'#fff'}} } }
    });

  // Tabel Masuk
let masukHTML = "<tr><th>Waktu Masuk</th><th>Barang</th><th>Jumlah</th></tr>";
data.masuk_terbaru.forEach(row => {
  masukHTML += `<tr><td>${row.waktu_masuk}</td><td>${row.nama_barang}</td><td>${row.jumlah}</td></tr>`;
});
document.getElementById('tableMasuk').innerHTML = masukHTML;

// Tabel Keluar
let keluarHTML = "<tr><th>Waktu Keluar</th><th>Barang</th><th>Jumlah</th></tr>";
data.keluar_terbaru.forEach(row => {
  keluarHTML += `<tr><td>${row.waktu_keluar}</td><td>${row.nama_barang}</td><td>${row.jumlah}</td></tr>`;
});
document.getElementById('tableKeluar').innerHTML = keluarHTML;


    // Tabel Log
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
