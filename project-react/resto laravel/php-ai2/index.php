<?php
include 'includes/navbar.php';
include 'config/db.php';
?>

<!-- Hero Section -->
<section class="hero-section bg-success text-white py-5" style="min-height: 400px; display: flex; align-items: center; border-radius: 15px; overflow: hidden;">
  <div class="container text-center">
    <h1 class="display-4 fw-bold mb-3">Koleksi Bunga Terbaik untuk Setiap Momen</h1>
    <p class="lead mb-4">Dapatkan bunga segar dan eksklusif dengan layanan cepat dan terpercaya.</p>
    <a href="#koleksi-bunga" class="btn btn-light btn-lg fw-semibold px-4">
      <i class="bi bi-flower1 me-2"></i> Jelajahi Koleksi Kami
    </a>
  </div>

  <div class="hero-image d-none d-md-block" style="flex: 1; max-width: 50%;">
    <img src="assets/hero-flower.png" alt="Bunga Segar" style="width: 100%; height: 400px; object-fit: cover; border-radius: 0 15px 15px 0;">
  </div>
</section>

<!-- Koleksi Bunga -->
<div id="koleksi-bunga" class="container my-5">
  <h2 class="text-center text-success fw-bold mb-4">
    <i class="bi bi-flower1"></i> Koleksi Bunga Terbaik Kami
  </h2>

  <div class="row row-cols-1 row-cols-md-3 g-4">
    <?php
    $result = $koneksi->query("SELECT * FROM flowers ORDER BY id DESC");
    while ($row = $result->fetch_assoc()):
    ?>
    <div class="col">
      <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden">
        <img src="images/<?= htmlspecialchars($row['image']) ?>" 
             class="card-img-top img-fluid" 
             alt="<?= htmlspecialchars($row['name']) ?>"
             style="height: 220px; object-fit: cover;">

        <div class="card-body d-flex flex-column">
          <h5 class="card-title text-success"><?= htmlspecialchars($row['name']) ?></h5>
          <p class="card-text text-danger fw-semibold fs-5 mb-2">Rp <?= number_format($row['price'], 0, ',', '.') ?></p>
          <p class="card-text text-muted flex-grow-1" style="min-height: 70px;"><?= nl2br(htmlspecialchars(substr($row['description'], 0, 80))) ?>...</p>

          <div class="d-flex justify-content-between mt-3">
            <a href="detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-success btn-sm d-flex align-items-center gap-1">
              <i class="bi bi-eye-fill"></i> Detail
            </a>
            <a href="cart.php?id=<?= $row['id'] ?>" class="btn btn-success btn-sm d-flex align-items-center gap-1">
              <i class="bi bi-cart-plus-fill"></i> Beli
            </a>
          </div>
        </div>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
