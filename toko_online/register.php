<?php
// register.php
include 'includes/header.php';
include 'includes/db_connect.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $errors[] = "Semua field wajib diisi.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak cocok.";
    }

    if (empty($errors)) {
        // Cek apakah username sudah ada
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if($stmt->num_rows > 0){
            $errors[] = "Username sudah digunakan.";
        }
        $stmt->close();

        if(empty($errors)){
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke database
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Registrasi berhasil. <a href='login.php'>Login sekarang</a>.</div>";
                $stmt->close();
                include 'includes/footer.php';
                exit;
            } else {
                $errors[] = "Gagal membuat akun.";
            }
            $stmt->close();
        }
    }
}
?>

<div class="register-form">
    <h2>Daftar</h2>
    <?php if(!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="POST" action="">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" 
                   class="form-control" 
                   value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>" 
                   required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" 
                   class="form-control" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Konfirmasi Password:</label>
            <input type="password" name="confirm_password" id="confirm_password" 
                   class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Daftar</button>
    </form>
</div>

<?php
include 'includes/footer.php';
?>
