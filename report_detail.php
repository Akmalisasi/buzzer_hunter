<?php
require 'config.php';
// Detail laporan bisa dilihat tanpa login, sama seperti dashboard.

$id = isset($_GET['id']) ? $_GET['id'] : 0;

$query = "SELECT r.*, u.username 
          FROM reports r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.id = '$id'";
$result = mysqli_query($conn, $query);
$report = $result ? mysqli_fetch_assoc($result) : null;

$shots = [];
if ($report) {
    $shotQuery = mysqli_query($conn, "SELECT * FROM report_screenshots WHERE report_id = '$id'");
    while ($shot = mysqli_fetch_assoc($shotQuery)) {
        $shots[] = $shot;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Laporan - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <?php if (!$report): ?>
        <h2>Laporan Tidak Ditemukan</h2>
        <p>Laporan dengan ID tersebut tidak ada.</p>
        <a href="dashboard.php" class="btn btn-secondary" style="margin-top:15px;">Kembali ke Dashboard</a>
    <?php else: ?>
        <h2><?php echo htmlspecialchars($report['judul']); ?></h2>

        <span class="badge badge-user"><?php echo htmlspecialchars($report['sosmed']); ?></span>

        <table style="margin-top:20px;">
            <tr><th style="width:180px;">Akun Buzzer</th><td><?php echo htmlspecialchars($report['nama_akun_buzzer']); ?></td></tr>
            <tr><th>Tanggal</th><td><?php echo date('d M Y', strtotime($report['tanggal'])); ?></td></tr>
            <tr><th>Dilaporkan Oleh</th><td><?php echo htmlspecialchars($report['username']); ?></td></tr>
            <tr><th>Deskripsi</th><td><?php echo nl2br(htmlspecialchars($report['deskripsi'])); ?></td></tr>
        </table>

        <?php if (count($shots) > 0): ?>
            <h2 style="margin-top:25px;">Screenshot</h2>
            <div>
                <?php foreach ($shots as $shot): ?>
                    <img src="uploads/<?php echo htmlspecialchars($shot['file_path']); ?>" class="screenshot-thumb">
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div style="margin-top:25px;">
            <a href="dashboard.php" class="btn btn-secondary">Kembali</a>
            <?php if (isLoggedIn() && ($report['user_id'] == $_SESSION['user_id'] || isAdmin())): ?>
                <a href="report_edit.php?id=<?php echo $report['id']; ?>" class="btn">Edit</a>
                <a href="report_delete.php?id=<?php echo $report['id']; ?>" class="btn btn-danger" onclick="return confirm('Yakin hapus laporan ini?')">Hapus</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>