<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php'; // Untuk navbar jika ada data user

// Ambil pesan sukses dari session
$pesan_sukses = $_SESSION['pesanan_sukses'] ?? '';
$kode_pesanan = $_GET['kode_pesanan'] ?? '';

// Hapus pesan dari session agar tidak tampil lagi jika halaman di-refresh
unset($_SESSION['pesanan_sukses']);

if (empty($pesan_sukses) && empty($kode_pesanan)) {
    // Jika tidak ada info sukses, mungkin redirect ke beranda
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Tepi Laut Café</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="asset/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php"><i class="fas fa-utensils me-2"></i>Tepi Laut Café</a>
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
                                <i class="fas fa-user me-2"></i><?= htmlspecialchars($_SESSION['username'] ?? 'Pengguna') ?>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm text-center">
                    <div class="card-body p-5">
                        <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                        <h1 class="card-title">Pesanan Berhasil!</h1>
                        <?php if (!empty($pesan_sukses)): ?>
                            <p class="lead"><?= $pesan_sukses ?></p>
                        <?php elseif(!empty($kode_pesanan)): ?>
                             <p class="lead">Pesanan Anda dengan kode <strong><?= htmlspecialchars($kode_pesanan) ?></strong> telah berhasil dibuat.</p>
                        <?php endif; ?>
                        <p>Terima kasih telah memesan di Tepi Laut Café. Silakan lakukan pembayaran sesuai metode yang Anda pilih.</p>
                        <hr>
                        <div class="mt-4">
                            <a href="index.php" class="btn btn-primary me-2">
                                <i class="fas fa-home me-1"></i> Kembali ke Beranda
                            </a>
                            <a href="history.php" class="btn btn-outline-secondary">
                                <i class="fas fa-history me-1"></i> Lihat Riwayat Pesanan
                            </a>
                        </div>
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