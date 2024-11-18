<?php
// admin/delete_product.php
include '../includes/header.php';
include '../includes/db_connect.php';
session_start();

// Cek apakah pengguna adalah admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    echo "<div class='alert alert-danger'>Anda tidak memiliki akses ke halaman ini.</div>";
    include '../includes/footer.php';
    exit;
}

if(!isset($_GET['id'])){
    echo "<div class='alert alert-danger'>ID produk tidak ditemukan.</div>";
    include '../includes/footer.php';
    exit;
}

$product_id = intval($_GET['id']);

// Mengambil data produk untuk menghapus gambar jika ada
$stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows !== 1){
    echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
    include '../includes/footer.php';
    exit;
}
$product = $result->fetch_assoc();
$stmt->close();

// Menghapus produk
$stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
$stmt->bind_param("i", $product_id);
if($stmt->execute()){
    // Menghapus gambar dari server jika ada
    if($product['image'] && file_exists('../assets/images/' . $product['image'])){
        unlink('../assets/images/' . $product['image']);
    }
    echo "<div class='alert alert-success'>Produk berhasil dihapus.</div>";
    echo "<a href='manage_products.php' class='btn btn-primary'>Kembali ke Kelola Produk</a>";
} else {
    echo "<div class='alert alert-danger'>Terjadi kesalahan saat menghapus produk.</div>";
    echo "<a href='manage_products.php' class='btn btn-primary'>Kembali ke Kelola Produk</a>";
}
$stmt->close();

include '../includes/footer.php';
?>
