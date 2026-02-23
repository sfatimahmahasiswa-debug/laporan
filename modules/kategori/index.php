<?php
$depth      = 1;
$pageTitle  = 'Data Kategori';
$activeMenu = 'kategori';
require_once '../../includes/header.php';

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

// Tambah / Edit inline
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama       = trim($_POST['nama'] ?? '');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $editId     = intval($_POST['edit_id'] ?? 0);

    if ($nama !== '') {
        if ($editId) {
            $stmt = $koneksi->prepare('UPDATE kategori SET nama=?, keterangan=? WHERE id=?');
            $stmt->bind_param('ssi', $nama, $keterangan, $editId);
            $stmt->execute();
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Kategori berhasil diperbarui.'];
        } else {
            $stmt = $koneksi->prepare('INSERT INTO kategori (nama, keterangan) VALUES (?, ?)');
            $stmt->bind_param('ss', $nama, $keterangan);
            $stmt->execute();
            $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Kategori berhasil ditambahkan.'];
        }
        $stmt->close();
    }
    header('Location: index.php');
    exit;
}

// Hapus
if (isset($_GET['hapus'])) {
    $hId = intval($_GET['hapus']);
    $stmt = $koneksi->prepare('DELETE FROM kategori WHERE id=?');
    $stmt->bind_param('i', $hId);
    $stmt->execute();
    $stmt->close();
    $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Kategori berhasil dihapus.'];
    header('Location: index.php');
    exit;
}

// Edit data
$editData = null;
if (isset($_GET['edit'])) {
    $eId = intval($_GET['edit']);
    $r   = $koneksi->prepare('SELECT * FROM kategori WHERE id=?');
    $r->bind_param('i', $eId);
    $r->execute();
    $editData = $r->get_result()->fetch_assoc();
    $r->close();
}

$list = $koneksi->query('SELECT k.*, (SELECT COUNT(*) FROM obat WHERE kategori_id=k.id) AS jumlah_obat FROM kategori k ORDER BY k.nama ASC');
?>

<?php require_once '../../includes/sidebar.php'; ?>

<div id="main-content">
  <div class="topbar">
    <span class="page-title"><i class="bi bi-tags me-2 text-primary"></i>Data Kategori</span>
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
    <?php if ($flash): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible fade show">
      <?= htmlspecialchars($flash['msg']) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="row g-3">
      <!-- Form -->
      <div class="col-md-4">
        <div class="form-card">
          <h6 class="fw-semibold mb-3"><i class="bi bi-<?= $editData ? 'pencil' : 'plus-circle' ?> me-1"></i>
            <?= $editData ? 'Edit' : 'Tambah' ?> Kategori</h6>
          <form method="post">
            <?php if ($editData): ?>
            <input type="hidden" name="edit_id" value="<?= $editData['id'] ?>">
            <?php endif; ?>
            <div class="mb-3">
              <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
              <input type="text" name="nama" class="form-control" required
                     value="<?= htmlspecialchars($editData['nama'] ?? '') ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <textarea name="keterangan" class="form-control" rows="3"><?= htmlspecialchars($editData['keterangan'] ?? '') ?></textarea>
            </div>
            <div class="d-flex gap-2">
              <?php if ($editData): ?>
              <a href="index.php" class="btn btn-outline-secondary flex-fill">Batal</a>
              <?php endif; ?>
              <button type="submit" class="btn btn-primary flex-fill">
                <i class="bi bi-save me-1"></i>Simpan
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Table -->
      <div class="col-md-8">
        <div class="table-card">
          <div class="table-header"><h6><i class="bi bi-tags me-1"></i>Daftar Kategori</h6></div>
          <div class="table-responsive">
            <table class="table mb-0">
              <thead>
                <tr><th>No</th><th>Nama Kategori</th><th>Keterangan</th><th>Jml Obat</th><th>Aksi</th></tr>
              </thead>
              <tbody>
                <?php if ($list->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted py-3">Belum ada data.</td></tr>
                <?php else: $no = 1; while ($row = $list->fetch_assoc()): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['nama']) ?></td>
                  <td><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                  <td><span class="badge bg-primary"><?= $row['jumlah_obat'] ?></span></td>
                  <td>
                    <a href="?edit=<?= $row['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <button class="btn btn-sm btn-outline-danger"
                      onclick="konfirmasiHapus('?hapus=<?= $row['id'] ?>','<?= addslashes(htmlspecialchars($row['nama'])) ?>')">
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
    </div>
  </div>

<?php require_once '../../includes/footer.php'; ?>
