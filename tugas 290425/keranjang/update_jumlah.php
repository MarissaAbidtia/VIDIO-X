<?php
require_once '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Validasi input
$keranjang_id = $_POST['keranjang_id'] ?? 0;
$jumlah = $_POST['jumlah'] ?? 1;

// Validasi jumlah
if ($jumlah < 1) $jumlah = 1;
if ($jumlah > 10) $jumlah = 10;

// Update jumlah item
$stmt = $pdo->prepare("
    UPDATE keranjang 
    SET jumlah = ? 
    WHERE id = ? AND user_id = ? AND status = 'keranjang'
");
$stmt->execute([$jumlah, $keranjang_id, $_SESSION['user_id']]);

// Kembali ke halaman keranjang
header('Location: index.php');
exit;