# Aplikasi Media Pembelajaran

Aplikasi web untuk platform media pembelajaran online.

## Fitur

- Login dan registrasi pengguna
- Manajemen produk pembelajaran
- Keranjang belanja
- Checkout dan pembayaran
- Riwayat pembelian
- Panel admin untuk manajemen konten

## Instalasi

1. Clone repository ini
2. Import file `db_media_pembelajaran.sql` ke MySQL
3. Sesuaikan konfigurasi database di `config/koneksi.php`
4. Akses aplikasi melalui web browser

## Struktur Folder

```plaintext
/media-pembelajaran/
├── config/                 # Koneksi database
├── admin/                 # Halaman admin
├── assets/                # Gambar, CSS, JS
├── auth/                  # Login, register, logout
└── ...                    # File-file utama