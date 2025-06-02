<?php
// Aktifkan pelaporan error untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    // Tambahkan exit setelah header
    header('Location: auth/login.php?error=not_logged_in');
    exit; 
}

// Pastikan request adalah POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Tambahkan exit setelah header
    header('Location: checkout.php?error=invalid_request_method');
    exit; 
}

$user_id = $_SESSION['user_id'];

// Ambil data dari POST (checkout.php)
$metode_pengambilan = $_POST['metode_pengambilan'] ?? '';
$nomor_meja = !empty($_POST['nomor_meja']) ? $_POST['nomor_meja'] : null;
$alamat_pengiriman = !empty($_POST['alamat_pengiriman']) ? $_POST['alamat_pengiriman'] : null;
$subtotal = floatval($_POST['subtotal'] ?? 0);
$pajak = floatval($_POST['pajak'] ?? 0);
$biaya_layanan = floatval($_POST['biaya_layanan'] ?? 0);
$total_keseluruhan = floatval($_POST['total_keseluruhan'] ?? 0);
$metode_pembayaran = $_POST['metode_pembayaran'] ?? '';

// Validasi dasar
if (empty($metode_pengambilan) || empty($metode_pembayaran) || $total_keseluruhan <= 0) {
    // Tambahkan exit setelah header
    header('Location: checkout.php?error=data_tidak_lengkap_proses');
    exit; 
}

// Ambil item keranjang yang aktif
$stmt_cart = $pdo->prepare("
    SELECT k.produk_id, k.jumlah, p.nama, p.harga 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = ? AND k.status = 'keranjang'
");
$stmt_cart->execute([$user_id]);
$items_keranjang = $stmt_cart->fetchAll();

if (empty($items_keranjang)) {
    // Tambahkan exit setelah header
    header('Location: cart.php?info=keranjang_kosong_saat_proses');
    exit; 
}

// Mulai transaksi database
$pdo->beginTransaction();

try {
    // 1. Buat kode pesanan unik
    $kode_pesanan = "TLC-" . date("YmdHis") . "-" . strtoupper(substr(uniqid(), -4)); // Tambahkan His untuk lebih unik

    // 2. Simpan ke tabel 'pesanan'
    $stmt_pesanan = $pdo->prepare("
        INSERT INTO pesanan (user_id, kode_pesanan, metode_pengambilan, nomor_meja, alamat_pengiriman, 
                             subtotal, pajak, biaya_layanan, total_keseluruhan, metode_pembayaran, 
                             status_pesanan, status_pembayaran)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'belum dibayar')
    ");
    $stmt_pesanan->execute([
        $user_id, $kode_pesanan, $metode_pengambilan, $nomor_meja, $alamat_pengiriman,
        $subtotal, $pajak, $biaya_layanan, $total_keseluruhan, $metode_pembayaran
    ]);
    $pesanan_id = $pdo->lastInsertId();

    if (!$pesanan_id) {
        throw new Exception("Gagal mendapatkan ID pesanan terakhir.");
    }

    // 3. Simpan item pesanan ke tabel 'detail_pesanan'
    $stmt_detail = $pdo->prepare("
        INSERT INTO detail_pesanan (pesanan_id, produk_id, nama_produk, harga_saat_pesan, jumlah, subtotal_item)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    foreach ($items_keranjang as $item) {
        $subtotal_item = $item['harga'] * $item['jumlah'];
        $stmt_detail->execute([
            $pesanan_id, $item['produk_id'], $item['nama'], $item['harga'], $item['jumlah'], $subtotal_item
        ]);
    }

    // 4. Update status item di keranjang (atau hapus)
    $stmt_hapus_keranjang = $pdo->prepare("DELETE FROM keranjang WHERE user_id = ? AND status = 'keranjang'");
    $stmt_hapus_keranjang->execute([$user_id]);
    
    // Commit transaksi
    $pdo->commit();

    // Redirect ke halaman sukses dengan kode pesanan
    $_SESSION['pesanan_sukses'] = "Pesanan Anda dengan kode <strong>$kode_pesanan</strong> telah berhasil dibuat.";
    // Pastikan tidak ada output sebelum header ini
    header("Location: pesanan_sukses.php?kode_pesanan=" . urlencode($kode_pesanan));
    exit; // Sangat penting untuk menghentikan eksekusi skrip setelah redirect

} catch (Exception $e) {
    // Rollback transaksi jika terjadi error
    $pdo->rollBack();
    // Catat error atau tampilkan pesan error yang lebih ramah
    // error_log("Error saat proses pesanan: " . $e->getMessage());
    // Untuk debugging, tampilkan pesan error langsung (hapus atau ubah untuk produksi)
    // die("Terjadi kesalahan: " . $e->getMessage() . "<br><a href='checkout.php'>Kembali ke Checkout</a>"); 
    header("Location: checkout.php?error=proses_gagal&msg=" . urlencode($e->getMessage()));
    exit; // Penting setelah redirect
}
?>