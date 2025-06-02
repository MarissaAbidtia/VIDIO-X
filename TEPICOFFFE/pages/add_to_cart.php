<?php
require_once '../config/db_connect.php'; // Ensures session_start() is called

// 1. Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Store the intended destination to redirect after login
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'] ?? '../index.php';
    $_SESSION['error_message'] = "Silakan login terlebih dahulu untuk menambahkan item ke keranjang.";
    header("Location: ../auth/login.php");
    exit();
}

// 2. Get item_id from GET request and validate
if (!isset($_GET['item_id']) || !filter_var($_GET['item_id'], FILTER_VALIDATE_INT)) {
    $_SESSION['error_message'] = "Item tidak valid.";
    header("Location: ../index.php"); // Redirect back to menu
    exit();
}
$item_id = (int)$_GET['item_id'];
$user_id = $_SESSION['user_id']; // Get user id

// 3. Get item details from database (Use Prepared Statement)
// Ensure you have 'menu_items' table with 'id', 'title', 'price', 'status' columns
$sql = "SELECT title, price FROM menu_items WHERE id = ? AND status = 'available'";
$stmt = mysqli_prepare($conn, $sql);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $item_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($item = mysqli_fetch_assoc($result)) {
        // Item found and available

        // 4. Initialize cart in session if it doesn't exist
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // 5. Add/Update item in cart session
        if (isset($_SESSION['cart'][$item_id])) {
            // Item already in cart, increment quantity
            $_SESSION['cart'][$item_id]['quantity']++;
        } else {
            // Add new item to cart
            $_SESSION['cart'][$item_id] = [
                'name' => $item['title'],
                'price' => (float)$item['price'], // Store price as float
                'quantity' => 1
            ];
        }

        $_SESSION['success_message'] = "'" . htmlspecialchars($item['title']) . "' berhasil ditambahkan ke keranjang.";

    } else {
        // Item not found or not available
        $_SESSION['error_message'] = "Menu tidak ditemukan atau sedang tidak tersedia.";
    }
    mysqli_stmt_close($stmt);
} else {
     // Error preparing statement
     $_SESSION['error_message'] = "Terjadi kesalahan database.";
     // Log the error: error_log("Database prepare error: " . mysqli_error($conn));
}


mysqli_close($conn);

// 6. Redirect back to the menu page (or the page they came from)
$redirect_page = $_SERVER['HTTP_REFERER'] ?? '../index.php';
header("Location: " . $redirect_page);
exit();
?>