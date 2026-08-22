<?php
require 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $query = "UPDATE users SET username = '$username', email = '$email' WHERE id = '$user_id'";

    if (!empty($_POST['password'])) {
        $password = $_POST['password'];
        $query = "UPDATE users SET username = '$username', email = '$email', password = '$password' WHERE id = '$user_id'";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['username'] = $username;
        $success = "Profile berhasil diperbarui.";
    } else {
        $error = "Gagal memperbarui profile: " . mysqli_error($conn);
    }
}

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($userQuery);

// Laporan milik user ini
$myReports = mysqli_query($conn, "SELECT * FROM reports WHERE user_id = '$user_id' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profile - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Profile Saya</h2>
    <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
        <?php echo strtoupper($user['role']); ?>
    </span>

    <?php if ($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
    <?php if ($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

    <form method="POST" action="profile.php" style="margin-top:20px;">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>
        <div class="form-group">
            <label>Password Baru (kosongkan jika tidak ingin ganti)</label>
            <input type="password" name="password">
        </div>
        <button type="submit" class="btn">Update Profile</button>
    </form>
</div>

<div class="container">
    <h2>Laporan Saya</h2>
    <table>
        <tr>
            <th>Judul</th>
            <th>Sosmed</th>
            <th>Akun Buzzer</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
        <?php if (mysqli_num_rows($myReports) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($myReports)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['sosmed']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_akun_buzzer']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                    <td>
                        <a href="report_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm">Edit</a>
                        <a href="report_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus laporan ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">Kamu belum membuat laporan.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>
