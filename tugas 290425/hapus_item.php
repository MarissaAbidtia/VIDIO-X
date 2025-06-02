<?php
require_once 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Validasi input
$keranjang_id = $_POST['keranjang_id'] ?? 0;

// Hapus item dari keranjang
$stmt = $pdo->prepare("
    DELETE FROM keranjang 
    WHERE id = ? AND user_id = ? AND status = 'keranjang'
");
$stmt->execute([$keranjang_id, $_SESSION['user_id']]);

// Kembali ke halaman keranjang
header('Location: cart.php');
exit;