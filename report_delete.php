<?php
require 'config.php';
requireLogin();

$id = $_GET['id'];

$query = "SELECT * FROM reports WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$report = mysqli_fetch_assoc($result);

if ($report && ($report['user_id'] == $_SESSION['user_id'] || isAdmin())) {
    // Hapus file screenshot fisik dulu
    $shots = mysqli_query($conn, "SELECT * FROM report_screenshots WHERE report_id = '$id'");
    while ($shot = mysqli_fetch_assoc($shots)) {
        $filePath = 'uploads/' . $shot['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Hapus laporan (screenshot di DB otomatis terhapus lewat ON DELETE CASCADE)
    mysqli_query($conn, "DELETE FROM reports WHERE id = '$id'");
}

header("Location: dashboard.php?msg=Laporan berhasil dihapus");
exit;
?>
