<?php
$depth      = 1;
$pageTitle  = 'Laporan';
$activeMenu = 'laporan';
require_once '../../includes/header.php';

$jenis = $_GET['jenis'] ?? 'stok';
$bln   = intval($_GET['bln'] ?? date('m'));
$thn   = intval($_GET['thn'] ?? date('Y'));

$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>

<?php require_once '../../includes/sidebar.php'; ?>

<div id="main-content">
  <div class="topbar">
    <span class="page-title"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan</span>
    <div class="d-flex align-items-center gap-3">
      <div class="dropdown">
        <button class="btn btn-light btn-sm dropdown-toggle" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($_SESSION['pengguna_nama']) ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
          <li><a class="dropdown-item text-danger" href="../../logout.php"><i class="bi bi-box-arrow-right me-1"></i>Keluar</a></li>
        </ul>
      </div>
    </div>
  </div>

  <div class="page-body">
    <!-- Filter -->
    <div class="form-card mb-4">
      <form method="get" class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Jenis Laporan</label>
          <select name="jenis" class="form-select">
            <option value="stok"     <?= $jenis === 'stok'     ? 'selected' : '' ?>>Stok Obat</option>
            <option value="masuk"    <?= $jenis === 'masuk'    ? 'selected' : '' ?>>Stok Masuk</option>
            <option value="keluar"   <?= $jenis === 'keluar'   ? 'selected' : '' ?>>Stok Keluar</option>
          </select>
        </div>
        <?php if ($jenis !== 'stok'): ?>
        <div class="col-md-2">
          <label class="form-label">Bulan</label>
          <select name="bln" class="form-select">
            <?php for ($i = 1; $i <= 12; $i++): ?>
            <option value="<?= $i ?>" <?= $i === $bln ? 'selected' : '' ?>><?= $namaBulan[$i] ?></option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Tahun</label>
          <input type="number" name="thn" class="form-control" value="<?= $thn ?>" min="2000" max="2099">
        </div>
        <?php endif; ?>
        <div class="col-md-2">
          <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-funnel me-1"></i>Tampilkan
          </button>
        </div>
        <div class="col-md-2">
          <a href="?<?= http_build_query(array_merge($_GET, ['cetak' => '1'])) ?>"
             target="_blank" class="btn btn-outline-secondary w-100">
            <i class="bi bi-printer me-1"></i>Cetak
          </a>
        </div>
      </form>
    </div>

    <?php
    // ============================
    // Laporan Stok Obat
    // ============================
    if ($jenis === 'stok'):
      $rows = $koneksi->query("SELECT o.kode_obat, o.nama, k.nama AS kategori, o.satuan,
          o.stok, o.stok_minimum, o.harga_beli, o.harga_jual, o.tanggal_kadaluarsa
          FROM obat o
          LEFT JOIN kategori k ON o.kategori_id=k.id
          ORDER BY o.nama ASC");
      $totalNilai = 0;
    ?>
    <div class="table-card">
      <div class="table-header">
        <h6>Laporan Stok Obat – Per <?= date('d F Y') ?></h6>
      </div>
      <div class="table-responsive">
        <table class="table mb-0" id="tblLaporan">
          <thead>
            <tr>
              <th>No</th><th>Kode</th><th>Nama Obat</th><th>Kategori</th>
              <th>Satuan</th><th>Stok</th><th>Min</th>
              <th>Harga Beli</th><th>Harga Jual</th><th>Nilai Stok</th>
              <th>Kadaluarsa</th><th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($rows->num_rows === 0): ?>
            <tr><td colspan="12" class="text-center text-muted py-4">Tidak ada data.</td></tr>
            <?php else: $no = 1; while ($r = $rows->fetch_assoc()):
              $nilai = $r['stok'] * $r['harga_beli'];
              $totalNilai += $nilai;
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><code><?= htmlspecialchars($r['kode_obat']) ?></code></td>
              <td><?= htmlspecialchars($r['nama']) ?></td>
              <td><?= htmlspecialchars($r['kategori'] ?? '-') ?></td>
              <td><?= htmlspecialchars($r['satuan']) ?></td>
              <td><?php if ($r['stok'] <= $r['stok_minimum']): ?>
                <span class="badge-stok-rendah"><?= $r['stok'] ?></span>
                <?php else: ?>
                <span class="badge-stok-aman"><?= $r['stok'] ?></span>
                <?php endif; ?></td>
              <td><?= $r['stok_minimum'] ?></td>
              <td>Rp <?= number_format($r['harga_beli'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($r['harga_jual'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($nilai, 0, ',', '.') ?></td>
              <td><?= $r['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($r['tanggal_kadaluarsa'])) : '-' ?></td>
              <td><?= $r['stok'] <= $r['stok_minimum']
                  ? '<span class="text-danger fw-semibold">Stok Rendah</span>'
                  : '<span class="text-success">Aman</span>' ?></td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
          <tfoot>
            <tr class="fw-bold">
              <td colspan="9" class="text-end">Total Nilai Stok:</td>
              <td>Rp <?= number_format($totalNilai, 0, ',', '.') ?></td>
              <td colspan="2"></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <?php
    // ============================
    // Laporan Stok Masuk
    // ============================
    elseif ($jenis === 'masuk'):
      $stmt = $koneksi->prepare("SELECT sm.kode_transaksi, o.kode_obat, o.nama AS obat, s.nama AS supplier,
          sm.jumlah, sm.harga_beli, (sm.jumlah * sm.harga_beli) AS total, sm.tanggal
          FROM stok_masuk sm
          JOIN obat o ON sm.obat_id=o.id
          LEFT JOIN supplier s ON sm.supplier_id=s.id
          WHERE MONTH(sm.tanggal)=? AND YEAR(sm.tanggal)=?
          ORDER BY sm.tanggal ASC");
      $stmt->bind_param('ii', $bln, $thn);
      $stmt->execute();
      $rows = $stmt->get_result();
      $totalJumlah = 0; $totalNilai = 0;
    ?>
    <div class="table-card">
      <div class="table-header">
        <h6>Laporan Stok Masuk – <?= $namaBulan[$bln] . ' ' . $thn ?></h6>
      </div>
      <div class="table-responsive">
        <table class="table mb-0" id="tblLaporan">
          <thead>
            <tr><th>No</th><th>Kode Transaksi</th><th>Kode Obat</th><th>Nama Obat</th>
              <th>Supplier</th><th>Jumlah</th><th>Harga Beli</th><th>Total</th><th>Tanggal</th></tr>
          </thead>
          <tbody>
            <?php if ($rows->num_rows === 0): ?>
            <tr><td colspan="9" class="text-center text-muted py-4">Tidak ada data pada periode ini.</td></tr>
            <?php else: $no = 1; while ($r = $rows->fetch_assoc()):
              $totalJumlah += $r['jumlah'];
              $totalNilai  += $r['total'];
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><code><?= htmlspecialchars($r['kode_transaksi']) ?></code></td>
              <td><code><?= htmlspecialchars($r['kode_obat']) ?></code></td>
              <td><?= htmlspecialchars($r['obat']) ?></td>
              <td><?= htmlspecialchars($r['supplier'] ?? '-') ?></td>
              <td><?= $r['jumlah'] ?></td>
              <td>Rp <?= number_format($r['harga_beli'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
              <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
          <tfoot class="fw-bold">
            <tr>
              <td colspan="5" class="text-end">Total:</td>
              <td><?= $totalJumlah ?></td>
              <td></td>
              <td>Rp <?= number_format($totalNilai, 0, ',', '.') ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>

    <?php
    // ============================
    // Laporan Stok Keluar
    // ============================
    else:
      $stmt = $koneksi->prepare("SELECT sk.kode_transaksi, o.kode_obat, o.nama AS obat,
          sk.jumlah, sk.harga_jual, (sk.jumlah * sk.harga_jual) AS total, sk.tanggal
          FROM stok_keluar sk
          JOIN obat o ON sk.obat_id=o.id
          WHERE MONTH(sk.tanggal)=? AND YEAR(sk.tanggal)=?
          ORDER BY sk.tanggal ASC");
      $stmt->bind_param('ii', $bln, $thn);
      $stmt->execute();
      $rows = $stmt->get_result();
      $totalJumlah = 0; $totalNilai = 0;
    ?>
    <div class="table-card">
      <div class="table-header">
        <h6>Laporan Stok Keluar – <?= $namaBulan[$bln] . ' ' . $thn ?></h6>
      </div>
      <div class="table-responsive">
        <table class="table mb-0" id="tblLaporan">
          <thead>
            <tr><th>No</th><th>Kode Transaksi</th><th>Kode Obat</th><th>Nama Obat</th>
              <th>Jumlah</th><th>Harga Jual</th><th>Total</th><th>Tanggal</th></tr>
          </thead>
          <tbody>
            <?php if ($rows->num_rows === 0): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data pada periode ini.</td></tr>
            <?php else: $no = 1; while ($r = $rows->fetch_assoc()):
              $totalJumlah += $r['jumlah'];
              $totalNilai  += $r['total'];
            ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><code><?= htmlspecialchars($r['kode_transaksi']) ?></code></td>
              <td><code><?= htmlspecialchars($r['kode_obat']) ?></code></td>
              <td><?= htmlspecialchars($r['obat']) ?></td>
              <td><?= $r['jumlah'] ?></td>
              <td>Rp <?= number_format($r['harga_jual'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($r['total'], 0, ',', '.') ?></td>
              <td><?= date('d/m/Y', strtotime($r['tanggal'])) ?></td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
          <tfoot class="fw-bold">
            <tr>
              <td colspan="4" class="text-end">Total:</td>
              <td><?= $totalJumlah ?></td>
              <td></td>
              <td>Rp <?= number_format($totalNilai, 0, ',', '.') ?></td>
              <td></td>
            </tr>
          </tfoot>
        </table>
      </div>
    </div>
    <?php endif; ?>

  </div>

<?php require_once '../../includes/footer.php'; ?>
