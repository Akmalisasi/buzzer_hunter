<?php
require 'config.php';
requireAdmin();

$id = $_GET['id'];

// Jangan biarkan admin hapus akunnya sendiri
if ($id != $_SESSION['user_id']) {
    // Hapus dulu file screenshot milik semua laporan user ini
    $reports = mysqli_query($conn, "SELECT id FROM reports WHERE user_id = '$id'");
    while ($r = mysqli_fetch_assoc($reports)) {
        $rid = $r['id'];
        $shots = mysqli_query($conn, "SELECT * FROM report_screenshots WHERE report_id = '$rid'");
        while ($shot = mysqli_fetch_assoc($shots)) {
            $filePath = 'uploads/' . $shot['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }

    // Hapus user (reports & screenshots ikut terhapus lewat ON DELETE CASCADE)
    mysqli_query($conn, "DELETE FROM users WHERE id = '$id'");
}

header("Location: admin_users.php?msg=User berhasil dihapus");
exit;
?>
