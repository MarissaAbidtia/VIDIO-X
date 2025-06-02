<?php
// Pastikan koneksi db dan session sudah dimulai jika belum
if (!isset($conn)) {
    require_once dirname(__DIR__) . '/config/db_connect.php'; // Use corrected path
}

// Calculate cart item count
$cart_item_count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    // Count unique items in the cart
    $cart_item_count = count($_SESSION['cart']);
    // If you want to count total quantity instead:
    // $cart_item_count = 0;
    // foreach ($_SESSION['cart'] as $item) {
    //     $cart_item_count += $item['quantity'];
    // }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coastal Coffee Resto - Pesan Online</title> <!-- Updated Name -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome (untuk ikon) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/TEPICOFFFE/assets/css/style.css"> <!-- Ensure this path is correct -->
</head>
<body class="bg-light-pink">

<nav class="navbar navbar-expand-lg navbar-dark bg-coffee-brown shadow-sm">
  <div class="container">
    <a class="navbar-brand" href="/TEPICOFFFE/index.php">
        <i class="fas fa-utensils"></i> Coastal Coffee <!-- Updated Name -->
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center"> <!-- Added align-items-center -->
        <li class="nav-item">
          <a class="nav-link active" aria-current="page" href="/TEPICOFFFE/index.php">Menu & Info</a>
        </li>
        <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <!-- Updated Cart Link with Item Count -->
              <a class="nav-link position-relative" href="/TEPICOFFFE/pages/cart.php">
                  <i class="fas fa-shopping-cart"></i> Keranjang
                  <?php if ($cart_item_count > 0): ?>
                      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                          <?php echo $cart_item_count; ?>
                          <span class="visually-hidden">items in cart</span>
                      </span>
                  <?php endif; ?>
              </a>
            </li>
             <li class="nav-item">
              <a class="nav-link" href="/TEPICOFFFE/pages/my_orders.php">Pesanan Saya</a>
            </li>
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] == 'admin'): ?>
                 <li class="nav-item">
                   <a class="nav-link" href="/TEPICOFFFE/admin/index.php">Admin Panel</a>
                 </li>
            <?php endif; ?>
            <!-- User Dropdown -->
            <li class="nav-item dropdown">
               <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                 <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($_SESSION['username']); ?>
               </a>
               <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                 <li><a class="dropdown-item" href="/TEPICOFFFE/pages/my_account.php">Akun Saya</a></li> <!-- Link to optional account page -->
                 <li><hr class="dropdown-divider"></li>
                 <li><a class="dropdown-item" href="/TEPICOFFFE/auth/logout.php">Logout</a></li>
               </ul>
             </li>
        <?php else: ?>
            <!-- Login/Register Links for Guests -->
            <li class="nav-item">
              <a class="nav-link" href="/TEPICOFFFE/auth/login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn btn-outline-light btn-sm" href="/TEPICOFFFE/auth/register.php">Register</a> <!-- Make register stand out slightly -->
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-4">
<!-- Display flash messages here -->
<?php
if (isset($_SESSION['error_message'])) {
    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['error_message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['error_message']); // Clear message after displaying
}
if (isset($_SESSION['success_message'])) {
    echo '<div class="alert alert-success alert-dismissible fade show" role="alert">' . htmlspecialchars($_SESSION['success_message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    unset($_SESSION['success_message']); // Clear message after displaying
}
?>