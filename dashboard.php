<?php
$depth      = 0;
$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';
require_once 'includes/header.php';
require_once 'config/database.php';

// --- Statistik ringkasan ---
$totalObat     = $koneksi->query('SELECT COUNT(*) c FROM obat')->fetch_assoc()['c'];
$stokRendah    = $koneksi->query('SELECT COUNT(*) c FROM obat WHERE stok <= stok_minimum')->fetch_assoc()['c'];
$masukBulanIni = $koneksi->query("SELECT COALESCE(SUM(jumlah),0) c FROM stok_masuk WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())")->fetch_assoc()['c'];
$keluarBulanIni= $koneksi->query("SELECT COALESCE(SUM(jumlah),0) c FROM stok_keluar WHERE MONTH(tanggal)=MONTH(CURDATE()) AND YEAR(tanggal)=YEAR(CURDATE())")->fetch_assoc()['c'];

// --- 5 obat stok rendah ---
$qRendah = $koneksi->query('SELECT o.kode_obat, o.nama, o.stok, o.stok_minimum, k.nama AS kategori
    FROM obat o LEFT JOIN kategori k ON o.kategori_id = k.id
    WHERE o.stok <= o.stok_minimum ORDER BY o.stok ASC LIMIT 5');

// --- 5 transaksi masuk terbaru ---
$qMasuk = $koneksi->query('SELECT sm.kode_transaksi, o.nama AS obat, sm.jumlah, sm.tanggal, s.nama AS supplier
    FROM stok_masuk sm
    JOIN obat o ON sm.obat_id = o.id
    LEFT JOIN supplier s ON sm.supplier_id = s.id
    ORDER BY sm.dibuat_pada DESC LIMIT 5');

// --- 5 transaksi keluar terbaru ---
$qKeluar = $koneksi->query('SELECT sk.kode_transaksi, o.nama AS obat, sk.jumlah, sk.tanggal
    FROM stok_keluar sk
    JOIN obat o ON sk.obat_id = o.id
    ORDER BY sk.dibuat_pada DESC LIMIT 5');
?>

<?php require_once 'includes/sidebar.php'; ?>

<div id="main-content">
  <!-- Topbar -->
  <div class="topbar">
    <span class="page-title"><i class="bi bi-grid-1x2 me-2 text-primary"></i>Dashboard</span>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted" style="font-size:.85rem;">
        <i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?>
      </span>
      <div class="dropdown">
        <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['pengguna_nama']) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item text-danger" href="logout.php">
            <i class="bi bi-box-arrow-right me-1"></i>Keluar</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-body">
    <?php if ($stokRendah > 0): ?>
    <div class="alert alert-warning d-flex align-items-center mb-4" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
      <div>Terdapat <strong><?= $stokRendah ?> obat</strong> dengan stok di bawah atau sama dengan batas minimum!</div>
    </div>
    <?php endif; ?>

    <!-- Stat cards -->
    <div class="row g-3 mb-4">
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background:#eff6ff;color:#2563eb;"><i class="bi bi-capsule"></i></div>
          <div>
            <div class="value"><?= $totalObat ?></div>
            <div class="label">Total Jenis Obat</div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background:#fef2f2;color:#dc2626;"><i class="bi bi-exclamation-circle"></i></div>
          <div>
            <div class="value"><?= $stokRendah ?></div>
            <div class="label">Stok Hampir Habis</div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background:#f0fdf4;color:#16a34a;"><i class="bi bi-box-arrow-in-down"></i></div>
          <div>
            <div class="value"><?= $masukBulanIni ?></div>
            <div class="label">Stok Masuk Bulan Ini</div>
          </div>
        </div>
      </div>
      <div class="col-xl-3 col-md-6">
        <div class="stat-card">
          <div class="icon" style="background:#fff7ed;color:#d97706;"><i class="bi bi-box-arrow-up"></i></div>
          <div>
            <div class="value"><?= $keluarBulanIni ?></div>
            <div class="label">Stok Keluar Bulan Ini</div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <!-- Obat stok rendah -->
      <div class="col-lg-4">
        <div class="table-card h-100">
          <div class="table-header">
            <h6><i class="bi bi-exclamation-circle text-danger me-1"></i>Stok Hampir Habis</h6>
            <a href="modules/obat/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
          </div>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Nama Obat</th><th>Stok</th><th>Min</th></tr>
              </thead>
              <tbody>
                <?php if ($qRendah->num_rows === 0): ?>
                <tr><td colspan="3" class="text-center text-muted py-3">
                  <i class="bi bi-check-circle text-success"></i> Semua stok aman</td></tr>
                <?php else: while ($r = $qRendah->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($r['nama']) ?></td>
                  <td><span class="badge-stok-rendah"><?= $r['stok'] ?></span></td>
                  <td><?= $r['stok_minimum'] ?></td>
                </tr>
                <?php endwhile; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Transaksi masuk terbaru -->
      <div class="col-lg-4">
        <div class="table-card h-100">
          <div class="table-header">
            <h6><i class="bi bi-box-arrow-in-down text-success me-1"></i>Stok Masuk Terbaru</h6>
            <a href="modules/stok_masuk/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
          </div>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Obat</th><th>Jumlah</th><th>Tanggal</th></tr>
              </thead>
              <tbody>
                <?php if ($qMasuk->num_rows === 0): ?>
                <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data</td></tr>
                <?php else: while ($r = $qMasuk->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($r['obat']) ?></td>
                  <td><span class="badge-stok-aman"><?= $r['jumlah'] ?></span></td>
                  <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                </tr>
                <?php endwhile; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Transaksi keluar terbaru -->
      <div class="col-lg-4">
        <div class="table-card h-100">
          <div class="table-header">
            <h6><i class="bi bi-box-arrow-up text-warning me-1"></i>Stok Keluar Terbaru</h6>
            <a href="modules/stok_keluar/index.php" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
          </div>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>Obat</th><th>Jumlah</th><th>Tanggal</th></tr>
              </thead>
              <tbody>
                <?php if ($qKeluar->num_rows === 0): ?>
                <tr><td colspan="3" class="text-center text-muted py-3">Belum ada data</td></tr>
                <?php else: while ($r = $qKeluar->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($r['obat']) ?></td>
                  <td><?= $r['jumlah'] ?></td>
                  <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
                </tr>
                <?php endwhile; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div><!-- end row -->

<?php require_once 'includes/footer.php'; ?>
