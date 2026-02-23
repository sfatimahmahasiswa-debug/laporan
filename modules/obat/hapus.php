<?php
session_start();
require_once '../../config/database.php';
if (!isset($_SESSION['pengguna_id'])) { header('Location: ../../login.php'); exit; }

$id = intval($_GET['id'] ?? 0);
if ($id) {
    // Ambil nama obat untuk pesan
    $res = $koneksi->prepare('SELECT nama FROM obat WHERE id = ?');
    $res->bind_param('i', $id);
    $res->execute();
    $row = $res->get_result()->fetch_assoc();
    $res->close();

    if ($row) {
        $stmt = $koneksi->prepare('DELETE FROM obat WHERE id = ?');
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            $_SESSION['flash'] = ['type' => 'success', 'msg' => "Obat \"{$row['nama']}\" berhasil dihapus."];
        } else {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Gagal menghapus data.'];
        }
        $stmt->close();
    }
}
header('Location: index.php');
exit;
