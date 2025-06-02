<?php
require_once '../includes/header.php'; // Ensures session_start()

// Redirect if user not logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk melihat keranjang.";
    header("Location: ../auth/login.php");
    exit();
}

// Initialize cart if it doesn't exist or is not an array
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$cart = $_SESSION['cart'];
$total_price = 0;
?>

<h2 class="mb-4">Keranjang Pesanan Anda</h2>

<?php if (empty($cart)): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-shopping-cart fa-3x mb-3"></i><br>
        Keranjang pesanan Anda masih kosong.<br>
        Yuk, <a href="../index.php" class="alert-link">pilih menu favoritmu</a>!
    </div>
<?php else: ?>
    <div class="table-responsive shadow-sm rounded">
        <table class="table table-bordered table-hover align-middle mb-0">
            <thead class="table-light" style="border-bottom: 2px solid var(--coffee-brown);">
                <tr>
                    <th class="text-center" style="width: 10%;">Gambar</th>
                    <th>Menu</th>
                    <th>Harga Satuan</th>
                    <th class="text-center" style="width: 15%;">Jumlah</th>
                    <th class="text-end">Subtotal</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $item_id => $item): ?>
                    <?php
                        // Basic validation for item structure
                        $item_name = isset($item['name']) ? htmlspecialchars($item['name']) : 'N/A';
                        $item_price = isset($item['price']) ? (float)$item['price'] : 0;
                        $item_quantity = isset($item['quantity']) ? (int)$item['quantity'] : 0;
                        $subtotal = $item_price * $item_quantity;
                        $total_price += $subtotal;
                        // You might want to add image path to the cart session as well
                        $image_path = isset($item['image']) ? htmlspecialchars($item['image']) : '/TEPICOFFFE/assets/images/placeholder_food.png';
                    ?>
                    <tr>
                        <td class="text-center"><img src="<?php echo $image_path; ?>" alt="<?php echo $item_name; ?>" style="width: 50px; height: 50px; object-fit: cover;"></td>
                        <td><?php echo $item_name; ?></td>
                        <td>Rp <?php echo number_format($item_price, 0, ',', '.'); ?></td>
                        <td class="text-center">
                            <!-- Form for updating quantity -->
                            <form action="update_cart.php" method="POST" class="d-inline-flex align-items-center justify-content-center">
                                <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                <input type="number" name="quantity" value="<?php echo $item_quantity; ?>" min="1" max="99" class="form-control form-control-sm me-2" style="width: 70px;" required onchange="this.form.submit()"> <!-- Auto-submit on change -->
                                <!-- Or use a button:
                                <button type="submit" class="btn btn-sm btn-outline-secondary" title="Update Jumlah"><i class="fas fa-sync-alt"></i></button>
                                -->
                            </form>
                        </td>
                        <td class="text-end">Rp <?php echo number_format($subtotal, 0, ',', '.'); ?></td>
                        <td class="text-center">
                            <!-- Form for removing item -->
                            <form action="remove_from_cart.php" method="POST" class="d-inline">
                                <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Item"><i class="fas fa-trash-alt"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- Order Summary -->
    <div class="row mt-4 justify-content-end">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Ringkasan Pesanan</h5>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Harga</span>
                        <strong>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></strong>
                    </div>
                    <!-- Add other costs like delivery fee if applicable later -->
                    <div class="d-grid gap-2 mt-3">
                         <a href="checkout.php" class="btn btn-primary btn-lg"><i class="fas fa-check"></i> Lanjut ke Checkout</a>
                    </div>
                </div>
            </div>
             <div class="text-center mt-3">
                <a href="../index.php" class="btn btn-link text-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Menu</a>
            </div>
        </div>
    </div>


<?php endif; ?>


<?php require_once '../includes/footer.php'; ?>