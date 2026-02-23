<?php
$base   = str_repeat('../', $depth ?? 0);
$active = $activeMenu ?? '';
?>
<nav id="sidebar">
  <div class="brand d-flex align-items-center gap-2">
    <div style="width:40px;height:40px;background:#2563eb;border-radius:8px;
                display:flex;align-items:center;justify-content:center;flex-shrink:0;">
      <i class="bi bi-hospital text-white fs-5"></i>
    </div>
    <span>Klinik<br>Harmy Medika</span>
  </div>

  <div class="mt-3">
    <span class="nav-section">Menu Utama</span>
    <a href="<?= $base ?>dashboard.php"
       class="nav-link <?= $active === 'dashboard' ? 'active' : '' ?>">
      <i class="bi bi-grid-1x2"></i> Dashboard
    </a>

    <span class="nav-section">Manajemen Obat</span>
    <a href="<?= $base ?>modules/obat/index.php"
       class="nav-link <?= $active === 'obat' ? 'active' : '' ?>">
      <i class="bi bi-capsule"></i> Data Obat
    </a>
    <a href="<?= $base ?>modules/stok_masuk/index.php"
       class="nav-link <?= $active === 'stok_masuk' ? 'active' : '' ?>">
      <i class="bi bi-box-arrow-in-down"></i> Stok Masuk
    </a>
    <a href="<?= $base ?>modules/stok_keluar/index.php"
       class="nav-link <?= $active === 'stok_keluar' ? 'active' : '' ?>">
      <i class="bi bi-box-arrow-up"></i> Stok Keluar
    </a>

    <span class="nav-section">Referensi</span>
    <a href="<?= $base ?>modules/kategori/index.php"
       class="nav-link <?= $active === 'kategori' ? 'active' : '' ?>">
      <i class="bi bi-tags"></i> Kategori
    </a>
    <a href="<?= $base ?>modules/supplier/index.php"
       class="nav-link <?= $active === 'supplier' ? 'active' : '' ?>">
      <i class="bi bi-building"></i> Supplier
    </a>

    <span class="nav-section">Laporan</span>
    <a href="<?= $base ?>modules/laporan/index.php"
       class="nav-link <?= $active === 'laporan' ? 'active' : '' ?>">
      <i class="bi bi-file-earmark-bar-graph"></i> Laporan
    </a>
  </div>
</nav>
