<?php
require_once '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Validasi input
$keranjang_id = $_POST['keranjang_id'] ?? 0;
$catatan = trim($_POST['catatan'] ?? '');

// Update catatan
$stmt = $pdo->prepare("
    UPDATE keranjang 
    SET catatan = ? 
    WHERE id = ? AND user_id = ? AND status = 'keranjang'
");
$stmt->execute([$catatan, $keranjang_id, $_SESSION['user_id']]);

// Kembali ke halaman keranjang
header('Location: index.php');
exit;