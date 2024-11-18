<?php
// success.php
include 'includes/header.php';
include 'includes/db_connect.php';
session_start();

if(!isset($_GET['order_id'])){
    echo "<div class='alert alert-danger'>ID pesanan tidak ditemukan.</div>";
    include 'includes/footer.php';
    exit;
}

$order_id = intval($_GET['order_id']);

// Mengambil detail pesanan
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows !== 1){
    echo "<div class='alert alert-danger'>Pesanan tidak ditemukan.</div>";
    include 'includes/footer.php';
    exit;
}
$order = $result->fetch_assoc();
$stmt->close();

// Mengambil item pesanan
$stmt = $conn->prepare("SELECT products.name, order_items.quantity, order_items.price 
                        FROM order_items 
                        JOIN products ON order_items.product_id = products.id 
                        WHERE order_items.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while($item = $result->fetch_assoc()){
    $items[] = $item;
}
$stmt->close();
?>

<h2>Pesanan Berhasil!</h2>
<p>Terima kasih telah berbelanja. Berikut adalah detail pesanan Anda:</p>

<h4>Order ID: <?php echo $order_id; ?></h4>
<h5>Tanggal: <?php echo $order['created_at']; ?></h5>

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
        <?php foreach($items as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td>Rp <?php echo number_format($item['price'], 2, ',', '.'); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td>Rp <?php echo number_format($item['price'] * $item['quantity'], 2, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="3" class="text-right"><strong>Total:</strong></td>
            <td><strong>Rp <?php echo number_format($order['total_amount'], 2, ',', '.'); ?></strong></td>
        </tr>
    </tbody>
</table>

<a href="index.php" class="btn btn-primary">Kembali ke Beranda</a>

<?php
include 'includes/footer.php';
?>
