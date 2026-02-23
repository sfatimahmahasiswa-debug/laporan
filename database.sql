-- Database: inventori_klinik_harmy_medika
CREATE DATABASE IF NOT EXISTS inventori_klinik_harmy_medika
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE inventori_klinik_harmy_medika;

-- Tabel pengguna
CREATE TABLE IF NOT EXISTS pengguna (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  peran ENUM('admin','petugas') NOT NULL DEFAULT 'petugas',
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel kategori obat
CREATE TABLE IF NOT EXISTS kategori (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(100) NOT NULL,
  keterangan TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel obat / barang
CREATE TABLE IF NOT EXISTS obat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_obat VARCHAR(20) NOT NULL UNIQUE,
  nama VARCHAR(150) NOT NULL,
  kategori_id INT,
  satuan VARCHAR(30) NOT NULL,
  stok INT NOT NULL DEFAULT 0,
  stok_minimum INT NOT NULL DEFAULT 5,
  harga_beli DECIMAL(12,2) NOT NULL DEFAULT 0,
  harga_jual DECIMAL(12,2) NOT NULL DEFAULT 0,
  tanggal_kadaluarsa DATE,
  keterangan TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (kategori_id) REFERENCES kategori(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabel supplier
CREATE TABLE IF NOT EXISTS supplier (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nama VARCHAR(150) NOT NULL,
  alamat TEXT,
  telepon VARCHAR(20),
  email VARCHAR(100),
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabel stok masuk
CREATE TABLE IF NOT EXISTS stok_masuk (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_transaksi VARCHAR(30) NOT NULL UNIQUE,
  obat_id INT NOT NULL,
  supplier_id INT,
  jumlah INT NOT NULL,
  harga_beli DECIMAL(12,2) NOT NULL,
  tanggal DATE NOT NULL,
  pengguna_id INT,
  keterangan TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (obat_id) REFERENCES obat(id) ON DELETE CASCADE,
  FOREIGN KEY (supplier_id) REFERENCES supplier(id) ON DELETE SET NULL,
  FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Tabel stok keluar
CREATE TABLE IF NOT EXISTS stok_keluar (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode_transaksi VARCHAR(30) NOT NULL UNIQUE,
  obat_id INT NOT NULL,
  jumlah INT NOT NULL,
  harga_jual DECIMAL(12,2) NOT NULL,
  tanggal DATE NOT NULL,
  pengguna_id INT,
  keterangan TEXT,
  dibuat_pada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (obat_id) REFERENCES obat(id) ON DELETE CASCADE,
  FOREIGN KEY (pengguna_id) REFERENCES pengguna(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Data awal: pengguna admin (password: admin123)
INSERT INTO pengguna (nama, username, password, peran) VALUES
('Administrator', 'admin', '$2y$10$alqAsjYoBJhihEGSl8eT4umu9Ax8rS1zE3iExVcVVIUWwSIfpIZbu', 'admin');

-- Data awal: kategori
INSERT INTO kategori (nama, keterangan) VALUES
('Antibiotik', 'Obat untuk melawan infeksi bakteri'),
('Analgesik', 'Obat pereda nyeri'),
('Vitamin & Suplemen', 'Vitamin dan suplemen kesehatan'),
('Antiseptik', 'Obat atau cairan antiseptik'),
('Alat Kesehatan', 'Peralatan medis dan kesehatan');

-- Data awal: supplier
INSERT INTO supplier (nama, alamat, telepon, email) VALUES
('PT. Kimia Farma', 'Jl. Veteran No. 9, Jakarta', '021-3847-0007', 'info@kimiafarma.co.id'),
('PT. Kalbe Farma', 'Jl. Let. Jend. Suprapto Kav. 4, Jakarta', '021-4287-3888', 'info@kalbe.co.id'),
('Apotek Sumber Sehat', 'Jl. Merdeka No. 12, Lokal', '0812-3456-7890', 'apoteksumbersehat@email.com');
