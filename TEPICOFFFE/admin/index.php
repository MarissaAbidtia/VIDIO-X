<?php
// admin/index.php (Contoh Dashboard Admin Sederhana)
require_once '../includes/header_admin.php'; // Kita akan buat header khusus admin

// Proteksi Halaman Admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    $_SESSION['error_message'] = "Anda tidak memiliki izin untuk mengakses halaman admin.";
    header("Location: ../auth/login.php");
    exit();
}

// Ambil nama admin dari session
$admin_username = $_SESSION['username'] ?? 'Admin';

?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard Admin</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Overview</li>
    </ol>

    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-receipt fa-2x mb-2"></i><br>
                    Pesanan Baru
                    <?php
                    // Contoh: Hitung pesanan dengan status 'pending'
                    // Anda perlu menambahkan koneksi DB jika header_admin belum melakukannya
                    // require_once '../config/db_connect.php'; // Jika perlu
                    // $sql_pending = "SELECT COUNT(*) as count FROM customer_orders WHERE order_status = 'pending'";
                    // $result_pending = mysqli_query($conn, $sql_pending);
                    // $pending_count = ($result_pending) ? mysqli_fetch_assoc($result_pending)['count'] : 0;
                    // echo "<span class='badge bg-light text-primary ms-2'>$pending_count</span>";
                    // mysqli_close($conn); // Tutup koneksi jika dibuka di sini
                    ?>
                     <span class='badge bg-light text-primary ms-2'>Contoh</span> <!-- Placeholder -->
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="manage_orders.php">Lihat Detail</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-dark mb-4">
                <div class="card-body">
                    <i class="fas fa-utensils fa-2x mb-2"></i><br>
                    Kelola Menu
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-dark stretched-link" href="manage_menu.php">Lihat & Edit Menu</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
         <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                     <i class="fas fa-images fa-2x mb-2"></i><br>
                    Kelola Banner
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="manage_banners.php">Atur Banner Promo</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
         <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <i class="fas fa-users fa-2x mb-2"></i><br>
                    Kelola Pengguna
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="manage_users.php">Lihat Pengguna</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-info">
        Selamat datang kembali, <?php echo htmlspecialchars($admin_username); ?>! Gunakan menu navigasi atau kartu di atas untuk mengelola toko.
    </div>

    <!-- Area untuk konten lainnya -->

</div>

<?php require_once '../includes/footer_admin.php'; // Kita akan buat footer khusus admin ?>