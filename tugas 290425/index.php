<?php
require_once 'koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Inisialisasi parameter pencarian dan filter
$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$sort = $_GET['sort'] ?? 'nama_asc';

// Jika user sudah login, ambil data keranjang
$keranjang_items = [];
$keranjang_total = 0;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT k.*, p.nama, p.harga, p.gambar 
        FROM keranjang k 
        JOIN produk p ON k.produk_id = p.id 
        WHERE k.user_id = ? AND k.status = 'keranjang'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $keranjang_items = $stmt->fetchAll();

    foreach ($keranjang_items as $item) {
        $keranjang_total += $item['harga'] * $item['jumlah'];
    }
}

// Buat query produk
$query = "SELECT * FROM produk WHERE status = 'tersedia'";
$params = [];

if (!empty($search)) {
    $query .= " AND (nama LIKE ? OR deskripsi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($kategori)) {
    $query .= " AND kategori = ?";
    $params[] = $kategori;
}

switch ($sort) {
    case 'harga_asc': $query .= " ORDER BY harga ASC"; break;
    case 'harga_desc': $query .= " ORDER BY harga DESC"; break;
    case 'nama_desc': $query .= " ORDER BY nama DESC"; break;
    default: $query .= " ORDER BY nama ASC"; break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produk = $stmt->fetchAll();

$stmt = $pdo->query("SELECT DISTINCT kategori FROM produk ORDER BY kategori");
$kategori_list = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tepi Laut Café</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="asset/style.css" rel="stylesheet">
    <style>
        .cart-dropdown { min-width: 320px; max-height: 400px; overflow-y: auto; padding: 15px; }
        .cart-item { padding: 10px; border-bottom: 1px solid #eee; }
        .cart-item-img { width: 60px; height: 60px; object-fit: cover; border-radius: 5px; }
        .cart-badge { position: absolute; top: -8px; right: -8px; font-size: 0.7rem; }
        .cart-total { font-weight: bold; padding: 10px; border-top: 2px solid #eee; margin-top: 10px; }
    </style>
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-utensils me-2"></i>Tepi Laut Café</a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">Beranda</a></li>
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
                    <li class="nav-item dropdown">
                        <a class="nav-link position-relative" href="#" data-bs-toggle="dropdown">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <?php if (count($keranjang_items) > 0): ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-badge">
                                    <?= count($keranjang_items) ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end cart-dropdown shadow">
                            <h6 class="dropdown-header">Keranjang Belanja</h6>
                            <?php if (empty($keranjang_items)): ?>
                                <div class="p-3 text-center text-muted">
                                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                                    <p>Keranjang Anda kosong</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($keranjang_items as $item): ?>
                                    <div class="dropdown-item cart-item d-flex align-items-center">
                                        <img src="images/<?= htmlspecialchars($item['gambar']) ?>" class="cart-item-img me-3" alt="<?= htmlspecialchars($item['nama']) ?>">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?= htmlspecialchars($item['nama']) ?></h6>
                                            <small class="text-muted"><?= $item['jumlah'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></small>
                                        </div>
                                        <form method="POST" action="hapus_item.php" class="ms-2">
                                            <input type="hidden" name="keranjang_id" value="<?= $item['id'] ?>">
                                            <button class="btn btn-link text-danger p-0"><i class="fas fa-trash-alt"></i></button>
                                        </form>
                                    </div>
                                <?php endforeach; ?>
                                <div class="p-3">
                                    <div class="d-flex justify-content-between">
                                        <span>Total:</span>
                                        <strong>Rp <?= number_format($keranjang_total, 0, ',', '.') ?></strong>
                                    </div>
                                    <a href="cart.php" class="btn btn-primary mt-2 w-100">Lihat Keranjang</a>
                                    <a href="checkout.php" class="btn btn-success mt-2 w-100">Checkout</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="auth/login.php"><i class="fas fa-sign-in-alt me-2"></i>Masuk</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Hero -->
<div class="hero-section py-5 bg-dark text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1>Selamat Datang di Tepi Laut Café</h1>
                <p class="lead">Nikmati kelezatan masakan Indonesia dengan cita rasa autentik</p>
                <a href="menu.php" class="btn btn-light btn-lg">Lihat Menu</a>
            </div>
            <div class="col-md-6">
                <img src="images/header.jpeg" class="img-fluid rounded" alt="Restoran">
            </div>
        </div>
    </div>
</div>

<!-- Kategori dan Filter -->
<div class="container my-5">
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Cari Menu</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Cari menu...">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua Kategori</option>
                        <?php foreach ($kategori_list as $kat): ?>
                            <option value="<?= htmlspecialchars($kat) ?>" <?= $kategori === $kat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($kat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="nama_asc" <?= $sort === 'nama_asc' ? 'selected' : '' ?>>Nama (A-Z)</option>
                        <option value="nama_desc" <?= $sort === 'nama_desc' ? 'selected' : '' ?>>Nama (Z-A)</option>
                        <option value="harga_asc" <?= $sort === 'harga_asc' ? 'selected' : '' ?>>Harga Termurah</option>
                        <option value="harga_desc" <?= $sort === 'harga_desc' ? 'selected' : '' ?>>Harga Termahal</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Terapkan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Menu Produk -->
    <div class="row">
        <?php if (empty($produk)): ?>
            <div class="col-12"><div class="alert alert-info">Tidak ada menu ditemukan.</div></div>
        <?php else: ?>
            <?php foreach ($produk as $p): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <img src="images/<?= htmlspecialchars($p['gambar']) ?>" alt="<?= htmlspecialchars($p['nama']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($p['nama']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($p['deskripsi'], 0, 80)) ?>...</p>
                            <h6 class="text-primary">Rp <?= number_format($p['harga'], 0, ',', '.') ?></h6>
                        </div>
                        <div class="card-footer bg-white border-0">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="tambah_keranjang.php" method="POST">
                                    <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-primary w-100"><i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang</button>
                                </form>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-outline-primary w-100">
                                    <i class="fas fa-sign-in-alt me-2"></i>Login untuk Memesan
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white py-4">
    <div class="container">
        <div class="row">
            <div class="col-md-4"><h5>Tepi Laut Café</h5><p>Makanan Indonesia terbaik di pinggir laut.</p></div>
            <div class="col-md-4"><h5>Jam Buka</h5><p>Senin-Minggu: 10:00 - 22:00</p></div>
            <div class="col-md-4"><h5>Kontak</h5><p>+1234-5678-9101<br>info@tepilautcafe.com<br>Jl. Ombakbiru 348, Sidoarjo</p></div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
