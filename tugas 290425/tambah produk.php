<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'koneksi.php';

// Cek jika bukan admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Proses simpan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'];
    $kategori = $_POST['kategori'];
    $label = $_POST['label'];
    $harga = $_POST['harga'];
    $deskripsi = $_POST['deskripsi'];
    $status = $_POST['status'];

    // Upload gambar
    $gambar = $_FILES['gambar']['name'];
    $tmp = $_FILES['gambar']['tmp_name'];
    $path = 'asset/' . $gambar;
    move_uploaded_file($tmp, $path);

    // Simpan ke database
    $stmt = $pdo->prepare("INSERT INTO produk (nama, gambar, kategori, label, harga, deskripsi, status)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$nama, $gambar, $kategori, $label, $harga, $deskripsi, $status]);

    header('Location: menu.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Produk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Tambah Produk Baru</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nama Produk</label>
            <input type="text" name="nama" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Gambar</label>
            <input type="file" name="gambar" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Kategori</label>
            <select name="kategori" class="form-select" required>
                <option value="Makanan">Makanan</option>
                <option value="Minuman">Minuman</option>
                <option value="Dessert">Dessert</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Label</label>
            <select name="label" class="form-select">
                <option value="">-</option>
                <option value="spesial">Spesial</option>
                <option value="pedas">Pedas</option>
                <option value="vegetarian">Vegetarian</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Deskripsi</label>
            <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status" class="form-select" required>
                <option value="tersedia">Tersedia</option>
                <option value="habis">Habis</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Produk</button>
    </form>
</div>
</body>
</html>
