<?php
// includes/footer_admin.php
?>
            <!-- Konten utama halaman berakhir di sini -->
        </main>
        <footer class="py-4 bg-light mt-auto">
            <div class="container-fluid px-4">
                <div class="d-flex align-items-center justify-content-between small">
                    <div class="text-muted">Copyright &copy; Coastal Coffee <?php echo date('Y'); ?></div>
                    <div>
                        <a href="#">Privacy Policy</a>
                        &middot;
                        <a href="#">Terms &amp; Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    </div> <!-- Akhir dari #layoutSidenav_content -->
</div> <!-- Akhir dari #layoutSidenav -->

<!-- Bootstrap Bundle JS (termasuk Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<!-- Custom Admin JS (untuk toggle sidebar, dll. jika menggunakan template) -->
<script src="/TEPICOFFFE/assets/js/admin_scripts.js"></script> <!-- Sesuaikan path jika perlu -->
<!-- Tambahkan JS lain jika diperlukan (misal: Chart.js, DataTables) -->

</body>
</html>
<?php
// Tutup koneksi database jika dibuka di header_admin.php
if (isset($conn) && $conn instanceof mysqli) {
   // mysqli_close($conn); // Komentari jika koneksi ditutup di tempat lain atau tidak perlu ditutup di sini
}
?>