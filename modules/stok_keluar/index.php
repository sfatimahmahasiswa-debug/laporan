<?php
$depth      = 1;
$pageTitle  = 'Stok Keluar';
$activeMenu = 'stok_keluar';
require_once '../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

$bln = intval($_GET['bln'] ?? date('m'));
$thn = intval($_GET['thn'] ?? date('Y'));

$sql = "SELECT sk.*, o.nama AS obat, o.kode_obat
        FROM stok_keluar sk
        JOIN obat o ON sk.obat_id = o.id
        WHERE MONTH(sk.tanggal)=? AND YEAR(sk.tanggal)=?
        ORDER BY sk.dibuat_pada DESC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param('ii', $bln, $thn);
$stmt->execute();
$result = $stmt->get_result();

$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>

<?php require_once '../../includes/sidebar.php'; ?>

<div id="main-content">
  <div class="topbar">
    <span class="page-title"><i class="bi bi-box-arrow-up me-2 text-primary"></i>Stok Keluar</span>
    <div class="d-flex align-items-center gap-3">
      <span class="text-muted" style="font-size:.85rem;"><i class="bi bi-calendar3 me-1"></i><?= date('d F Y') ?></span>
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
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
      <?= htmlspecialchars($flash['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="table-card">
      <div class="table-header">
        <h6><i class="bi bi-box-arrow-up me-1"></i>Daftar Stok Keluar</h6>
        <div class="d-flex gap-2 align-items-center">
          <form class="d-flex gap-2" method="get">
            <select name="bln" class="form-select form-select-sm" style="width:130px;">
              <?php for ($i = 1; $i <= 12; $i++): ?>
              <option value="<?= $i ?>" <?= $i === $bln ? 'selected' : '' ?>><?= $namaBulan[$i] ?></option>
              <?php endfor; ?>
            </select>
            <input type="number" name="thn" class="form-control form-control-sm" style="width:90px;"
                   value="<?= $thn ?>" min="2000" max="2099">
            <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-funnel"></i></button>
          </form>
          <a href="tambah.php" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg me-1"></i>Tambah</a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr><th>No</th><th>Kode Transaksi</th><th>Obat</th><th>Jumlah</th><th>Harga Jual</th><th>Total</th><th>Tanggal</th><th>Keterangan</th></tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada transaksi pada periode ini.</td></tr>
            <?php else: $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><code><?= htmlspecialchars($row['kode_transaksi']) ?></code></td>
              <td><?= htmlspecialchars($row['obat']) ?><br>
                <small class="text-muted"><?= htmlspecialchars($row['kode_obat']) ?></small></td>
              <td><?= $row['jumlah'] ?></td>
              <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($row['jumlah'] * $row['harga_jual'], 0, ',', '.') ?></td>
              <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
              <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php require_once '../../includes/footer.php'; ?>
