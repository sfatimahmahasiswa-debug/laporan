<?php
$depth      = 1;
$pageTitle  = 'Tambah Stok Masuk';
$activeMenu = 'stok_masuk';
require_once '../../includes/header.php';

$errors = [];

// Generate kode transaksi otomatis
$lastKode = $koneksi->query("SELECT kode_transaksi FROM stok_masuk ORDER BY id DESC LIMIT 1")->fetch_assoc();
$newNo    = 1;
if ($lastKode) {
    preg_match('/(\d+)$/', $lastKode['kode_transaksi'], $m);
    $newNo = intval($m[1] ?? 0) + 1;
}
$kodeDefault = 'SM-' . date('Ymd') . '-' . str_pad($newNo, 4, '0', STR_PAD_LEFT);

$obatList    = $koneksi->query('SELECT id, kode_obat, nama, satuan, stok FROM obat ORDER BY nama ASC');
$supplierList= $koneksi->query('SELECT id, nama FROM supplier ORDER BY nama ASC');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode      = trim($_POST['kode_transaksi'] ?? '');
    $obatId    = intval($_POST['obat_id'] ?? 0);
    $supId     = intval($_POST['supplier_id'] ?? 0);
    $jumlah    = intval($_POST['jumlah'] ?? 0);
    $hargaBeli = floatval($_POST['harga_beli'] ?? 0);
    $tanggal   = $_POST['tanggal'] ?? '';
    $keterangan= trim($_POST['keterangan'] ?? '');

    if ($kode === '')    $errors[] = 'Kode transaksi wajib diisi.';
    if (!$obatId)        $errors[] = 'Obat wajib dipilih.';
    if ($jumlah <= 0)    $errors[] = 'Jumlah harus lebih dari 0.';
    if ($tanggal === '') $errors[] = 'Tanggal wajib diisi.';

    if (!$errors) {
        // Cek duplikat kode
        $ck = $koneksi->prepare('SELECT id FROM stok_masuk WHERE kode_transaksi=?');
        $ck->bind_param('s', $kode);
        $ck->execute();
        if ($ck->get_result()->num_rows > 0) $errors[] = 'Kode transaksi sudah digunakan.';
        $ck->close();
    }

    if (!$errors) {
        $supBind = $supId ?: null;
        $pgId    = $_SESSION['pengguna_id'];
        $stmt    = $koneksi->prepare('INSERT INTO stok_masuk (kode_transaksi, obat_id, supplier_id, jumlah, harga_beli, tanggal, pengguna_id, keterangan) VALUES (?,?,?,?,?,?,?,?)');
        $stmt->bind_param('siiidsis', $kode, $obatId, $supBind, $jumlah, $hargaBeli, $tanggal, $pgId, $keterangan);

        if ($stmt->execute()) {
            // Update stok obat
            $upd = $koneksi->prepare('UPDATE obat SET stok = stok + ? WHERE id = ?');
            $upd->bind_param('ii', $jumlah, $obatId);
            $upd->execute();
            $upd->close();

            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Stok masuk berhasil dicatat."];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Gagal menyimpan data.';
        }
        $stmt->close();
    }
}
?>

<?php require_once '../../includes/sidebar.php'; ?>

<div id="main-content">
  <div class="topbar">
    <span class="page-title"><i class="bi bi-plus-circle me-2 text-primary"></i>Tambah Stok Masuk</span>
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
        <h6 class="mb-0 fw-semibold"><i class="bi bi-box-arrow-in-down me-1"></i>Form Stok Masuk</h6>
        <a href="index.php" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
      </div>

      <form method="post" novalidate>
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Kode Transaksi <span class="text-danger">*</span></label>
            <input type="text" name="kode_transaksi" class="form-control"
                   value="<?= htmlspecialchars($_POST['kode_transaksi'] ?? $kodeDefault) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Tanggal <span class="text-danger">*</span></label>
            <input type="date" name="tanggal" class="form-control"
                   value="<?= htmlspecialchars($_POST['tanggal'] ?? date('Y-m-d')) ?>" required>
          </div>
          <div class="col-md-4">
            <label class="form-label">Supplier</label>
            <select name="supplier_id" class="form-select">
              <option value="">-- Pilih Supplier --</option>
              <?php $supplierList->data_seek(0); while ($s = $supplierList->fetch_assoc()): ?>
              <option value="<?= $s['id'] ?>" <?= (($_POST['supplier_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($s['nama']) ?>
              </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">Obat <span class="text-danger">*</span></label>
            <select name="obat_id" class="form-select" required>
              <option value="">-- Pilih Obat --</option>
              <?php $obatList->data_seek(0); while ($o = $obatList->fetch_assoc()): ?>
              <option value="<?= $o['id'] ?>" <?= (($_POST['obat_id'] ?? '') == $o['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($o['kode_obat'] . ' – ' . $o['nama']) ?>
                (Stok: <?= $o['stok'] ?> <?= htmlspecialchars($o['satuan']) ?>)
              </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label">Jumlah <span class="text-danger">*</span></label>
            <input type="number" name="jumlah" class="form-control" min="1"
                   value="<?= intval($_POST['jumlah'] ?? 1) ?>" required>
          </div>
          <div class="col-md-3">
            <label class="form-label">Harga Beli (Rp)</label>
            <input type="number" name="harga_beli" class="form-control" min="0" step="100"
                   value="<?= htmlspecialchars($_POST['harga_beli'] ?? '0') ?>">
          </div>
          <div class="col-12">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control" rows="2"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
          </div>
          <div class="col-12 d-flex gap-2 justify-content-end">
            <a href="index.php" class="btn btn-outline-secondary">Batal</a>
            <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
          </div>
        </div>
      </form>
    </div>
  </div>

<?php require_once '../../includes/footer.php'; ?>
