<?php
session_start();
require_once 'config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Username dan password tidak boleh kosong.';
    } else {
        $stmt = $koneksi->prepare('SELECT id, nama, password, peran FROM pengguna WHERE username = ? LIMIT 1');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['pengguna_id']   = $user['id'];
            $_SESSION['pengguna_nama'] = $user['nama'];
            $_SESSION['pengguna_peran']= $user['peran'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – <?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="login-wrapper">
  <div class="login-card">
    <div class="logo"><i class="bi bi-hospital"></i></div>
    <h5 class="text-center fw-bold mb-1" style="color:#1e293b;">Klinik Harmy Medika</h5>
    <p class="text-center text-muted mb-4" style="font-size:.85rem;">Sistem Inventori Berbasis Web</p>

    <?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <form method="post" autocomplete="off" novalidate>
      <div class="mb-3">
        <label class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-person text-muted"></i>
          </span>
          <input type="text" name="username" class="form-control border-start-0"
                 placeholder="Masukkan username" required
                 value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
        </div>
      </div>
      <div class="mb-4">
        <label class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-lock text-muted"></i>
          </span>
          <input type="password" name="password" class="form-control border-start-0"
                 placeholder="Masukkan password" required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary w-100 py-2">
        <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
      </button>
    </form>
    <p class="text-center mt-4 mb-0 text-muted" style="font-size:.78rem;">
      &copy; <?= date('Y') ?> Klinik Harmy Medika
    </p>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/main.js"></script>
</body>
</html>
