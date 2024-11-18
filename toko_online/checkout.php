<?php
// checkout.php
include 'includes/header.php';
include 'includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    echo "<div class='alert alert-warning'>Anda harus login terlebih dahulu.</div>";
    include 'includes/footer.php';
    exit;
}

if(!isset($_SESSION['cart']) || empty($_SESSION['cart'])){
    echo "<div class='alert alert-warning'>Keranjang belanja Anda kosong.</div>";
    include 'includes/footer.php';
    exit;
}

// Mengambil detail produk dari keranjang
$cart_items = [];
$total = 0;

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

// Menangani proses checkout
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    // Mulai transaksi
    $conn->begin_transaction();
    try {
        // Insert ke tabel orders
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $stmt->bind_param("id", $_SESSION['user_id'], $total);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert ke tabel order_items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) 
                                VALUES (?, ?, ?, ?)");
        foreach($cart_items as $item){
            $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
            $stmt->execute();
        }
        $stmt->close();

        // Commit transaksi
        $conn->commit();

        // Kosongkan keranjang
        unset($_SESSION['cart']);

        // Redirect ke halaman sukses
        header("Location: success.php?order_id=$order_id");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger'>Terjadi kesalahan: " . $e->getMessage() . "</div>";
    }
}
?>

<h2>Checkout</h2>

<h4>Detail Pesanan</h4>
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
                <td><?php echo $item['quantity']; ?></td>
                <td>Rp <?php echo number_format($item['subtotal'], 2, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="text-right"><strong>Total:</strong></td>
            <td><strong>Rp <?php echo number_format($total, 2, ',', '.'); ?></strong></td>
        </tr>
    </tbody>
</table>

<form method="POST" action="">
    <button type="submit" class="btn btn-success">Selesaikan Pesanan</button>
</form>

<?php
include 'includes/footer.php';
?>
