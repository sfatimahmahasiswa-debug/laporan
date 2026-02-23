<?php
$depth      = 1;
$pageTitle  = 'Tambah Obat';
$activeMenu = 'obat';
require_once '../../includes/header.php';

$errors = [];

// Ambil daftar kategori
$kategoriList = $koneksi->query('SELECT id, nama FROM kategori ORDER BY nama ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode      = trim($_POST['kode_obat'] ?? '');
    $nama      = trim($_POST['nama'] ?? '');
    $katId     = intval($_POST['kategori_id'] ?? 0);
    $satuan    = trim($_POST['satuan'] ?? '');
    $stokMin   = intval($_POST['stok_minimum'] ?? 0);
    $hargaBeli = floatval(str_replace(['.', ','], ['', '.'], $_POST['harga_beli'] ?? '0'));
    $hargaJual = floatval(str_replace(['.', ','], ['', '.'], $_POST['harga_jual'] ?? '0'));
    $kadaluarsa= $_POST['tanggal_kadaluarsa'] ?? '';
    $keterangan= trim($_POST['keterangan'] ?? '');

    if ($kode === '')   $errors[] = 'Kode obat wajib diisi.';
    if ($nama === '')   $errors[] = 'Nama obat wajib diisi.';
    if ($satuan === '') $errors[] = 'Satuan wajib diisi.';

    if (!$errors) {
        // Cek duplikat kode
        $ck = $koneksi->prepare('SELECT id FROM obat WHERE kode_obat = ?');
        $ck->bind_param('s', $kode);
        $ck->execute();
        if ($ck->get_result()->num_rows > 0) {
            $errors[] = 'Kode obat sudah digunakan.';
        }
        $ck->close();
    }

    if (!$errors) {
        $katIdBind = $katId ?: null;
        $kadBind   = $kadaluarsa ?: null;
        $stmt = $koneksi->prepare('INSERT INTO obat (kode_obat, nama, kategori_id, satuan, stok_minimum, harga_beli, harga_jual, tanggal_kadaluarsa, keterangan) VALUES (?,?,?,?,?,?,?,?,?)');
        $stmt->bind_param('ssissddss', $kode, $nama, $katIdBind, $satuan, $stokMin, $hargaBeli, $hargaJual, $kadBind, $keterangan);
        if ($stmt->execute()) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Obat \"{$nama}\" berhasil ditambahkan."];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data. Silakan coba lagi.';
        }
        $stmt->close();
    }
}
?>

<?php require_once '../../includes/sidebar.php'; ?>

<div id="main-content">
  <div class="topbar">
    <span class="page-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Obat</span>
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
    <?php if ($errors): ?>
    <div class="alert alert-danger alert-dismissible fade show">
      <ul class="mb-0"><?php foreach ($errors as $e) echo '<li>' . htmlspecialchars($e) . '</li>'; ?></ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <div class="form-card">
      <div class="d-flex align-items-center justify-content-between mb-4">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-capsule me-1"></i>Form Tambah Obat</h6>
        <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
      </div>

      <form method="post" novalidate>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Kode Obat <span class="text-danger">*</span></label>
            <input type="text" name="kode_obat" class="form-control" placeholder="cth. OBT-001"
                   value="<?= htmlspecialchars($_POST['kode_obat'] ?? '') ?>" required>
          </div>
          <div class="col-md-8">
            <label class="form-label">Nama Obat <span class="text-danger">*</span></label>
            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap obat"
                   value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Kategori</label>
            <select name="kategori_id" class="form-select">
              <option value="">-- Pilih Kategori --</option>
              <?php $kategoriList->data_seek(0); while ($k = $kategoriList->fetch_assoc()): ?>
              <option value="<?= $k['id'] ?>" <?= (($_POST['kategori_id'] ?? '') == $k['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($k['nama']) ?>
              </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label">Satuan <span class="text-danger">*</span></label>
            <input type="text" name="satuan" class="form-control" placeholder="cth. Tablet, Kapsul, Botol"
                   value="<?= htmlspecialchars($_POST['satuan'] ?? '') ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Stok Minimum</label>
            <input type="number" name="stok_minimum" class="form-control" min="0"
                   value="<?= intval($_POST['stok_minimum'] ?? 5) ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Harga Beli (Rp)</label>
            <input type="number" name="harga_beli" class="form-control" min="0" step="100"
                   value="<?= htmlspecialchars($_POST['harga_beli'] ?? '0') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Harga Jual (Rp)</label>
            <input type="number" name="harga_jual" class="form-control" min="0" step="100"
                   value="<?= htmlspecialchars($_POST['harga_jual'] ?? '0') ?>">
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal Kadaluarsa</label>
            <input type="date" name="tanggal_kadaluarsa" class="form-control"
                   value="<?= htmlspecialchars($_POST['tanggal_kadaluarsa'] ?? '') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"
                      placeholder="Opsional"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
          </div>
          <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

<?php require_once '../../includes/footer.php'; ?>
