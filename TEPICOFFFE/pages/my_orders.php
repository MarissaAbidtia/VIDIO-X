<?php
require_once '../includes/header.php'; // Memastikan session dimulai dan koneksi DB

// 1. Cek Login Pengguna
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk melihat riwayat pesanan Anda.";
    $_SESSION['redirect_url'] = '/TEPICOFFFE/pages/my_orders.php'; // Simpan tujuan
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$orders = [];

// 2. Ambil Data Pesanan Utama dari Database
// Urutkan berdasarkan tanggal terbaru
$sql_orders = "SELECT id, order_date, total_amount, order_status, customer_name, customer_whatsapp, customer_address, customer_notes
               FROM customer_orders
               WHERE user_id = ?
               ORDER BY order_date DESC";

$stmt_orders = mysqli_prepare($conn, $sql_orders);

if ($stmt_orders) {
    mysqli_stmt_bind_param($stmt_orders, "i", $user_id);
    mysqli_stmt_execute($stmt_orders);
    $result_orders = mysqli_stmt_get_result($stmt_orders);

    // 3. Ambil Detail Item untuk Setiap Pesanan
    while ($order = mysqli_fetch_assoc($result_orders)) {
        $order_id = $order['id'];
        $order['details'] = []; // Inisialisasi array untuk detail

        $sql_details = "SELECT menu_item_id, item_name, quantity, price_per_item
                        FROM customer_order_details
                        WHERE order_id = ?";
        $stmt_details = mysqli_prepare($conn, $sql_details);

        if ($stmt_details) {
            mysqli_stmt_bind_param($stmt_details, "i", $order_id);
            mysqli_stmt_execute($stmt_details);
            $result_details = mysqli_stmt_get_result($stmt_details);
            while ($detail = mysqli_fetch_assoc($result_details)) {
                $order['details'][] = $detail;
            }
            mysqli_stmt_close($stmt_details);
        } else {
            // Handle error jika statement detail gagal disiapkan
             error_log("Gagal menyiapkan statement detail order untuk order ID: $order_id - " . mysqli_error($conn));
        }
        $orders[] = $order; // Tambahkan pesanan beserta detailnya ke array utama
    }
    mysqli_stmt_close($stmt_orders);

} else {
    // Handle error jika statement order utama gagal disiapkan
    $_SESSION['error_message'] = "Gagal mengambil data pesanan.";
    error_log("Gagal menyiapkan statement order utama untuk user ID: $user_id - " . mysqli_error($conn));
    // Mungkin redirect atau tampilkan pesan error di halaman
}

mysqli_close($conn);

// Fungsi untuk format status
function formatOrderStatus($status) {
    switch (strtolower($status)) {
        case 'pending':
            return '<span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>';
        case 'processing':
            return '<span class="badge bg-info text-dark">Sedang Diproses</span>';
        case 'completed':
            return '<span class="badge bg-success">Selesai</span>';
        case 'cancelled':
            return '<span class="badge bg-danger">Dibatalkan</span>';
        default:
            return '<span class="badge bg-secondary">' . htmlspecialchars($status) . '</span>';
    }
}
?>

<h2 class="mb-4">Riwayat Pesanan Saya</h2>

<?php if (empty($orders)): ?>
    <div class="alert alert-info text-center">
        <i class="fas fa-history fa-3x mb-3"></i><br>
        Anda belum memiliki riwayat pesanan.<br>
        <a href="../index.php" class="alert-link">Mulai pesan sekarang!</a>
    </div>
<?php else: ?>
    <div class="accordion" id="ordersAccordion">
        <?php foreach ($orders as $index => $order): ?>
            <div class="accordion-item mb-3 shadow-sm rounded">
                <h2 class="accordion-header" id="heading<?php echo $order['id']; ?>">
                    <button class="accordion-button <?php echo $index > 0 ? 'collapsed' : ''; ?>" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $order['id']; ?>" aria-expanded="<?php echo $index == 0 ? 'true' : 'false'; ?>" aria-controls="collapse<?php echo $order['id']; ?>">
                        <div class="d-flex w-100 justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-receipt me-2"></i> Pesanan #<?php echo $order['id']; ?> -
                                <small class="text-muted"><?php echo date('d M Y, H:i', strtotime($order['order_date'])); ?></small>
                            </span>
                            <span>
                                <?php echo formatOrderStatus($order['order_status']); ?>
                            </span>
                        </div>
                    </button>
                </h2>
                <div id="collapse<?php echo $order['id']; ?>" class="accordion-collapse collapse <?php echo $index == 0 ? 'show' : ''; ?>" aria-labelledby="heading<?php echo $order['id']; ?>" data-bs-parent="#ordersAccordion">
                    <div class="accordion-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <h5>Detail Pemesan:</h5>
                                <p class="mb-1"><strong>Nama:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                                <p class="mb-1"><strong>WhatsApp:</strong> <?php echo htmlspecialchars($order['customer_whatsapp']); ?></p>
                                <p class="mb-1"><strong>Alamat:</strong> <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                                <?php if (!empty($order['customer_notes'])): ?>
                                    <p class="mb-1"><strong>Catatan:</strong> <?php echo nl2br(htmlspecialchars($order['customer_notes'])); ?></p>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <h5>Ringkasan Pesanan:</h5>
                                <ul class="list-group list-group-flush">
                                    <?php foreach ($order['details'] as $detail): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-1">
                                            <span>
                                                <?php echo htmlspecialchars($detail['item_name']); ?>
                                                <small class="text-muted"> (x <?php echo $detail['quantity']; ?>)</small>
                                            </span>
                                            <small>Rp <?php echo number_format($detail['price_per_item'] * $detail['quantity'], 0, ',', '.'); ?></small>
                                        </li>
                                    <?php endforeach; ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-light fw-bold">
                                        <span>Total Pembayaran</span>
                                        <span>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                         <hr>
                         <p class="text-muted text-center mb-0"><small>Status Pesanan: <?php echo formatOrderStatus($order['order_status']); ?></small></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>