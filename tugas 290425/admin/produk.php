<?php
require_once '../koneksi.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek autentikasi admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil semua produk
$stmt = $pdo->query("SELECT * FROM produk ORDER BY id DESC");
$produk = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../asset/style.css" rel="stylesheet">
</head>
<body>
    <div class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola Produk</h2>
            <a href="tambah_produk.php" class="btn btn-success">Tambah Produk</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Gambar</th>
                        <th>Nama</th>
                        <th>Harga</th>
                        <th>Kategori</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produk as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['id']) ?></td>
                        <td>
                            <img src="/asset/images/<?= htmlspecialchars($item['gambar']) ?>" 
                                 alt="<?= htmlspecialchars($item['nama']) ?>" 
                                 width="100">
                        </td>
                        <td><?= htmlspecialchars($item['nama']) ?></td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= htmlspecialchars($item['kategori']) ?></td>
                        <td><?= htmlspecialchars($item['status']) ?></td>
                        <td>
                            <a href="edit_produk.php?id=<?= $item['id'] ?>" 
                               class="btn btn-sm btn-primary">Edit</a>
                            <a href="hapus_produk.php?id=<?= $item['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                            <img src="../assetsimagesmenu/<?= $item['gambar'] ?>" alt="<?= $item['nama'] ?>" style="width: 50px; height: 50px; object-fit: cover;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
