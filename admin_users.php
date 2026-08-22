<?php
require 'config.php';
requireAdmin();

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Users - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Kelola Users (Admin)</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <a href="admin_user_create.php" class="btn">+ Tambah User</a>

    <table>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Terdaftar</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = mysqli_fetch_assoc($users)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['username']); ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td>
                    <span class="badge <?php echo $row['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                        <?php echo strtoupper($row['role']); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                    <a href="admin_user_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm">Edit</a>
                    <?php if ($row['id'] != $_SESSION['user_id']): ?>
                        <a href="admin_user_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus user ini? Semua laporannya juga akan terhapus.')">Hapus</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
