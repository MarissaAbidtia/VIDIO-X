$produk = [
    ['Nasi Goreng', 'nasi_goreng.jpg', 'Makanan', 'spesial', 25000, 'Nasi goreng enak', 'tersedia'],
    ['Es Teh', 'es_teh.jpg', 'Minuman', '', 5000, 'Minuman segar', 'tersedia'],
];

foreach ($produk as $p) {
    $stmt = $pdo->prepare("INSERT INTO produk (nama, gambar, kategori, label, harga, deskripsi, status)
                           VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute($p);
}
