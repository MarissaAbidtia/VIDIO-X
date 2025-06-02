<?php
require_once '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Validasi input
$metode_pengambilan = $_POST['metode_pengambilan'] ?? '';
$nomor_meja = $_POST['nomor_meja'] ?? null;
$alamat_pengiriman = $_POST['alamat_pengiriman'] ?? '';

// Validasi metode pengambilan
if (!in_array($metode_pengambilan, ['dine-in', 'takeaway', 'delivery'])) {
    $_SESSION['error'] = 'Metode pengambilan tidak valid';
    header('Location: index.php');
    exit;
}

// Validasi input tambahan berdasarkan metode
if ($metode_pengambilan === 'dine-in' && empty($nomor_meja)) {
    $_SESSION['error'] = 'Nomor meja harus diisi untuk dine-in';
    header('Location: index.php');
    exit;
}

if ($metode_pengambilan === 'delivery' && empty($alamat_pengiriman)) {
    $_SESSION['error'] = 'Alamat pengiriman harus diisi untuk delivery';
    header('Location: index.php');
    exit;
}

try {
    // Mulai transaksi
    $pdo->beginTransaction();

    // Buat pesanan baru
    $stmt = $pdo->prepare("
        INSERT INTO pesanan (
            user_id, 
            metode_pengambilan, 
            nomor_meja, 
            alamat_pengiriman, 
            status,
            tanggal_pesanan
        ) VALUES (?, ?, ?, ?, 'menunggu_pembayaran', NOW())
    ");
    $stmt->execute([
        $_SESSION['user_id'],
        $metode_pengambilan,
        $nomor_meja,
        $alamat_pengiriman
    ]);
    $pesanan_id = $pdo->lastInsertId();

    // Ambil item dari keranjang
    $stmt = $pdo->prepare("
        SELECT k.*, p.harga 
        FROM keranjang k 
        JOIN produk p ON k.produk_id = p.id 
        WHERE k.user_id = ? AND k.status = 'keranjang'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $items = $stmt->fetchAll();

    // Hitung total
    $subtotal = 0;
    foreach ($items as $item) {
        $subtotal += $item['harga'] * $item['jumlah'];
    }
    $pajak = $subtotal * 0.1;
    $total = $subtotal + $pajak;

    // Update total pesanan
    $stmt = $pdo->prepare("
        UPDATE pesanan 
        SET subtotal = ?, pajak = ?, total = ? 
        WHERE id = ?
    ");
    $stmt->execute([$subtotal, $pajak, $total, $pesanan_id]);

    // Pindahkan item dari keranjang ke detail pesanan
    foreach ($items as $item) {
        $stmt = $pdo->prepare("
            INSERT INTO detail_pesanan (
                pesanan_id, 
                produk_id, 
                jumlah, 
                harga, 
                catatan,
                level_pedas
            ) VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $pesanan_id,
            $item['produk_id'],
            $item['jumlah'],
            $item['harga'],
            $item['catatan'],
            $item['level_pedas']
        ]);
    }

    // Kosongkan keranjang
    $stmt = $pdo->prepare("
        DELETE FROM keranjang 
        WHERE user_id = ? AND status = 'keranjang'
    ");
    $stmt->execute([$_SESSION['user_id']]);

    // Commit transaksi
    $pdo->commit();

    // Redirect ke halaman pembayaran
    header("Location: ../pembayaran.php?id=$pesanan_id");
    exit;

} catch (Exception $e) {
    // Rollback jika terjadi error
    $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan saat memproses pesanan';
    header('Location: index.php');
    exit;
}