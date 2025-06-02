<?php require_once '../includes/header.php'; // Pastikan session_start() sudah dipanggil di header/db_connect ?>

<div class="row justify-content-center">
    <div class="col-md-6">
        <h2 class="text-center mb-4">Login Pelanggan / Admin</h2>
        <!-- Tampilkan pesan error/sukses jika ada dari session flash message -->
        <?php
        if (isset($_SESSION['error_message'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
            unset($_SESSION['error_message']); // Hapus pesan setelah ditampilkan
        }
        if (isset($_SESSION['success_message'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
            unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
        }
        // Cek jika pengguna sudah login, arahkan ke halaman utama
        if (isset($_SESSION['user_id'])) {
             header("Location: ../index.php");
             exit();
        }
        ?>

        <form action="process_login.php" method="POST">
            <div class="mb-3">
                <label for="username_or_email" class="form-label">Username atau Email</label>
                <input type="text" class="form-control" id="username_or_email" name="username_or_email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Login</button>
        </form>
        <p class="mt-3 text-center">Belum punya akun? <a href="register.php">Register di sini</a></p>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>