<?php
require 'config.php';
requireLogin();

$user_id = $_SESSION['user_id'];
$error = "";
$success = "";

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($userQuery);

// Update profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Form avatar: upload file atau import via URL ----
    if (isset($_POST['form']) && $_POST['form'] === 'avatar') {
        if (isset($_FILES['avatar_file']) && $_FILES['avatar_file']['error'] === 0) {
            // Upload avatar dari komputer
            $ext = strtolower(pathinfo($_FILES['avatar_file']['name'], PATHINFO_EXTENSION));
            $allowedExt = ['jpg', 'jpeg', 'png'];

            if (in_array($ext, $allowedExt)) {
                $newFileName = uniqid('avatar_') . '.' . $ext;

                if (move_uploaded_file($_FILES['avatar_file']['tmp_name'], 'uploads/' . $newFileName)) {
                    deleteUpload($user['avatar']);
                    mysqli_query($conn, "UPDATE users SET avatar = '$newFileName' WHERE id = '$user_id'");
                    $success = "Avatar berhasil diupload.";
                } else {
                    $error = "Gagal menyimpan file avatar.";
                }
            } else {
                $error = "Format avatar harus jpg/jpeg/png.";
            }
        } elseif (!empty($_POST['avatar_url'])) {
            // Import avatar via URL
            $newFileName = downloadFromUrl($_POST['avatar_url'], 'avatar_');

            if ($newFileName !== false) {
                deleteUpload($user['avatar']);
                mysqli_query($conn, "UPDATE users SET avatar = '$newFileName' WHERE id = '$user_id'");
                $success = "Avatar berhasil diimport dari URL.";
            } else {
                $error = "Gagal import avatar: URL tidak valid, bukan gambar jpg/jpeg/png, atau ukuran lebih dari 2MB.";
            }
        } else {
            $error = "Pilih file atau isi URL avatar terlebih dahulu.";
        }

        // Refresh data user setelah update avatar
        $userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
        $user = mysqli_fetch_assoc($userQuery);
    } else {
        // ---- Form update profile ----
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
}

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

    <!-- Avatar: upload atau import via URL -->
    <h3 style="margin-top:20px;">Avatar</h3>
    <p>
        <?php if (!empty($user['avatar'])): ?>
            <img src="uploads/<?php echo htmlspecialchars($user['avatar']); ?>" class="screenshot-thumb">
        <?php else: ?>
            <em>Belum ada avatar.</em>
        <?php endif; ?>
    </p>
    <form method="POST" action="profile.php" enctype="multipart/form-data">
        <input type="hidden" name="form" value="avatar">
        <div class="form-group">
            <label>Upload Avatar dari Komputer (jpg/jpeg/png)</label>
            <input type="file" name="avatar_file" accept=".jpg,.jpeg,.png">
        </div>
        <div class="form-group">
            <label>atau Import Avatar via URL</label>
            <input type="url" name="avatar_url" placeholder="https://contoh.com/foto.jpg" style="width:100%;">
        </div>
        <button type="submit" class="btn">Simpan Avatar</button>
    </form>

    <form method="POST" action="profile.php" style="margin-top:20px;">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
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
