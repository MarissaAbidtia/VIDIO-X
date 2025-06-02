<?php
require_once '../includes/header.php'; // Memastikan session dimulai

// 1. Cek Login Pengguna
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk melanjutkan ke checkout.";
    $_SESSION['redirect_url'] = '/TEPICOFFFE/pages/checkout.php'; // Simpan tujuan
    header("Location: ../auth/login.php");
    exit();
}

// 2. Cek Isi Keranjang
if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Keranjang Anda kosong. Silakan pilih menu terlebih dahulu.";
    header("Location: ../index.php"); // Arahkan ke menu jika keranjang kosong
    exit();
}

$cart = $_SESSION['cart'];
$total_price = 0;

// Hitung total harga dari keranjang
foreach ($cart as $item) {
    if (isset($item['price']) && isset($item['quantity'])) {
        $total_price += (float)$item['price'] * (int)$item['quantity'];
    }
}

// Ambil data pengguna yang mungkin sudah ada (opsional, bisa dari tabel users)
$user_id = $_SESSION['user_id'];
$user_name = ''; // Anda bisa mengambil nama dari DB jika ada
$user_phone = ''; // Anda bisa mengambil no WA dari DB jika ada

// Query untuk mengambil data user (contoh)
$sql_user = "SELECT full_name, phone_number FROM users WHERE id = ?"; // Asumsi ada kolom phone_number
$stmt_user = mysqli_prepare($conn, $sql_user);
if ($stmt_user) {
    mysqli_stmt_bind_param($stmt_user, "i", $user_id);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    if ($user_data = mysqli_fetch_assoc($result_user)) {
        $user_name = $user_data['full_name'] ?? '';
        $user_phone = $user_data['phone_number'] ?? '';
    }
    mysqli_stmt_close($stmt_user);
}

?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <h2 class="mb-4 text-center">Checkout Pesanan</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-coffee-brown text-white">
                <i class="fas fa-list-alt me-2"></i>Ringkasan Pesanan
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <?php foreach ($cart as $item_id => $item): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <?php echo htmlspecialchars($item['name'] ?? 'N/A'); ?>
                                <small class="text-muted"> (x <?php echo (int)($item['quantity'] ?? 0); ?>)</small>
                            </span>
                            <span>Rp <?php echo number_format(((float)($item['price'] ?? 0)) * ((int)($item['quantity'] ?? 0)), 0, ',', '.'); ?></span>
                        </li>
                    <?php endforeach; ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center bg-light">
                        <strong>Total Harga</strong>
                        <strong>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></strong>
                    </li>
                </ul>
            </div>
        </div>

        <div class="card shadow-sm">
             <div class="card-header bg-coffee-brown text-white">
                <i class="fas fa-user-edit me-2"></i>Detail Pemesan & Pengiriman
            </div>
            <div class="card-body">
                <form action="process_checkout.php" method="POST" id="checkout-form">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Nama Pemesan <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="customer_name" name="customer_name" value="<?php echo htmlspecialchars($user_name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="customer_whatsapp" class="form-label">Nomor WhatsApp <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control" id="customer_whatsapp" name="customer_whatsapp" placeholder="Contoh: 08123456789" value="<?php echo htmlspecialchars($user_phone); ?>" required pattern="^\+?[0-9\s\-]{10,15}$">
                         <small class="form-text text-muted">Kami akan menghubungi nomor ini untuk konfirmasi pesanan.</small>
                    </div>
                    <div class="mb-3">
                        <label for="customer_address" class="form-label">Alamat Pengiriman <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="customer_address" name="customer_address" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="customer_notes" class="form-label">Catatan Tambahan (Opsional)</label>
                        <textarea class="form-control" id="customer_notes" name="customer_notes" rows="2" placeholder="Contoh: Tidak pakai bawang, saus dipisah"></textarea>
                    </div>

                    <!-- Hidden field for total amount -->
                    <input type="hidden" name="total_amount" value="<?php echo $total_price; ?>">

                    <div class="d-grid gap-2 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-paper-plane me-2"></i> Buat Pesanan Sekarang</button>
                    </div>
                     <div class="text-center mt-3">
                        <a href="cart.php" class="btn btn-link text-secondary"><i class="fas fa-arrow-left"></i> Kembali ke Keranjang</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php require_once '../includes/footer.php'; ?>