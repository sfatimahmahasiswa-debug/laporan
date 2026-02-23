<?php
session_start();
if (isset($_SESSION['pengguna_id'])) {
    header('Location: dashboard.php');
    exit;
}
header('Location: login.php');
exit;
