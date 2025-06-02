<?php
// includes/header_admin.php
// Pastikan koneksi db dan session sudah dimulai jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once dirname(__DIR__) . '/config/db_connect.php'; // Sesuaikan path ke db_connect.php

// Ambil nama pengguna dari session untuk ditampilkan
$admin_username = $_SESSION['username'] ?? 'Admin';

// Tentukan halaman aktif untuk styling menu (opsional)
$current_page = basename($_SERVER['PHP_SELF']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Admin Panel Coastal Coffee" />
    <meta name="author" content="Your Name" />
    <title>Admin Panel - Coastal Coffee</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Custom Admin CSS (jika Anda membuatnya nanti) -->
    <link href="/TEPICOFFFE/assets/css/admin_style.css" rel="stylesheet" /> <!-- Sesuaikan path jika perlu -->
    <!-- Favicon (opsional) -->
    <!-- <link rel="icon" type="image/x-icon" href="/TEPICOFFFE/assets/favicon.ico"> -->
</head>
<body class="sb-nav-fixed"> <!-- Class untuk layout admin template (misal: Start Bootstrap SB Admin) -->

<nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Brand-->
    <a class="navbar-brand ps-3" href="/TEPICOFFFE/admin/index.php">
       <i class="fas fa-coffee me-1"></i> Admin Coastal Coffee
    </a>
    <!-- Sidebar Toggle (jika menggunakan template dengan sidebar)-->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

    <!-- Navbar-->
    <ul class="navbar-nav ms-auto me-3 me-lg-4">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-user fa-fw me-1"></i><?php echo htmlspecialchars($admin_username); ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                <li><a class="dropdown-item" href="#!">Pengaturan (TBD)</a></li>
                <li><hr class="dropdown-divider" /></li>
                <li><a class="dropdown-item" href="/TEPICOFFFE/auth/logout.php">Logout</a></li>
            </ul>
        </li>
    </ul>
</nav>

<!-- Layout Utama (Contoh dengan Sidebar Kiri) -->
<div id="layoutSidenav">
    <div id="layoutSidenav_nav">
        <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
            <div class="sb-sidenav-menu">
                <div class="nav">
                    <div class="sb-sidenav-menu-heading">Utama</div>
                    <a class="nav-link <?php echo ($current_page == 'index.php') ? 'active' : ''; ?>" href="/TEPICOFFFE/admin/index.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                        Dashboard
                    </a>
                    <div class="sb-sidenav-menu-heading">Manajemen</div>
                    <a class="nav-link <?php echo ($current_page == 'manage_orders.php') ? 'active' : ''; ?>" href="/TEPICOFFFE/admin/manage_orders.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-receipt"></i></div>
                        Pesanan Masuk
                    </a>
                    <a class="nav-link <?php echo ($current_page == 'manage_menu.php') ? 'active' : ''; ?>" href="/TEPICOFFFE/admin/manage_menu.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-utensils"></i></div>
                        Kelola Menu
                    </a>
                    <a class="nav-link <?php echo ($current_page == 'manage_banners.php') ? 'active' : ''; ?>" href="/TEPICOFFFE/admin/manage_banners.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-images"></i></div>
                        Kelola Banner
                    </a>
                     <a class="nav-link <?php echo ($current_page == 'manage_users.php') ? 'active' : ''; ?>" href="/TEPICOFFFE/admin/manage_users.php">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Kelola Pengguna
                    </a>
                    <!-- Tambahkan menu lain jika perlu -->

                </div>
            </div>
            <div class="sb-sidenav-footer">
                <div class="small">Logged in as:</div>
                <?php echo htmlspecialchars($admin_username); ?>
            </div>
        </nav>
    </div>
    <div id="layoutSidenav_content">
        <main>
            <!-- Konten utama halaman akan dimulai di sini -->
            <!-- Tampilkan flash messages admin -->
            <div class="container-fluid px-4">
                <?php
                if (isset($_SESSION['admin_error_message'])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">' . htmlspecialchars($_SESSION['admin_error_message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    unset($_SESSION['admin_error_message']);
                }
                if (isset($_SESSION['admin_success_message'])) {
                    echo '<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">' . htmlspecialchars($_SESSION['admin_success_message']) . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
                    unset($_SESSION['admin_success_message']);
                }
                ?>
            </div>