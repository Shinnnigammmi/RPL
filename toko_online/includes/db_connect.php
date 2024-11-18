<?php
// includes/db_connect.php
$host = 'localhost';
$user = 'root';  // Ganti sesuai user database Anda
$password = '';  // Ganti sesuai password database Anda
$dbname = 'toko_online';

// Buat koneksi
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
