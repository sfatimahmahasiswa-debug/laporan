<?php
// Guard: cek sesi login
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['pengguna_id'])) {
    header('Location: ' . str_repeat('../', $depth ?? 0) . 'login.php');
    exit;
}
require_once str_repeat('../', $depth ?? 0) . 'config/database.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' – ' : '' ?><?= APP_NAME ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="<?= str_repeat('../', $depth ?? 0) ?>assets/css/style.css">
</head>
<body>
