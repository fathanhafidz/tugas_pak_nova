-- =============================
-- DATABASE: manajemen_gudang_fifo
-- =============================
CREATE DATABASE IF NOT EXISTS manajemen_gudang_fifo;
USE manajemen_gudang_fifo;

-- =============================
-- TABEL USERS (LOGIN USER)
-- =============================
CREATE TABLE users (
    id_users INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    level ENUM('admin', 'operator_masuk', 'operator_keluar') NOT NULL
);

-- =============================
-- TABEL KATEGORI
-- =============================
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL
);

-- =============================
-- TABEL SUPPLIER
-- =============================
CREATE TABLE supplier (
    id_supplier INT AUTO_INCREMENT PRIMARY KEY,
    nama_supplier VARCHAR(100) NOT NULL,
    alamat TEXT,
    telepon VARCHAR(20)
);

-- =============================
-- TABEL BARANG
-- =============================
CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    id_kategori INT,
    id_supplier INT,
    harga_jual DECIMAL(15,2),
    FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori),
    FOREIGN KEY (id_supplier) REFERENCES supplier(id_supplier)
);

-- =============================
-- TABEL BARANG MASUK
-- (menyimpan harga beli per batch + jumlah_sisa)
-- =============================
CREATE TABLE barang_masuk (
    id_masuk INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    tanggal_masuk DATE NOT NULL,
    waktu_masuk TIME NOT NULL,
    jumlah INT NOT NULL,
    harga_beli_satuan DECIMAL(15,2) NOT NULL,
    jumlah_sisa INT NOT NULL, -- stok sisa batch ini
    keterangan TEXT,
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
);

-- =============================
-- TABEL BARANG KELUAR (header transaksi keluar)
-- =============================
CREATE TABLE barang_keluar (
    id_keluar INT AUTO_INCREMENT PRIMARY KEY,
    tanggal_keluar DATE NOT NULL,
    waktu_keluar TIME NOT NULL,
    tujuan VARCHAR(100),
    keterangan TEXT
);

-- =============================
-- TABEL BARANG KELUAR DETAIL (FIFO)
-- menyimpan barang keluar per batch masuk
-- =============================
CREATE TABLE barang_keluar_detail (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    id_keluar INT NOT NULL,
    id_barang INT NOT NULL,
    id_masuk INT NOT NULL, -- batch asal barang masuk
    jumlah INT NOT NULL,
    harga_beli_satuan DECIMAL(15,2) NOT NULL,
    harga_jual_satuan DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_keluar) REFERENCES barang_keluar(id_keluar),
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang),
    FOREIGN KEY (id_masuk) REFERENCES barang_masuk(id_masuk)
);

-- =============================
-- TABEL ACTIVITY LOG
-- menyimpan aktivitas user
-- =============================
CREATE TABLE activity_log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_users INT,
    aktivitas VARCHAR(255) NOT NULL, -- deskripsi aktivitas
    tabel VARCHAR(100),              -- tabel yang terpengaruh
    id_data INT,                     -- id data yang diubah/dihapus/dibuat
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_users) REFERENCES users(id_users)
);
