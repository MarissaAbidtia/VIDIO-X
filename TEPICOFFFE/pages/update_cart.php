<?php
require_once '../config/db_connect.php'; // Ensures session_start() is called

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Silakan login untuk mengubah keranjang.";
    header("Location: ../auth/login.php");
    exit();
}

// Check if it's a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate item_id
    if (!isset($_POST['item_id']) || !filter_var($_POST['item_id'], FILTER_VALIDATE_INT)) {
        $_SESSION['error_message'] = "Item ID tidak valid.";
        header("Location: cart.php");
        exit();
    }
    $item_id = (int)$_POST['item_id'];

    // Validate quantity
    if (!isset($_POST['quantity']) || !filter_var($_POST['quantity'], FILTER_VALIDATE_INT) || $_POST['quantity'] < 1) {
        // If quantity is invalid or less than 1, treat it as removal
        if (isset($_SESSION['cart'][$item_id])) {
            unset($_SESSION['cart'][$item_id]);
            $_SESSION['success_message'] = "Item berhasil dihapus dari keranjang.";
        } else {
             $_SESSION['error_message'] = "Jumlah tidak valid.";
        }
        header("Location: cart.php");
        exit();
    }
    $quantity = (int)$_POST['quantity'];

    // Check if cart exists and the item is in the cart
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart']) && isset($_SESSION['cart'][$item_id])) {
        // Update the quantity
        $_SESSION['cart'][$item_id]['quantity'] = $quantity;
        $_SESSION['success_message'] = "Jumlah item berhasil diperbarui.";
    } else {
        $_SESSION['error_message'] = "Item tidak ditemukan di keranjang.";
    }

} else {
    // If not a POST request, redirect
    $_SESSION['error_message'] = "Metode request tidak valid.";
}

// Redirect back to the cart page
header("Location: cart.php");
exit();
?>