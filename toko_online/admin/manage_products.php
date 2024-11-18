<?php
// admin/manage_products.php
include '../includes/header.php';
include '../includes/db_connect.php';
session_start();

// Cek apakah pengguna adalah admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    echo "<div class='alert alert-danger'>Anda tidak memiliki akses ke halaman ini.</div>";
    include '../includes/footer.php';
    exit;
}

// Mengambil semua produk
$sql = "SELECT * FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<h2>Kelola Produk</h2>
<a href="add_product.php" class="btn btn-success mb-3">Tambah Produk Baru</a>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama Produk</th>
            <th>Harga</th>
            <th>Gambar</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if($result->num_rows > 0): ?>
            <?php while($product = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $product['id']; ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td>Rp <?php echo number_format($product['price'], 2, ',', '.'); ?></td>
                    <td>
                        <?php if($product['image']): ?>
                            <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                 width="50">
                        <?php else: ?>
                            Tidak Ada Gambar
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" 
                           class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" 
                           class="btn btn-danger btn-sm" 
                           onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
                           Hapus
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="5">Tidak ada produk.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php
include '../includes/footer.php';
?>
