<?php
session_start();
include 'includes/navbar.php';
include 'config/db.php';

if (isset($_POST['register'])) {
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];

    if (empty($username) || empty($email) || empty($password)) {
        $errors[] = "Semua field harus diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email tidak valid.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi tidak cocok.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter.";
    }

    // Cek username/email sudah ada?
    $stmt = $koneksi->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $errors[] = "Username atau email sudah terdaftar.";
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password_hash);
        if ($stmt->execute()) {
            $_SESSION['success'] = "Registrasi berhasil, silakan login.";
            header("Location: login.php");
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat registrasi.";
        }
    }
}
?>

<div class="container my-5" style="max-width: 500px;">
  <h2 class="text-success mb-4 fw-bold">Register</h2>

  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
      <?php foreach($errors as $e): ?>
        <li><?= $e ?></li>
      <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input type="text" name="username" id="username" class="form-control" required value="<?= $_POST['username'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input type="email" name="email" id="email" class="form-control" required value="<?= $_POST['email'] ?? '' ?>">
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input type="password" name="password" id="password" class="form-control" required>
    </div>
    <div class="mb-3">
      <label for="confirm_password" class="form-label">Konfirmasi Password</label>
      <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
    </div>
    <button type="submit" name="register" class="btn btn-success w-100"><i class="bi bi-person-plus"></i> Register</button>
    <p class="mt-3">Sudah punya akun? <a href="login.php">Login di sini</a>.</p>
  </form>
</div>

<?php include 'includes/footer.php'; ?>
