<?php
require_once 'config.php';

if (is_logged_in()) {
    // Log aktivitas logout
    log_aktivitas($_SESSION['user_id'], 'Logout', 'User keluar dari sistem');
}

// Hapus semua session
session_destroy();

// Redirect ke halaman login
header("Location: login.php");
exit();
?>