<?php
require 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Ambil password saat ini dari database
    $result = mysqli_query($conn, "SELECT password FROM users WHERE id = '$user_id'");
    $user = mysqli_fetch_assoc($result);

    if ($user['password'] !== $old_password) {
        $error = "Password lama tidak sesuai.";
    } elseif ($new_password !== $confirm_password) {
        $error = "Konfirmasi password baru tidak cocok.";
    } elseif (strlen($new_password) < 4) {
        $error = "Password baru minimal 4 karakter.";
    } else {
        $query = "UPDATE users SET password = '$new_password' WHERE id = '$user_id'";
        if (mysqli_query($conn, $query)) {
            $success = "Password berhasil diubah.";
        } else {
            $error = "Gagal mengubah password: " . mysqli_error($conn);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Ganti Password</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>

    <form method="POST" action="change_password.php">
        <div class="form-group">
            <label>Password Lama</label>
            <input type="password" name="old_password" required>
        </div>
        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="new_password" required>
        </div>
        <div class="form-group">
            <label>Konfirmasi Password Baru</label>
            <input type="password" name="confirm_password" required>
        </div>
        <button type="submit" class="btn">Ubah Password</button>
        <a href="profile.php" class="btn btn-secondary">Kembali ke Profile</a>
    </form>
</div>
</body>
</html>