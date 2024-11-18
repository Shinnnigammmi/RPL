<?php
// cart.php
include 'includes/header.php';
include 'includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    echo "<div class='alert alert-warning'>Anda harus login terlebih dahulu.</div>";
    include 'includes/footer.php';
    exit;
}

// Menangani update keranjang
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['update'])){
        foreach($_POST['quantities'] as $product_id => $quantity){
            if($quantity <= 0){
                unset($_SESSION['cart'][$product_id]);
            } else {
                $_SESSION['cart'][$product_id] = $quantity;
            }
        }
        echo "<div class='alert alert-success'>Keranjang diperbarui.</div>";
    }
    if(isset($_POST['checkout'])){
        header("Location: checkout.php");
        exit;
    }
}

// Mengambil detail produk dari keranjang
$cart_items = [];
$total = 0;

if(isset($_SESSION['cart']) && !empty($_SESSION['cart'])){
    $product_ids = array_keys($_SESSION['cart']);
    $ids = implode(',', $product_ids);
    $sql = "SELECT * FROM products WHERE id IN ($ids)";
    $result = $conn->query($sql);
    while($product = $result->fetch_assoc()){
        $product_id = $product['id'];
        $quantity = $_SESSION['cart'][$product_id];
        $subtotal = $product['price'] * $quantity;
        $total += $subtotal;
        $cart_items[] = [
            'id' => $product_id,
            'name' => $product['name'],
            'price' => $product['price'],
            'quantity' => $quantity,
            'subtotal' => $subtotal
        ];
    }
}
?>

<h2>Keranjang Belanja</h2>

<?php if(!empty($cart_items)): ?>
    <form method="POST" action="">
        <table class="table">
            <thead>
                <tr>
                    <th>Produk</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($cart_items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td>Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                        <td>
                            <input type="number" name="quantities[<?php echo $item['id']; ?>]" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="0" class="form-control" required>
                        </td>
                        <td>Rp <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td><strong>Rp <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
                </tr>
            </tbody>
        </table>
        <button type="submit" name="update" class="btn btn-primary">Perbarui Keranjang</button>
        <button type="submit" name="checkout" class="btn btn-success">Checkout</button>
    </form>
<?php else: ?>
    <p>Keranjang belanja Anda kosong.</p>
<?php endif; ?>

<?php
include 'includes/footer.php';
?>
