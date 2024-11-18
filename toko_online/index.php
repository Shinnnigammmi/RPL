<?php
// index.php
include 'includes/header.php';
include 'includes/db_connect.php';

// Mengambil semua produk
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<h1>Produk Tersedia</h1>
<div class="product-list">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($product = $result->fetch_assoc()): ?>
            <div class="product">
                <?php if ($product['image']): ?>
                    <img src="assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                         alt="<?php echo htmlspecialchars($product['name']); ?>" 
                         class="product-image">
                <?php else: ?>
                    <img src="https://via.placeholder.com/150" alt="No Image" class="product-image">
                <?php endif; ?>
                <h2><?php echo htmlspecialchars($product['name']); ?></h2>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></p>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary">Detail</a>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Belum ada produk.</p>
    <?php endif; ?>
</div>

<?php
include 'includes/footer.php';
?>
