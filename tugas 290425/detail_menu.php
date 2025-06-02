<?php
require_once 'koneksi.php';

// Ambil ID menu dari parameter URL
$id = $_GET['id'] ?? 0;

// Ambil detail menu
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ? AND status = 'tersedia'");
$stmt->execute([$id]);
$menu = $stmt->fetch();

// Jika menu tidak ditemukan, redirect ke halaman menu
if (!$menu) {
    header('Location: menu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($menu['nama']) ?> - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="asset/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-utensils me-2"></i>Restoran Nusantara
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="menu.php">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="history.php">Pesanan Saya</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">Keluar</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">Masuk</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="row">
            <div class="col-md-6">
                <img src="asset/<?= htmlspecialchars($menu['gambar']) ?>"
                     class="img-fluid rounded shadow-sm"
                     alt="<?= htmlspecialchars($menu['nama']) ?>">
            </div>
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="menu.php">Menu</a></li>
                        <li class="breadcrumb-item active"><?= htmlspecialchars($menu['nama']) ?></li>
                    </ol>
                </nav>
                <h1 class="mb-3"><?= htmlspecialchars($menu['nama']) ?></h1>
                
                <div class="mb-3">
                    <?php if ($menu['label'] === 'spesial'): ?>
                        <span class="badge bg-warning me-2">Spesial</span>
                    <?php endif; ?>
                    <?php if ($menu['label'] === 'pedas'): ?>
                        <span class="badge bg-danger me-2">Pedas</span>
                    <?php endif; ?>
                    <?php if ($menu['label'] === 'vegetarian'): ?>
                        <span class="badge bg-success me-2">Vegetarian</span>
                    <?php endif; ?>
                    <span class="badge bg-primary"><?= htmlspecialchars($menu['kategori']) ?></span>
                </div>

                <h3 class="text-primary mb-3">
                    Rp <?= number_format($menu['harga'], 0, ',', '.') ?>
                </h3>

                <div class="mb-4">
                    <h5>Deskripsi</h5>
                    <p><?= nl2br(htmlspecialchars($menu['deskripsi'])) ?></p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="tambah_keranjang.php" method="POST" class="mb-4">
                        <input type="hidden" name="produk_id" value="<?= $menu['id'] ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Pesanan</label>
                            <div class="input-group" style="width: 150px;">
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="decrementQuantity()">-</button>
                                <input type="number" name="jumlah" id="quantity" 
                                       class="form-control text-center" value="1" min="1" max="10">
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="incrementQuantity()">+</button>
                            </div>
                        </div>

                        <?php if ($menu['label'] === 'pedas'): ?>
                            <div class="mb-3">
                                <label class="form-label">Level Kepedasan</label>
                                <select name="level_pedas" class="form-select">
                                    <option value="1">Level 1 (Tidak Pedas)</option>
                                    <option value="2">Level 2 (Sedang)</option>
                                    <option value="3">Level 3 (Pedas)</option>
                                    <option value="4">Level 4 (Sangat Pedas)</option>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Catatan Khusus</label>
                            <textarea name="catatan" class="form-control" 
                                      rows="3" placeholder="Tambahkan catatan khusus untuk pesanan Anda"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="fas fa-shopping-cart me-2"></i>Tambah ke Keranjang
                        </button>
                    </form>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Silakan <a href="auth/login.php">login</a> untuk memesan menu ini.
                    </div>
                <?php endif; ?>
            </div>
        </div>
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
        function incrementQuantity() {
            var input = document.getElementById('quantity');
            var value = parseInt(input.value, 10);
            if (value < 10) input.value = value + 1;
        }

        function decrementQuantity() {
            var input = document.getElementById('quantity');
            var value = parseInt(input.value, 10);
            if (value > 1) input.value = value - 1;
        }
    </script>
</body>
</html>