
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Toko Bunga</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- Google Fonts (Poppins) -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <!-- Global Font Style -->
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-white shadow-sm border-bottom">
  <div class="container">
    <a class="navbar-brand fw-bold text-success" href="index.php">
      <i class="bi bi-flower1 me-1"></i> Toko Bunga
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
      <ul class="navbar-nav align-items-center gap-3">
        <li class="nav-item">
          <a class="nav-link text-success fw-semibold" href="index.php">
            <i class="bi bi-house-door-fill me-1"></i> Home
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-success fw-semibold" href="cart.php">
            <i class="bi bi-cart-fill me-1"></i> Keranjang
          </a>
        </li>

        <?php if (isset($_SESSION['username'])): ?>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-success fw-semibold" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['username']) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i> Logout</a></li>
            </ul>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link text-success fw-semibold" href="login.php">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-success fw-semibold" href="register.php">
              <i class="bi bi-person-plus-fill me-1"></i> Register
            </a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
