<?php
// admin/edit_product.php
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

// Mengambil data produk
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
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

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    // Validasi
    if(empty($name) || empty($price)){
        $errors[] = "Nama produk dan harga wajib diisi.";
    }

    // Menangani upload gambar baru
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed)){
            $new_filename = uniqid() . '.' . $file_ext;
            $destination = '../assets/images/' . $new_filename;
            if(move_uploaded_file($file_tmp, $destination)){
                // Hapus gambar lama jika ada
                if($product['image'] && file_exists('../assets/images/' . $product['image'])){
                    unlink('../assets/images/' . $product['image']);
                }
                $image = $new_filename;
            } else {
                $errors[] = "Gagal mengupload gambar.";
            }
        } else {
            $errors[] = "Format gambar tidak diperbolehkan. Hanya JPG, JPEG, PNG, GIF.";
        }
    } else {
        $image = $product['image']; // Gunakan gambar lama
    }

    if(empty($errors)){
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
        $stmt->bind_param("ssdsi", $name, $description, $price, $image, $product_id);
        if($stmt->execute()){
            echo "<div class='alert alert-success'>Produk berhasil diperbarui.</div>";
            echo "<a href='manage_products.php' class='btn btn-primary'>Kembali ke Kelola Produk</a>";
            include '../includes/footer.php';
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat memperbarui produk.";
        }
        $stmt->close();
    }
}
?>

<h2>Edit Produk</h2>

<?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Nama Produk:</label>
        <input type="text" name="name" id="name" 
               class="form-control" 
               value="<?php echo isset($name) ? htmlspecialchars($name) : htmlspecialchars($product['name']); ?>" 
               required>
    </div>
    <div class="form-group">
        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" 
                  class="form-control" rows="5"><?php echo isset($description) ? htmlspecialchars($description) : htmlspecialchars($product['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="price">Harga (Rp):</label>
        <input type="number" name="price" id="price" 
               class="form-control" 
               value="<?php echo isset($price) ? htmlspecialchars($price) : htmlspecialchars($product['price']); ?>" 
               step="0.01" required>
    </div>
    <div class="form-group">
        <label for="image">Gambar Produk:</label>
        <?php if($product['image']): ?>
            <div class="mb-2">
                <img src="../assets/images/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     width="100">
            </div>
        <?php endif; ?>
        <input type="file" name="image" id="image" 
               class="form-control-file" accept="image/*">
        <small class="form-text text-muted">Biarkan kosong jika tidak ingin mengganti gambar.</small>
    </div>
    <button type="submit" class="btn btn-primary">Perbarui Produk</button>
    <a href="manage_products.php" class="btn btn-secondary">Batal</a>
</form>

<?php
include '../includes/footer.php';
?>
