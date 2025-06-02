<?php
session_start();
require_once 'koneksi.php';

$search = $_GET['search'] ?? '';
$kategori = $_GET['kategori'] ?? '';
$sort = $_GET['sort'] ?? 'nama_asc';
$harga_min = $_GET['harga_min'] ?? '';
$harga_max = $_GET['harga_max'] ?? '';
$label = $_GET['label'] ?? [];

// Query dasar
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
if (!empty($harga_min)) {
    $query .= " AND harga >= ?";
    $params[] = $harga_min;
}
if (!empty($harga_max)) {
    $query .= " AND harga <= ?";
    $params[] = $harga_max;
}
if (!empty($label)) {
    $placeholders = implode(',', array_fill(0, count($label), '?'));
    $query .= " AND label IN ($placeholders)";
    $params = array_merge($params, $label);
}

switch ($sort) {
    case 'harga_asc': $query .= " ORDER BY harga ASC"; break;
    case 'harga_desc': $query .= " ORDER BY harga DESC"; break;
    case 'nama_desc': $query .= " ORDER BY nama DESC"; break;
    case 'popularitas': $query .= " ORDER BY jumlah_pesanan DESC"; break;
    default: $query .= " ORDER BY nama ASC";
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$produk = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Menu - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="asset/style.css" rel="stylesheet">
    <style>
        .menu-label { position: absolute; top: 10px; left: 10px; z-index: 2; }
        .menu-card { transition: transform 0.2s; cursor: pointer; }
        .menu-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
<!-- Navigasi -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="fas fa-utensils me-2"></i>Restoran Nusantara</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                <li class="nav-item"><a class="nav-link active" href="menu.php">Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="cart.php">Keranjang</a></li>
                <li class="nav-item"><a class="nav-link" href="history.php">Pesanan Saya</a></li>
            </ul>
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="profile.php">Profil</a></li>
                    <li class="nav-item"><a class="nav-link" href="auth/logout.php">Keluar</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="auth/login.php">Masuk</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container my-4">
    <div class="text-center mb-4">
        <h1>Menu Kami</h1>
        <p class="lead">Pilihan menu terbaik dengan cita rasa autentik</p>
    </div>

    <!-- Filter -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Cari Menu</label>
                    <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua</option>
                        <option value="Makanan Pembuka" <?= $kategori === 'Makanan Pembuka' ? 'selected' : '' ?>>Makanan Pembuka</option>
                        <option value="Main Course" <?= $kategori === 'Main Course' ? 'selected' : '' ?>>Main Course</option>
                        <option value="Dessert" <?= $kategori === 'Dessert' ? 'selected' : '' ?>>Dessert</option>
                        <option value="Minuman" <?= $kategori === 'Minuman' ? 'selected' : '' ?>>Minuman</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Rentang Harga</label>
                    <div class="d-flex">
                        <input type="number" name="harga_min" class="form-control me-2" placeholder="Min" value="<?= htmlspecialchars($harga_min) ?>">
                        <input type="number" name="harga_max" class="form-control" placeholder="Max" value="<?= htmlspecialchars($harga_max) ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="nama_asc" <?= $sort === 'nama_asc' ? 'selected' : '' ?>>Nama A-Z</option>
                        <option value="nama_desc" <?= $sort === 'nama_desc' ? 'selected' : '' ?>>Nama Z-A</option>
                        <option value="harga_asc" <?= $sort === 'harga_asc' ? 'selected' : '' ?>>Harga Termurah</option>
                        <option value="harga_desc" <?= $sort === 'harga_desc' ? 'selected' : '' ?>>Harga Termahal</option>
                        <option value="popularitas" <?= $sort === 'popularitas' ? 'selected' : '' ?>>Terpopuler</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Label</label>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="label[]" value="spesial" <?= in_array('spesial', $label) ? 'checked' : '' ?>> <label class="form-check-label">Spesial</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="label[]" value="pedas" <?= in_array('pedas', $label) ? 'checked' : '' ?>> <label class="form-check-label">Pedas</label></div>
                    <div class="form-check"><input class="form-check-input" type="checkbox" name="label[]" value="vegetarian" <?= in_array('vegetarian', $label) ? 'checked' : '' ?>> <label class="form-check-label">Vegetarian</label></div>
                </div>
                <div class="col-12 text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daftar Menu -->
    <div class="row">
        <?php if (empty($produk)): ?>
            <div class="col-12"><div class="alert alert-info">Menu tidak ditemukan.</div></div>
        <?php else: ?>
            <?php foreach ($produk as $p): ?>
                <div class="col-md-3 mb-4">
                    <div class="card menu-card h-100 shadow-sm" onclick="window.location='detail_menu.php?id=<?= $p['id'] ?>'">
                        <div class="position-relative">
                            <?php
                                $path = 'images/' . $p['gambar'];
                                $img = (!empty($p['gambar']) && file_exists($path)) ? $path : 'images/placeholder.png';
                            ?>
                            <img src="<?= $img ?>" alt="<?= htmlspecialchars($p['nama']) ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                            <div class="menu-label">
                                <?php if ($p['label']): ?>
                                    <span class="badge bg-<?= $p['label'] === 'pedas' ? 'danger' : ($p['label'] === 'vegetarian' ? 'success' : 'warning') ?>">
                                        <?= ucfirst($p['label']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                            <div class="position-absolute top-0 end-0 p-2">
                                <span class="badge bg-primary"><?= htmlspecialchars($p['kategori']) ?></span>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5><?= htmlspecialchars($p['nama']) ?></h5>
                            <p class="text-muted"><?= htmlspecialchars(substr($p['deskripsi'], 0, 60)) ?>...</p>
                            <p class="text-primary fw-bold">Rp <?= number_format($p['harga'], 0, ',', '.') ?></p>
                        </div>
                        <div class="card-footer bg-white">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="tambah_keranjang.php" method="POST" onsubmit="event.stopPropagation();">
                                    <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
                                    <div class="row g-1">
                                        <div class="col-4">
                                            <input type="number" name="jumlah" class="form-control" value="1" min="1" max="10">
                                        </div>
                                        <div class="col-8">
                                            <button class="btn btn-primary w-100"><i class="fas fa-cart-plus me-2"></i>Tambah</button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <a href="auth/login.php" class="btn btn-outline-primary w-100"><i class="fas fa-sign-in-alt me-2"></i>Login untuk pesan</a>
                            <?php endif; ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <form action="hapus_produk.php" method="POST" onsubmit="return confirm('Yakin ingin menghapus?');" class="mt-2">
                                    <input type="hidden" name="id" value="<?= $p['id'] ?>">
                                    <button class="btn btn-danger w-100"><i class="fas fa-trash-alt me-2"></i>Hapus</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container text-center">
        <p>&copy; <?= date('Y') ?> Restoran Nusantara - Cita rasa asli Indonesia.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
