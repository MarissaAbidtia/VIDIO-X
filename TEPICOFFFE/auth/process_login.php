<?php
require_once '../config/db_connect.php'; // Pastikan session_start() sudah dipanggil

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data dari form & sanitasi
    $username_or_email = mysqli_real_escape_string($conn, $_POST['username_or_email']);
    $password = $_POST['password'];

    // 2. Validasi dasar
    if (empty($username_or_email) || empty($password)) {
        $_SESSION['error_message'] = "Username/Email dan Password wajib diisi.";
        header("Location: login.php");
        exit();
    }

    // 3. Cari user berdasarkan username atau email (Gunakan Prepared Statement)
    $sql = "SELECT id, username, password_hash, role FROM users WHERE username = ? OR email = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username_or_email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($user = mysqli_fetch_assoc($result)) {
        // 4. Verifikasi Password
        if (password_verify($password, $user['password_hash'])) {
            // Password cocok, login berhasil

            // 5. Simpan data user ke session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];

            // Hapus pesan error jika ada sebelumnya
            unset($_SESSION['error_message']);

            // 6. Arahkan berdasarkan role
            if ($user['role'] == 'admin') {
                // Arahkan admin ke panel admin
                header("Location: ../admin/index.php");
            } else {
                // Cek apakah ada URL redirect yang disimpan
                $redirect_url = $_SESSION['redirect_url'] ?? '../index.php';
                unset($_SESSION['redirect_url']); // Hapus setelah digunakan
                header("Location: " . $redirect_url); // Arahkan customer ke tujuan atau halaman utama
            }
            exit();

        } else {
            // Password tidak cocok
            $_SESSION['error_message'] = "Username/Email atau Password salah.";
            header("Location: login.php");
            exit();
        }
    } else {
        // User tidak ditemukan
        $_SESSION['error_message'] = "Username/Email atau Password salah.";
        header("Location: login.php");
        exit();
    }

    mysqli_stmt_close($stmt);

} else {
    // Jika bukan POST, redirect ke halaman login
    header("Location: login.php");
    exit();
}

mysqli_close($conn);
?>