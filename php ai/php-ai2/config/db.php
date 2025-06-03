<?php
$koneksi = new mysqli("localhost", "root", "", "flower_shop");
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
?>