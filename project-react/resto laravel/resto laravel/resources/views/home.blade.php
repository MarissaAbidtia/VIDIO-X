<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMK Revit - Home</title>
    <!-- Tambahkan Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .logo img {
            height: 40px;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
        }
        .main-content {
            display: flex;
            min-height: calc(100vh - 100px);
        }
        .sidebar {
            width: 200px;
            padding: 1rem;
            background-color: #f5f5f5;
        }
        .content {
            flex: 1;
            padding: 1rem;
        }
        .footer {
            padding: 1rem;
            background-color: #f5f5f5;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="logo">
            <img src="/images/smkrevit-logo.png" alt="SMK Revit">
        </div>
        <nav class="nav-links">
            <a href="/cart">Cart</a>
            <a href="/register">Register</a>
            <a href="/email">Email</a>
            <a href="/login">Login</a>
            <a href="/logout">Logout</a>
        </nav>
    </header>

    <div class="main-content">
        <aside class="sidebar">
            <h3>Kategori</h3>
            <!-- Kategori menu akan ditampilkan di sini -->
        </aside>

        <main class="content">
            <div class="container-fluid">
                <div class="row row-cols-1 row-cols-md-3 g-4">
                    @foreach($menus as $menu)
                    <div class="col">
                        <div class="card h-100">
                            <img src="{{ $menu->gambar }}" class="card-img-top" alt="{{ $menu->nama }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $menu->nama }}</h5>
                                <p class="card-text">{{ $menu->deskripsi }}</p>
                                <p class="text-danger">Rp {{ number_format($menu->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </main>
    </div>

    <footer class="footer">
        <p>&copy; 2024 SMK Revit. All rights reserved.</p>
    </footer>
    <!-- Tambahkan Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
