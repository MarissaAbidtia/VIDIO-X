<?php
session_start();

if (empty($_SESSION['cart'])) {
  header('Location: index.php');
  exit;
}

$nama = htmlspecialchars($_POST['nama']);
$alamat = htmlspecialchars($_POST['alamat']);
$nohp = htmlspecialchars($_POST['nohp']);

// Bisa ditambahkan simpan data pesanan ke database jika mau

// Hapus keranjang
unset($_SESSION['cart']);
include 'includes/navbar.php';
?>

<div class="container my-5">
  <h2 class="text-success fw-bold">Terima Kasih, <?= $nama ?>!</h2>
  <p>Pesanan Anda telah kami terima dan akan segera kami proses.</p>
  <p><strong>Alamat Pengiriman:</strong> <?= $alamat ?></p>
  <p><strong>No. HP:</strong> <?= $nohp ?></p>
  <a href="index.php" class="btn btn-success"><i class="bi bi-house-door"></i> Kembali ke Beranda</a>
</div>

<?php include 'includes/footer.php'; ?>
