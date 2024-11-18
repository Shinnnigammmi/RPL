<?php
// login.php
include 'includes/header.php';
include 'includes/db_connect.php';
session_start();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi
    if (empty($username) || empty($password)) {
        $errors[] = "Username dan Password wajib diisi.";
    }

    if (empty($errors)) {
        // Cek database
        $stmt = $conn->prepare("SELECT id, password, is_admin FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $hashed_password, $is_admin);
            $stmt->fetch();
            if (password_verify($password, $hashed_password)) {
                // Simpan data user di sesi
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = $is_admin;
                header("Location: index.php");
                exit;
            } else {
                $errors[] = "Password salah.";
            }
        } else {
            $errors[] = "Username tidak ditemukan.";
        }
        $stmt->close();
    }
}
?>

<div class="login-form">
    <h2>Masuk</h2>
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
        <button type="submit" class="btn btn-primary">Masuk</button>
    </form>
</div>

<?php
include 'includes/footer.php';
?>
