<?php
// product.php
include 'includes/header.php';
include 'includes/db_connect.php';
session_start();

if(isset($_GET['id'])){
    $product_id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->num_rows === 1){
        $product = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger'>Produk tidak ditemukan.</div>";
        include 'includes/footer.php';
        exit;
    }
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>ID produk tidak ada.</div>";
    include 'includes/footer.php';
    exit;
}

// Menangani penambahan ke keranjang
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(!isset($_SESSION['user_id'])){
        echo "<div class='alert alert-warning'>Anda harus login terlebih dahulu.</div>";
    } else {
        $quantity = intval($_POST['quantity']);
        if($quantity > 0){
            if(!isset($_SESSION['cart'])){
                $_SESSION['cart'] = [];
            }
            if(isset($_SESSION['cart'][$product_id])){
                $_SESSION['cart'][$product_id] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
            echo "<div class='alert alert-success'>Produk berhasil ditambahkan ke keranjang.</div>";
        } else {
            echo "<div class='alert alert-danger'>Jumlah harus lebih dari 0.</div>";
        }
    }
}
?>

<div class="product-detail">
    <h2><?php echo htmlspecialchars($product['name']); ?></h2>
    <?php if ($product['image']): ?>
        <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
             alt="<?php echo htmlspecialchars($product['name']); ?>" 
             class="product-detail-image">
    <?php else: ?>
        <img src="https://via.placeholder.com/300" alt="No Image" class="product-detail-image">
    <?php endif; ?>
    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
    <p>Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
    <form method="POST" action="">
        <div class="form-group">
            <label for="quantity">Jumlah:</label>
            <input type="number" name="quantity" id="quantity" 
                   class="form-control" value="1" min="1" required>
        </div>
        <button type="submit" class="btn btn-success">Tambah ke Keranjang</button>
    </form>
</div>

<?php
include 'includes/footer.php';
?>
