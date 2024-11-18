<?php
// admin/add_product.php
include '../includes/header.php';
include '../includes/db_connect.php';
session_start();

// Cek apakah pengguna adalah admin
if(!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1){
    echo "<div class='alert alert-danger'>Anda tidak memiliki akses ke halaman ini.</div>";
    include '../includes/footer.php';
    exit;
}

$errors = [];

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);

    // Validasi
    if(empty($name) || empty($price)){
        $errors[] = "Nama produk dan harga wajib diisi.";
    }

    // Menangani upload gambar
    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0){
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['image']['name'];
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($file_ext, $allowed)){
            $new_filename = uniqid() . '.' . $file_ext;
            $destination = '../assets/images/' . $new_filename;
            if(move_uploaded_file($file_tmp, $destination)){
                $image = $new_filename;
            } else {
                $errors[] = "Gagal mengupload gambar.";
            }
        } else {
            $errors[] = "Format gambar tidak diperbolehkan. Hanya JPG, JPEG, PNG, GIF.";
        }
    } else {
        $image = NULL; // Tidak wajib upload gambar
    }

    if(empty($errors)){
        $stmt = $conn->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $name, $description, $price, $image);
        if($stmt->execute()){
            echo "<div class='alert alert-success'>Produk berhasil ditambahkan.</div>";
            echo "<a href='manage_products.php' class='btn btn-primary'>Kembali ke Kelola Produk</a>";
            include '../includes/footer.php';
            exit;
        } else {
            $errors[] = "Terjadi kesalahan saat menambahkan produk.";
        }
        $stmt->close();
    }
}
?>

<h2>Tambah Produk Baru</h2>

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
               value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" 
               required>
    </div>
    <div class="form-group">
        <label for="description">Deskripsi:</label>
        <textarea name="description" id="description" 
                  class="form-control" rows="5"><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
    </div>
    <div class="form-group">
        <label for="price">Harga (Rp):</label>
        <input type="number" name="price" id="price" 
               class="form-control" 
               value="<?php echo isset($price) ? htmlspecialchars($price) : ''; ?>" 
               step="0.01" required>
    </div>
    <div class="form-group">
        <label for="image">Gambar Produk:</label>
        <input type="file" name="image" id="image" 
               class="form-control-file" accept="image/*">
    </div>
    <button type="submit" class="btn btn-success">Tambah Produk</button>
    <a href="manage_products.php" class="btn btn-secondary">Batal</a>
</form>

<?php
include '../includes/footer.php';
?>
