<?php
require_once 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

$pesanan_id = $_GET['id'] ?? 0;

// Ambil detail pesanan
$stmt = $pdo->prepare("
    SELECT p.*, u.nama_lengkap 
    FROM pesanan p
    JOIN users u ON p.user_id = u.id
    WHERE p.id = ? AND p.user_id = ?
");
$stmt->execute([$pesanan_id, $_SESSION['user_id']]);
$pesanan = $stmt->fetch();

if (!$pesanan) {
    header('Location: history.php');
    exit;
}

// Ambil item pesanan
$stmt = $pdo->prepare("
    SELECT dp.*, p.nama, p.gambar
    FROM detail_pesanan dp
    JOIN produk p ON dp.produk_id = p.id
    WHERE dp.pesanan_id = ?
");
$stmt->execute([$pesanan_id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan #<?= $pesanan_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="asset/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="index.php">Media Pembelajaran</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="history.php">Pesanan</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Profil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Keluar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Detail Pesanan #<?= $pesanan_id ?></h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>Tanggal:</strong> <?= date('d/m/Y H:i', strtotime($pesanan['created_at'])) ?></p>
                        <p><strong>Status:</strong> 
                            <span class="badge bg-<?= $pesanan['status'] === 'selesai' ? 'success' : 
                                ($pesanan['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                <?= ucfirst($pesanan['status']) ?>
                            </span>
                        </p>
                    </div>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['gambar']) ?>" 
                                     alt="<?= htmlspecialchars($item['nama']) ?>"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                                <?= htmlspecialchars($item['nama']) ?>
                            </td>
                            <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                            <td><?= $item['jumlah'] ?></td>
                            <td>Rp <?= number_format($item['harga'] * $item['jumlah'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
                            <td><strong>Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></strong></td>
                        </tr>
                    </tfoot>
                </table>

                <?php if ($pesanan['status'] === 'pending'): ?>
                <div class="mt-3">
                    <h6>Pembayaran</h6>
                    <p>Silakan transfer ke rekening berikut:</p>
                    <div class="alert alert-info">
                        <p class="mb-1"><strong>Bank BCA</strong></p>
                        <p class="mb-1">No. Rekening: 1234567890</p>
                        <p class="mb-1">Atas Nama: PT Media Pembelajaran</p>
                        <p class="mb-0">Jumlah: Rp <?= number_format($pesanan['total_harga'], 0, ',', '.') ?></p>
                    </div>
                    <form action="konfirmasi_pembayaran.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="pesanan_id" value="<?= $pesanan_id ?>">
                        <div class="mb-3">
                            <label class="form-label">Bukti Transfer</label>
                            <input type="file" name="bukti_transfer" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>