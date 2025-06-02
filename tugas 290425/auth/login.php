<?php
require_once '../koneksi.php';

// Tambahkan pengecekan redirect_to dari session
$redirect_to = $_SESSION['redirect_to'] ?? '../index.php';
unset($_SESSION['redirect_to']); // Hapus session setelah digunakan

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Hapus pengecekan database dan langsung set session
    $_SESSION['user_id'] = 1; // ID default
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'user'; // Role default
    
    // Redirect ke halaman yang diminta sebelumnya
    header('Location: ' . $redirect_to);
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Restoran Nusantara</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../asset/style.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">Selamat Datang</h2>
                        <p class="text-center text-muted mb-4">Silakan login untuk memesan makanan atau mengakses keranjang</p>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label>Username</label>
                                <input type="text" name="username" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Masuk</button>
                        </form>
                        <p class="text-center mt-3">
                            Belum punya akun? <a href="register.php">Daftar di sini</a>
                        </p>
                        <div class="text-center mt-3">
                            <a href="../menu.php" class="text-decoration-none">← Kembali ke Menu</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>