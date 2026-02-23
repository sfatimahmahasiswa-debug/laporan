<?php
$depth      = 1;
$pageTitle  = 'Data Obat';
$activeMenu = 'obat';
require_once '../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Pencarian
$cari = trim($_GET['cari'] ?? '');
$where = '';
$params = [];
$types  = '';
if ($cari !== '') {
    $where = 'WHERE (o.kode_obat LIKE ? OR o.nama LIKE ?)';
    $like  = "%{$cari}%";
    $params = [$like, $like];
    $types  = 'ss';
}

$sql = "SELECT o.*, k.nama AS kategori
        FROM obat o
        LEFT JOIN kategori k ON o.kategori_id = k.id
        {$where}
        ORDER BY o.nama ASC";

$stmt = $koneksi->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php require_once '../../includes/sidebar.php'; ?>

<div id="main-content">
  <div class="topbar">
    <span class="page-title"><i class="bi bi-capsule me-2 text-primary"></i>Data Obat</span>
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
        <h6><i class="bi bi-capsule me-1"></i>Daftar Obat</h6>
        <div class="d-flex gap-2">
          <form class="d-flex gap-2" method="get">
            <input type="text" name="cari" class="form-control form-control-sm" placeholder="Cari kode / nama..."
                   value="<?= htmlspecialchars($cari) ?>" style="width:200px;">
            <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
            <?php if ($cari): ?>
            <a href="index.php" class="btn btn-sm btn-outline-danger"><i class="bi bi-x"></i></a>
            <?php endif; ?>
          </form>
          <a href="tambah.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg me-1"></i>Tambah
          </a>
        </div>
      </div>
      <div class="table-responsive">
        <table class="table mb-0">
          <thead>
            <tr>
              <th>No</th>
              <th>Kode</th>
              <th>Nama Obat</th>
              <th>Kategori</th>
              <th>Satuan</th>
              <th>Stok</th>
              <th>Min</th>
              <th>Harga Beli</th>
              <th>Harga Jual</th>
              <th>Kadaluarsa</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($result->num_rows === 0): ?>
            <tr><td colspan="11" class="text-center text-muted py-4">Tidak ada data ditemukan.</td></tr>
            <?php else: $no = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $no++ ?></td>
              <td><code><?= htmlspecialchars($row['kode_obat']) ?></code></td>
              <td><?= htmlspecialchars($row['nama']) ?></td>
              <td><?= htmlspecialchars($row['kategori'] ?? '-') ?></td>
              <td><?= htmlspecialchars($row['satuan']) ?></td>
              <td>
                <?php if ($row['stok'] <= $row['stok_minimum']): ?>
                <span class="badge-stok-rendah"><?= $row['stok'] ?></span>
                <?php else: ?>
                <span class="badge-stok-aman"><?= $row['stok'] ?></span>
                <?php endif; ?>
              </td>
              <td><?= $row['stok_minimum'] ?></td>
              <td>Rp <?= number_format($row['harga_beli'], 0, ',', '.') ?></td>
              <td>Rp <?= number_format($row['harga_jual'], 0, ',', '.') ?></td>
              <td><?= $row['tanggal_kadaluarsa'] ? date('d/m/Y', strtotime($row['tanggal_kadaluarsa'])) : '-' ?></td>
              <td>
                <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary">
                  <i class="bi bi-pencil"></i>
                </a>
                <button class="btn btn-sm btn-outline-danger"
                  onclick="konfirmasiHapus('hapus.php?id=<?= $row['id'] ?>','<?= addslashes(htmlspecialchars($row['nama'])) ?>')">
                  <i class="bi bi-trash"></i>
                </button>
              </td>
            </tr>
            <?php endwhile; endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

<?php require_once '../../includes/footer.php'; ?>
