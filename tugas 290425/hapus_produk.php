<?php
require_once 'koneksi.php';

// Cek apakah user sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: auth/login.php');
    exit;
}

// Cek apakah ada ID yang dikirim
if (isset($_POST['id'])) {
    $id = $_POST['id'];
    
    try {
        // Mulai transaksi
        $pdo->beginTransaction();
        
        // Hapus data produk
        $stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
        $stmt->execute([$id]);
        
        // Commit transaksi
        $pdo->commit();
        
        // Redirect dengan pesan sukses
        $_SESSION['success'] = "Produk berhasil dihapus";
        header('Location: menu.php');
        exit;
        
    } catch (PDOException $e) {
        // Rollback jika terjadi error
        $pdo->rollBack();
        $_SESSION['error'] = "Gagal menghapus produk: " . $e->getMessage();
        header('Location: menu.php');
        exit;
    }
}

// Jika tidak ada ID, redirect ke menu
header('Location: menu.php');
exit;
?>