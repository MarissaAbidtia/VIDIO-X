<?php
// Aktifkan pelaporan error untuk debugging (bisa dihapus atau dikomentari di produksi)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php?pesan=belum_login'); // Arahkan ke login jika belum
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Pengguna'; // Ambil username untuk tampilan

// 2. Ambil data pesanan untuk user yang login
// Menggunakan kolom 'created_at' sesuai struktur tabel yang diberikan
$kolomTanggalPesananDariDB = 'created_at'; 

$stmt_pesanan = $pdo->prepare("
    SELECT * 
    FROM pesanan 
    WHERE user_id = ? 
    ORDER BY " . $kolomTanggalPesananDariDB . " DESC 
");
$stmt_pesanan->execute([$user_id]);
$riwayat_pesanan = $stmt_pesanan->fetchAll();

// Untuk setiap pesanan, kita mungkin juga ingin mengambil detail itemnya
// Ini bisa dilakukan dengan query terpisah dalam loop atau dengan JOIN yang lebih kompleks
// Untuk kesederhanaan, kita akan fokus pada data pesanan utama dulu
// dan bisa ditambahkan pengambilan detail item jika diperlukan.

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Tepi Laut Café</title>
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
                    <li class="nav-item"><a class="nav-link active" href="history.php">Pesanan Saya</a></li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="fas fa-user me-2"></i><?= htmlspecialchars($username) ?>
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
        <h1 class="mb-4">Riwayat Pesanan Saya</h1>

        <?php if (empty($riwayat_pesanan)): ?>
            <div class="alert alert-info text-center">
                <i class="fas fa-history fa-3x mb-3"></i>
                <p class="lead">Anda belum memiliki riwayat pesanan.</p>
                <a href="menu.php" class="btn btn-primary">Mulai Belanja</a>
            </div>
        <?php else: ?>
            <div class="accordion" id="accordionRiwayatPesanan">
                <?php foreach ($riwayat_pesanan as $index => $pesanan): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $pesanan['id'] ?>">
                            <button class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $pesanan['id'] ?>" aria-expanded="<?= $index == 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $pesanan['id'] ?>">
                                <div class="d-flex w-100 justify-content-between">
                                    <span><strong>Kode Pesanan:</strong> <?= htmlspecialchars($pesanan['kode_pesanan']) ?></span>
                                    <?php // Menggunakan kolom 'created_at' untuk menampilkan tanggal ?>
                                    <span><strong>Tanggal:</strong> <?= date('d M Y, H:i', strtotime($pesanan[$kolomTanggalPesananDariDB])) ?></span>
                                    <span><strong>Total:</strong> Rp <?= number_format($pesanan['total_keseluruhan'], 0, ',', '.') ?></span>
                                    <span class="badge bg-<?= $pesanan['status_pembayaran'] == 'sudah dibayar' ? 'success' : ($pesanan['status_pesanan'] == 'dibatalkan' ? 'danger' : 'warning') ?>">
                                        <?= ucfirst(str_replace('_', ' ', $pesanan['status_pesanan'])) ?>
                                    </span>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $pesanan['id'] ?>" class="accordion-collapse collapse <?= $index == 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $pesanan['id'] ?>" data-bs-parent="#accordionRiwayatPesanan">
                            <div class="accordion-body">
                                <p><strong>Metode Pengambilan:</strong> <?= ucfirst(htmlspecialchars($pesanan['metode_pengambilan'])) ?></p>
                                <?php if ($pesanan['metode_pengambilan'] == 'dine-in' && !empty($pesanan['nomor_meja'])): ?>
                                    <p><strong>Nomor Meja:</strong> <?= htmlspecialchars($pesanan['nomor_meja']) ?></p>
                                <?php elseif ($pesanan['metode_pengambilan'] == 'delivery' && !empty($pesanan['alamat_pengiriman'])): ?>
                                    <p><strong>Alamat Pengiriman:</strong><br><?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])) ?></p>
                                <?php endif; ?>
                                <p><strong>Metode Pembayaran:</strong> <?= ucfirst(htmlspecialchars($pesanan['metode_pembayaran'])) ?></p>
                                <p><strong>Status Pembayaran:</strong> <span class="fw-bold text-<?= $pesanan['status_pembayaran'] == 'sudah dibayar' ? 'success' : 'danger' ?>"><?= ucfirst(str_replace('_', ' ', $pesanan['status_pembayaran'])) ?></span></p>
                                
                                <h6 class="mt-3">Detail Item:</h6>
                                <?php
                                // Ambil detail item untuk pesanan ini
                                $stmt_detail = $pdo->prepare("SELECT * FROM detail_pesanan WHERE pesanan_id = ?");
                                $stmt_detail->execute([$pesanan['id']]);
                                $detail_items = $stmt_detail->fetchAll();
                                ?>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($detail_items as $item): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span><?= htmlspecialchars($item['nama_produk']) ?> (<?= $item['jumlah'] ?> x Rp <?= number_format($item['harga_saat_pesan'], 0, ',', '.') ?>)</span>
                                            <span>Rp <?= number_format($item['subtotal_item'], 0, ',', '.') ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <hr>
                                <div class="row">
                                    <div class="col-md-6 offset-md-6">
                                        <p class="d-flex justify-content-between"><span>Subtotal:</span> <span>Rp <?= number_format($pesanan['subtotal'], 0, ',', '.') ?></span></p>
                                        <p class="d-flex justify-content-between"><span>Pajak:</span> <span>Rp <?= number_format($pesanan['pajak'], 0, ',', '.') ?></span></p>
                                        <?php if ($pesanan['biaya_layanan'] > 0): ?>
                                        <p class="d-flex justify-content-between"><span>Biaya Layanan/Pengiriman:</span> <span>Rp <?= number_format($pesanan['biaya_layanan'], 0, ',', '.') ?></span></p>
                                        <?php endif; ?>
                                        <p class="d-flex justify-content-between fw-bold"><span>Total Keseluruhan:</span> <span>Rp <?= number_format($pesanan['total_keseluruhan'], 0, ',', '.') ?></span></p>
                                    </div>
                                </div>
                                <?php if ($pesanan['status_pesanan'] == 'pending' && $pesanan['status_pembayaran'] == 'belum dibayar'): ?>
                                    <!-- Tambahkan opsi untuk bayar atau batalkan jika relevan -->
                                    <!-- <a href="bayar.php?kode_pesanan=<?= $pesanan['kode_pesanan'] ?>" class="btn btn-success btn-sm mt-2">Bayar Sekarang</a> -->
                                    <!-- <a href="batalkan_pesanan.php?pesanan_id=<?= $pesanan['id'] ?>" class="btn btn-danger btn-sm mt-2" onclick="return confirm('Anda yakin ingin membatalkan pesanan ini?')">Batalkan Pesanan</a> -->
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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