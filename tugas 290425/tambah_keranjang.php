<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $produk_id = $_POST['produk_id'] ?? 0;
    
    // Cek apakah produk sudah ada di keranjang
    $stmt = $pdo->prepare("
        SELECT id, jumlah FROM keranjang 
        WHERE user_id = ? AND produk_id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $produk_id]);
    $keranjang = $stmt->fetch();
    
    if ($keranjang) {
        // Update jumlah jika sudah ada
        $stmt = $pdo->prepare("
            UPDATE keranjang 
            SET jumlah = jumlah + 1 
            WHERE id = ?
        ");
        $stmt->execute([$keranjang['id']]);
    } else {
        // Tambah baru jika belum ada
        $stmt = $pdo->prepare("
            INSERT INTO keranjang (user_id, produk_id, jumlah) 
            VALUES (?, ?, 1)
        ");
        $stmt->execute([$_SESSION['user_id'], $produk_id]);
    }
}

// Redirect kembali ke halaman produk
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit;