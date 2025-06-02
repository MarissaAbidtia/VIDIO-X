<?php
session_start();
include 'includes/navbar.php';
include 'config/db.php';

// Tambah produk ke keranjang jika ada id dari URL
if (isset($_GET['id'])) {
  $id = intval($_GET['id']);
  if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
  if (isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id]++;
  } else {
    $_SESSION['cart'][$id] = 1;
  }
}
?>

<div class="container my-5">
  <h2 class="mb-4 text-success fw-bold">Keranjang Belanja</h2>
  <?php if (!empty($_SESSION['cart'])): ?>
  <table class="table table-striped align-middle">
    <thead class="table-success">
      <tr>
        <th>Gambar</th>
        <th>Nama Bunga</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Subtotal</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $total = 0;
      foreach ($_SESSION['cart'] as $id => $qty):
        $stmt = $koneksi->prepare("SELECT * FROM flowers WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $flower = $stmt->get_result()->fetch_assoc();
        $subtotal = $flower['price'] * $qty;
        $total += $subtotal;
      ?>
      <tr>
        <td><img src="images/<?= htmlspecialchars($flower['image']) ?>" alt="<?= htmlspecialchars($flower['name']) ?>" width="80"></td>
        <td><?= htmlspecialchars($flower['name']) ?></td>
        <td>Rp <?= number_format($flower['price'], 0, ',', '.') ?></td>
        <td><?= $qty ?></td>
        <td>Rp <?= number_format($subtotal, 0, ',', '.') ?></td>
      </tr>
      <?php endforeach; ?>
      <tr class="table-success fw-bold">
        <td colspan="4" class="text-end">Total</td>
        <td>Rp <?= number_format($total, 0, ',', '.') ?></td>
      </tr>
    </tbody>
  </table>
  <a href="checkout.php" class="btn btn-success"><i class="bi bi-bag-check"></i> Checkout</a>
  <?php else: ?>
    <p class="text-muted">Keranjang kosong.</p>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
