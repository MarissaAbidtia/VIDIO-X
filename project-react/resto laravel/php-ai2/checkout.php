<?php
session_start();
include 'includes/navbar.php';

// Pastikan keranjang tidak kosong
if (empty($_SESSION['cart'])) {
  echo '<div class="container my-5"><p class="text-danger">Keranjang kosong, silakan tambah produk dulu.</p></div>';
  include 'includes/footer.php';
  exit;
}
?>

<div class="container my-5">
  <h2 class="mb-4 text-success fw-bold">Form Checkout</h2>
  <form action="checkout_proses.php" method="post" class="mx-auto" style="max-width: 500px;">
    <div class="mb-3">
      <label for="nama" class="form-label">Nama Lengkap</label>
      <input type="text" class="form-control" id="nama" name="nama" required />
    </div>
    <div class="mb-3">
      <label for="alamat" class="form-label">Alamat Pengiriman</label>
      <textarea class="form-control" id="alamat" name="alamat" rows="3" required></textarea>
    </div>
    <div class="mb-3">
      <label for="nohp" class="form-label">No. HP</label>
      <input type="tel" class="form-control" id="nohp" name="nohp" required />
    </div>
    <button type="submit" class="btn btn-success w-100"><i class="bi bi-send-check"></i> Konfirmasi Pesanan</button>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
