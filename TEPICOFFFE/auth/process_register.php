<?php
require_once '../config/db_connect.php'; // Mulai session ada di sini

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Ambil data dari form & lakukan sanitasi dasar
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // 2. Validasi Input (Contoh sederhana)
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $_SESSION['error_message'] = "Semua field wajib diisi.";
        header("Location: register.php");
        exit();
    }

    if ($password !== $confirm_password) {
        $_SESSION['error_message'] = "Password dan konfirmasi password tidak cocok.";
        header("Location: register.php");
        exit();
    }

    // Cek apakah username atau email sudah ada
    $sql_check = "SELECT id FROM users WHERE username = ? OR email = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    mysqli_stmt_bind_param($stmt_check, "ss", $username, $email);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
         $_SESSION['error_message'] = "Username atau Email sudah terdaftar.";
         mysqli_stmt_close($stmt_check);
         header("Location: register.php");
         exit();
    }
    mysqli_stmt_close($stmt_check);

    // 3. Hash Password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // 4. Insert ke Database (Gunakan Prepared Statement)
    $sql_insert = "INSERT INTO users (username, email, full_name, password_hash, role) VALUES (?, ?, ?, ?, 'customer')";
    $stmt_insert = mysqli_prepare($conn, $sql_insert);
    mysqli_stmt_bind_param($stmt_insert, "ssss", $username, $email, $full_name, $password_hash);

    if (mysqli_stmt_execute($stmt_insert)) {
        $_SESSION['success_message'] = "Registrasi berhasil! Silakan login.";
        header("Location: login.php"); // Arahkan ke login setelah sukses
        exit();
    } else {
        $_SESSION['error_message'] = "Terjadi kesalahan saat registrasi: " . mysqli_error($conn);
        header("Location: register.php");
        exit();
    }
    mysqli_stmt_close($stmt_insert);

} else {
    // Jika bukan POST, redirect ke halaman register
    header("Location: register.php");
    exit();
}

mysqli_close($conn);
?>