<?php
// includes/header.php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Toko Online Sederhana</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- Bootstrap CSS (Opsional, jika ingin menggunakan) -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
<nav>
    <a href="index.php">Beranda</a>
    <?php if (isset($_SESSION['user_id'])): ?>
        <a href="cart.php">Keranjang</a>
        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1): ?>
            <a href="admin/manage_products.php">Kelola Produk</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
    <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Daftar</a>
    <?php endif; ?>
</nav>
<div class="container">
