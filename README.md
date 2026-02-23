# Sistem Inventori Klinik Harmy Medika

**Laporan Skripsi – Perancangan Sistem Inventori Klinik Harmy Medika Berbasis Web Menggunakan Metode Rapid Application Development (RAD)**

Sistem inventori berbasis web untuk mengelola stok obat dan barang medis di Klinik Harmy Medika.

---

## Fitur Utama

| Modul | Deskripsi |
|---|---|
| **Autentikasi** | Login & logout pengguna |
| **Dashboard** | Ringkasan stok, stok hampir habis, transaksi terbaru |
| **Data Obat** | CRUD data obat (kode, nama, kategori, satuan, harga, stok minimum) |
| **Stok Masuk** | Pencatatan penerimaan barang dari supplier, update stok otomatis |
| **Stok Keluar** | Pencatatan pengeluaran barang, validasi ketersediaan stok |
| **Kategori** | Manajemen kategori obat |
| **Supplier** | Manajemen data supplier |
| **Laporan** | Laporan stok, laporan masuk & keluar per periode, cetak |

---

## Teknologi

- **Backend:** PHP 8.x
- **Database:** MySQL 5.7+ / MariaDB 10.x
- **Frontend:** Bootstrap 5.3, Bootstrap Icons
- **Metode Pengembangan:** Rapid Application Development (RAD)

---

## Instalasi

### Prasyarat
- Web server (Apache/Nginx) dengan PHP 8.x
- MySQL 5.7+ atau MariaDB 10.x

### Langkah-langkah

1. **Clone atau salin** seluruh folder ke direktori web server Anda (misal: `htdocs/inventori` untuk XAMPP).

2. **Buat database** dengan mengimpor file SQL:
   ```bash
   mysql -u root -p < database.sql
   ```
   Atau melalui phpMyAdmin: impor file `database.sql`.

3. **Konfigurasi koneksi database** di `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');      // sesuaikan
   define('DB_PASS', '');          // sesuaikan
   define('DB_NAME', 'inventori_klinik_harmy_medika');
   ```

4. **Akses aplikasi** melalui browser:
   ```
   http://localhost/inventori/
   ```

5. **Login** dengan akun default:
   - Username: `admin`
   - Password: `admin123`

   > **Penting:** Segera ubah password setelah login pertama.

---

## Struktur Direktori

```
inventori/
├── index.php               # Redirect ke login
├── login.php               # Halaman login
├── logout.php              # Proses logout
├── dashboard.php           # Halaman dashboard
├── database.sql            # Skema & data awal database
├── config/
│   └── database.php        # Konfigurasi koneksi DB & konstanta
├── assets/
│   ├── css/style.css       # Custom stylesheet
│   └── js/main.js          # Custom JavaScript
├── includes/
│   ├── header.php          # HTML head + guard login
│   ├── sidebar.php         # Navigasi sidebar
│   └── footer.php          # HTML closing tags
└── modules/
    ├── obat/               # Manajemen data obat
    ├── stok_masuk/         # Stok masuk
    ├── stok_keluar/        # Stok keluar
    ├── kategori/           # Manajemen kategori
    ├── supplier/           # Manajemen supplier
    └── laporan/            # Laporan & cetak
```

---

## Lisensi

Proyek ini dibuat untuk keperluan skripsi. Hak cipta © 2024 Klinik Harmy Medika.
