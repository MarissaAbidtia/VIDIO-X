<?php
require_once '../koneksi.php';

// Jika user belum login, simpan halaman ini sebagai redirect dan arahkan ke login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_to'] = '../keranjang/';
    header('Location: ../auth/login.php');
    exit;
}

// Ambil data keranjang user
$stmt = $pdo->prepare("
    SELECT k.*, p.nama, p.harga, p.gambar, p.deskripsi, p.kategori 
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

// Ambil rekomendasi menu
$kategori_dipesan = array_unique(array_column($items, 'kategori'));
if (!empty($kategori_dipesan)) {
    $placeholders = str_repeat('?,', count($kategori_dipesan) - 1) . '?';
    $stmt = $pdo->prepare("
        SELECT * FROM produk 
        WHERE kategori IN ($placeholders) 
        AND id NOT IN (SELECT produk_id FROM keranjang WHERE user_id = ?)
        AND status = 'tersedia'
        ORDER BY RAND() 
        LIMIT 4
    ");
    $params = array_merge($kategori_dipesan, [$_SESSION['user_id']]);
    $stmt->execute($params);
    $rekomendasi = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../asset/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-utensils me-2"></i>Restoran Nusantara
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../history.php">Pesanan Saya</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../profile.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h1 class="mb-4">Keranjang Belanja</h1>

        <?php if (empty($items)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Keranjang belanja Anda masih kosong. 
                <a href="../menu.php" class="alert-link">Lihat menu kami</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-8">
                    <!-- Daftar Item -->
                    <?php foreach ($items as $item): ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="row g-0">
                                <div class="col-md-3">
                                    <img src="<?= htmlspecialchars($item['gambar']) ?>" 
                                         class="img-fluid rounded-start" 
                                         alt="<?= htmlspecialchars($item['nama']) ?>"
                                         style="height: 150px; object-fit: cover;">
                                </div>
                                <div class="col-md-9">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <h5 class="card-title"><?= htmlspecialchars($item['nama']) ?></h5>
                                            <form action="hapus_item.php" method="POST" class="d-inline">
                                                <input type="hidden" name="keranjang_id" value="<?= $item['id'] ?>">
                                                <button type="submit" class="btn btn-link text-danger p-0">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                        <p class="card-text">
                                            <span class="text-primary">
                                                Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                                            </span>
                                        </p>
                                        <div class="row g-3">
                                            <div class="col-auto">
                                                <form action="update_jumlah.php" method="POST" class="d-flex align-items-center">
                                                    <input type="hidden" name="keranjang_id" value="<?= $item['id'] ?>">
                                                    <div class="input-group" style="width: 120px;">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                                onclick="this.parentNode.querySelector('input[type=number]').stepDown()">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" name="jumlah" class="form-control form-control-sm text-center" 
                                                               value="<?= $item['jumlah'] ?>" min="1" max="10">
                                                        <button type="button" class="btn btn-outline-secondary btn-sm" 
                                                                onclick="this.parentNode.querySelector('input[type=number]').stepUp()">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                    <button type="submit" class="btn btn-sm btn-primary ms-2">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="col">
                                                <form action="update_catatan.php" method="POST">
                                                    <input type="hidden" name="keranjang_id" value="<?= $item['id'] ?>">
                                                    <div class="input-group">
                                                        <input type="text" name="catatan" class="form-control form-control-sm" 
                                                               value="<?= htmlspecialchars($item['catatan'] ?? '') ?>" 
                                                               placeholder="Tambah catatan">
                                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                            <i class="fas fa-save"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <!-- Rekomendasi Menu -->
                    <?php if (!empty($rekomendasi)): ?>
                        <div class="mt-5">
                            <h4>Rekomendasi Menu</h4>
                            <div class="row">
                                <?php foreach ($rekomendasi as $menu): ?>
                                    <div class="col-md-3 mb-3">
                                        <div class="card h-100 border-0 shadow-sm">
                                            <img src="<?= htmlspecialchars($menu['gambar']) ?>" 
                                                 class="card-img-top" 
                                                 alt="<?= htmlspecialchars($menu['nama']) ?>"
                                                 style="height: 150px; object-fit: cover;">
                                            <div class="card-body">
                                                <h6 class="card-title"><?= htmlspecialchars($menu['nama']) ?></h6>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                                                    </small>
                                                </p>
                                                <form action="../tambah_keranjang.php" method="POST">
                                                    <input type="hidden" name="produk_id" value="<?= $menu['id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                                        <i class="fas fa-plus me-1"></i>Tambah
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-md-4">
                    <!-- Ringkasan Pesanan -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-4">Ringkasan Pesanan</h5>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Subtotal</span>
                                    <span>Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Pajak (10%)</span>
                                    <span>Rp <?= number_format($pajak, 0, ',', '.') ?></span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Total</span>
                                    <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                                </div>
                            </div>

                            <form action="checkout.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Metode Pengambilan</label>
                                    <select name="metode_pengambilan" class="form-select" required>
                                        <option value="">Pilih metode pengambilan</option>
                                        <option value="dine-in">Dine-in</option>
                                        <option value="takeaway">Takeaway</option>
                                        <option value="delivery">Delivery</option>
                                    </select>
                                </div>

                                <div class="mb-3" id="nomorMeja" style="display: none;">
                                    <label class="form-label">Nomor Meja</label>
                                    <input type="number" name="nomor_meja" class="form-control" min="1">
                                </div>

                                <div class="mb-3" id="alamatDelivery" style="display: none;">
                                    <label class="form-label">Alamat Pengiriman</label>
                                    <textarea name="alamat_pengiriman" class="form-control" rows="3"></textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-shopping-cart me-2"></i>Lanjut ke Pembayaran
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fas fa-utensils me-2"></i>Restoran Nusantara</h5>
                    <p>Menyajikan masakan Indonesia terbaik dengan cita rasa autentik.</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-clock me-2"></i>Jam Buka</h5>
                    <p>Senin - Minggu<br>10:00 - 22:00 WIB</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fas fa-address-book me-2"></i>Kontak</h5>
                    <p>
                        <i class="fas fa-phone me-2"></i>+62 123 4567 890<br>
                        <i class="fas fa-envelope me-2"></i>info@restoranku.com<br>
                        <i class="fas fa-map-marker-alt me-2"></i>Jl. Nusantara No. 123, Jakarta
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tampilkan/sembunyikan field berdasarkan metode pengambilan
        document.querySelector('select[name="metode_pengambilan"]').addEventListener('change', function() {
            const nomorMeja = document.getElementById('nomorMeja');
            const alamatDelivery = document.getElementById('alamatDelivery');
            
            nomorMeja.style.display = this.value === 'dine-in' ? 'block' : 'none';
            alamatDelivery.style.display = this.value === 'delivery' ? 'block' : 'none';
            
            // Reset field yang tidak digunakan
            if (this.value !== 'dine-in') {
                nomorMeja.querySelector('input').value = '';
            }
            if (this.value !== 'delivery') {
                alamatDelivery.querySelector('textarea').value = '';
            }
        });
    </script>
</body>
</html>