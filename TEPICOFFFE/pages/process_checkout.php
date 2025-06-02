<?php
require_once '../config/db_connect.php'; // Memastikan session dimulai dan koneksi DB

// 1. Cek Login & Metode Request
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Sesi Anda telah berakhir. Silakan login kembali.";
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error_message'] = "Metode request tidak valid.";
    header("Location: checkout.php");
    exit();
}

// 2. Cek Isi Keranjang
if (!isset($_SESSION['cart']) || empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['error_message'] = "Keranjang Anda kosong atau terjadi kesalahan.";
    header("Location: ../index.php");
    exit();
}

$cart = $_SESSION['cart'];
$user_id = $_SESSION['user_id'];

// 3. Ambil dan Validasi Data dari Form POST
$customer_name = trim($_POST['customer_name'] ?? '');
$customer_whatsapp = trim($_POST['customer_whatsapp'] ?? '');
$customer_address = trim($_POST['customer_address'] ?? '');
$customer_notes = trim($_POST['customer_notes'] ?? ''); // Opsional
$total_amount_from_form = filter_var($_POST['total_amount'] ?? 0, FILTER_VALIDATE_FLOAT);

// Validasi dasar input wajib
if (empty($customer_name) || empty($customer_whatsapp) || empty($customer_address) || $total_amount_from_form === false) {
    $_SESSION['error_message'] = "Harap lengkapi semua field yang wajib diisi (Nama, WhatsApp, Alamat).";
    // Simpan input sebelumnya ke session agar form bisa diisi ulang (opsional)
    $_SESSION['checkout_input'] = $_POST;
    header("Location: checkout.php");
    exit();
}

// 4. Hitung ulang total dari session cart (lebih aman daripada hanya mengandalkan hidden field)
$calculated_total = 0;
foreach ($cart as $item) {
    if (isset($item['price']) && isset($item['quantity'])) {
        $calculated_total += (float)$item['price'] * (int)$item['quantity'];
    }
}

// Bandingkan total yang dihitung ulang dengan yang dari form (toleransi kecil jika perlu)
if (abs($calculated_total - $total_amount_from_form) > 0.01) {
     $_SESSION['error_message'] = "Terjadi perbedaan total harga. Silakan coba lagi.";
     error_log("Checkout total mismatch: Session=" . $calculated_total . " Form=" . $total_amount_from_form . " UserID=" . $user_id);
     header("Location: cart.php"); // Arahkan kembali ke keranjang jika total tidak cocok
     exit();
}

// 5. Proses Penyimpanan ke Database (Gunakan Transaksi)
mysqli_autocommit($conn, false); // Mulai transaksi
$order_saved = false;
$order_id = null;

try {
    // Insert ke tabel customer_orders
    $sql_order = "INSERT INTO customer_orders (user_id, customer_name, customer_whatsapp, customer_address, customer_notes, total_amount, order_status) VALUES (?, ?, ?, ?, ?, ?, 'pending')";
    $stmt_order = mysqli_prepare($conn, $sql_order);

    if (!$stmt_order) {
        throw new Exception("Gagal menyiapkan statement order: " . mysqli_error($conn));
    }

    mysqli_stmt_bind_param($stmt_order, "issssds", $user_id, $customer_name, $customer_whatsapp, $customer_address, $customer_notes, $calculated_total);

    if (!mysqli_stmt_execute($stmt_order)) {
         throw new Exception("Gagal menyimpan order: " . mysqli_stmt_error($stmt_order));
    }

    $order_id = mysqli_insert_id($conn); // Dapatkan ID order yang baru saja dibuat
    mysqli_stmt_close($stmt_order);

    if (!$order_id) {
        throw new Exception("Gagal mendapatkan ID order baru.");
    }

    // Insert ke tabel customer_order_details
    $sql_details = "INSERT INTO customer_order_details (order_id, menu_item_id, item_name, quantity, price_per_item) VALUES (?, ?, ?, ?, ?)";
    $stmt_details = mysqli_prepare($conn, $sql_details);

    if (!$stmt_details) {
         throw new Exception("Gagal menyiapkan statement detail order: " . mysqli_error($conn));
    }

    foreach ($cart as $item_id => $item) {
        $menu_item_id = (int)$item_id;
        $item_name = $item['name'] ?? 'N/A';
        $quantity = (int)($item['quantity'] ?? 0);
        $price_per_item = (float)($item['price'] ?? 0);

        if ($quantity <= 0) continue; // Lewati jika jumlah 0 atau kurang

        mysqli_stmt_bind_param($stmt_details, "iisid", $order_id, $menu_item_id, $item_name, $quantity, $price_per_item);
        if (!mysqli_stmt_execute($stmt_details)) {
            throw new Exception("Gagal menyimpan detail item (ID: $menu_item_id): " . mysqli_stmt_error($stmt_details));
        }
    }
    mysqli_stmt_close($stmt_details);

    // Jika semua berhasil, commit transaksi
    if (mysqli_commit($conn)) {
        $order_saved = true;
    } else {
        throw new Exception("Gagal melakukan commit transaksi: " . mysqli_error($conn));
    }

} catch (Exception $e) {
    mysqli_rollback($conn); // Batalkan semua perubahan jika ada error
    error_log("Checkout Error: " . $e->getMessage() . " UserID=" . $user_id); // Catat error
    $_SESSION['error_message'] = "Terjadi kesalahan saat memproses pesanan Anda. Silakan coba lagi. Error: " . $e->getMessage();
} finally {
     mysqli_autocommit($conn, true); // Kembalikan ke mode autocommit
}


// 6. Finalisasi dan Redirect
if ($order_saved && $order_id) {
    // Hapus keranjang dari session
    unset($_SESSION['cart']);
    // Hapus input checkout yang tersimpan (jika ada)
    unset($_SESSION['checkout_input']);

    // Set pesan sukses
    $_SESSION['success_message'] = "Pesanan Anda (ID: #$order_id) berhasil dibuat! Kami akan segera menghubungi Anda via WhatsApp untuk konfirmasi.";

    // Redirect ke halaman sukses atau riwayat pesanan
    header("Location: my_orders.php"); // Atau buat halaman order_success.php
    exit();
} else {
    // Jika gagal menyimpan, arahkan kembali ke checkout
    // Pesan error sudah diatur di blok catch
    header("Location: checkout.php");
    exit();
}

mysqli_close($conn);
?>