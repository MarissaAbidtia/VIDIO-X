<?php
require_once 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Pastikan request adalah POST dari cart.php
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Jika bukan POST, mungkin redirect ke keranjang atau tampilkan error
    header('Location: cart.php?error=invalid_request');
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data dari POST
$metode_pengambilan = $_POST['metode_pengambilan'] ?? '';
$nomor_meja = $_POST['nomor_meja'] ?? null;
$alamat_pengiriman = $_POST['alamat_pengiriman'] ?? null;

// Validasi dasar
if (empty($metode_pengambilan)) {
    header('Location: cart.php?error=metode_missing');
    exit;
}
if ($metode_pengambilan === 'dine-in' && empty($nomor_meja)) {
    header('Location: cart.php?error=meja_missing');
    exit;
}
if ($metode_pengambilan === 'delivery' && empty($alamat_pengiriman)) {
    header('Location: cart.php?error=alamat_missing');
    exit;
}

// Ambil data keranjang user yang masih aktif
$stmt_cart = $pdo->prepare("
    SELECT k.*, p.nama, p.harga, p.gambar 
    FROM keranjang k 
    JOIN produk p ON k.produk_id = p.id 
    WHERE k.user_id = ? AND k.status = 'keranjang'
");
$stmt_cart->execute([$user_id]);
$items = $stmt_cart->fetchAll();

if (empty($items)) {
    // Jika keranjang kosong, redirect kembali ke halaman keranjang
    header('Location: cart.php?info=keranjang_kosong');
    exit;
}

// Hitung total
$subtotal = 0;
foreach ($items as $item) {
    $subtotal += $item['harga'] * $item['jumlah'];
}
$pajak = $subtotal * 0.1; // Pajak 10%
$biaya_layanan = 0; // Bisa ditambahkan jika ada biaya layanan/delivery

if ($metode_pengambilan === 'delivery') {
    // Contoh biaya pengiriman tetap, bisa dibuat lebih dinamis
    $biaya_layanan = 15000; 
}

$total_keseluruhan = $subtotal + $pajak + $biaya_layanan;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan - Tepi Laut Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="asset/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-utensils me-2"></i>Tepi Laut Café
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="menu.php">Menu</a></li>
                    <li class="nav-item"><a class="nav-link" href="cart.php">Keranjang</a></li>
                    <li class="nav-item"><a class="nav-link" href="history.php">Pesanan Saya</a></li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?= htmlspecialchars($_SESSION['username']) ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="profile.php">Profil</a></li>
                                <li><a class="dropdown-item" href="history.php">Pesanan Saya</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="auth/logout.php">Keluar</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="auth/login.php"><i class="fas fa-sign-in-alt me-2"></i>Masuk</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <h1 class="mb-4">Konfirmasi Pesanan Anda</h1>

        <div class="row">
            <!-- Rincian Pesanan -->
            <div class="col-md-7">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Item Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <?php foreach ($items as $item): ?>
                        <div class="row mb-3 border-bottom pb-3">
                            <div class="col-2">
                                <img src="images/<?= htmlspecialchars($item['gambar']) ?>" 
                                     alt="<?= htmlspecialchars($item['nama']) ?>" class="img-fluid rounded"
                                     style="width: 60px; height: 60px; object-fit: cover;">
                            </div>
                            <div class="col-6">
                                <h6 class="mb-0"><?= htmlspecialchars($item['nama']) ?></h6>
                                <small class="text-muted">Jumlah: <?= $item['jumlah'] ?></small>
                            </div>
                            <div class="col-4 text-end">
                                Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Detail Pengambilan/Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Metode:</strong> 
                            <?php 
                                if ($metode_pengambilan === 'dine-in') echo 'Makan di Tempat (Dine-in)';
                                elseif ($metode_pengambilan === 'takeaway') echo 'Bawa Pulang (Takeaway)';
                                elseif ($metode_pengambilan === 'delivery') echo 'Pesan Antar (Delivery)';
                            ?>
                        </p>
                        <?php if ($metode_pengambilan === 'dine-in' && !empty($nomor_meja)): ?>
                            <p><strong>Nomor Meja:</strong> <?= htmlspecialchars($nomor_meja) ?></p>
                        <?php endif; ?>
                        <?php if ($metode_pengambilan === 'delivery' && !empty($alamat_pengiriman)): ?>
                            <p><strong>Alamat Pengiriman:</strong><br><?= nl2br(htmlspecialchars($alamat_pengiriman)) ?></p>
                        <?php endif; ?>
                         <?php if ($metode_pengambilan === 'takeaway'): ?>
                            <p class="text-muted"><i class="fas fa-info-circle me-1"></i>Pesanan akan disiapkan untuk diambil di café.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Ringkasan Biaya dan Pembayaran -->
            <div class="col-md-5">
                <div class="card shadow-sm sticky-top" style="top: 20px;">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Ringkasan Biaya</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal</span>
                            <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Pajak (10%)</span>
                            <span>Rp <?= number_format($pajak, 0, ',', '.') ?></span>
                        </div>
                        <?php if ($metode_pengambilan === 'delivery'): ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Biaya Pengiriman</span>
                            <span>Rp <?= number_format($biaya_layanan, 0, ',', '.') ?></span>
                        </div>
                        <?php endif; ?>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold h5">
                            <span>Total Keseluruhan</span>
                            <span>Rp <?= number_format($total_keseluruhan, 0, ',', '.') ?></span>
                        </div>
                        <hr>
                        
                        <!-- Form untuk proses order selanjutnya -->
                        <form action="proses_pemesanan.php" method="POST">
                            <!-- Hidden fields untuk data yang perlu dikirim -->
                            <input type="hidden" name="metode_pengambilan" value="<?= htmlspecialchars($metode_pengambilan) ?>">
                            <input type="hidden" name="nomor_meja" value="<?= htmlspecialchars($nomor_meja) ?>">
                            <input type="hidden" name="alamat_pengiriman" value="<?= htmlspecialchars($alamat_pengiriman) ?>">
                            <input type="hidden" name="subtotal" value="<?= $subtotal ?>">
                            <input type="hidden" name="pajak" value="<?= $pajak ?>">
                            <input type="hidden" name="biaya_layanan" value="<?= $biaya_layanan ?>">
                            <input type="hidden" name="total_keseluruhan" value="<?= $total_keseluruhan ?>">
                            
                            <div class="mb-3">
                                <label for="metode_pembayaran" class="form-label">Metode Pembayaran</label>
                                <select name="metode_pembayaran" id="metode_pembayaran" class="form-select" required>
                                    <option value="">Pilih Metode Pembayaran</option>
                                    <option value="cash">Bayar di Kasir (Cash)</option>
                                    <option value="qris">QRIS (Tunjukkan ke Kasir)</option>
                                    <!-- Tambahkan metode lain jika ada -->
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100 btn-lg">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi & Buat Pesanan
                            </button>
                        </form>
                        <a href="cart.php" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-arrow-left me-2"></i>Kembali ke Keranjang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <p>&copy; <?= date('Y') ?> Tepi Laut Café. Semua hak dilindungi.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>