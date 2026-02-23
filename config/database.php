<?php
// Konfigurasi koneksi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'inventori_klinik_harmy_medika');

$koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($koneksi->connect_error) {
    die('<div style="font-family:sans-serif;color:red;padding:20px;">
        Koneksi database gagal: ' . htmlspecialchars($koneksi->connect_error) . '
        <br>Pastikan server MySQL aktif dan database sudah dibuat menggunakan file <code>database.sql</code>.
    </div>');
}

$koneksi->set_charset('utf8mb4');

// Konstanta aplikasi
define('APP_NAME', 'Sistem Inventori Klinik Harmy Medika');
define('APP_VERSION', '1.0.0');
