<?php
require 'config.php';
requireAdmin();

$id = $_GET['id'];
$error = "";

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = '$id'");
$user = mysqli_fetch_assoc($userQuery);

if (!$user) {
    header("Location: admin_users.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $query = "UPDATE users SET username = '$username', email = '$email', role = '$role' WHERE id = '$id'";

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $query = "UPDATE users SET username = '$username', email = '$email', role = '$role', password = '$password' WHERE id = '$id'";
    }

    if (mysqli_query($conn, $query)) {
        header("Location: admin_users.php?msg=User berhasil diperbarui");
        exit;
    } else {
        $error = "Gagal memperbarui user: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit User - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Edit User</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="admin_user_edit.php?id=<?php echo $id; ?>">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Password Baru (kosongkan jika tidak diubah)</label>
            <input type="password" name="password">
        </div>
        <div class="form-group">
            <label>Role</label>
            <select name="role" required>
                <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="btn">Update</button>
        <a href="admin_users.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
</body>
</html>
