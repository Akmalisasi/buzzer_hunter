<?php
require 'config.php';
requireLogin();

$id = $_GET['id'];
$report_id = $_GET['report_id'];

// Pastikan screenshot ini milik report yang boleh diakses user
$reportQuery = mysqli_query($conn, "SELECT * FROM reports WHERE id = '$report_id'");
$report = mysqli_fetch_assoc($reportQuery);

if ($report && ($report['user_id'] == $_SESSION['user_id'] || isAdmin())) {
    $shotQuery = mysqli_query($conn, "SELECT * FROM report_screenshots WHERE id = '$id'");
    $shot = mysqli_fetch_assoc($shotQuery);

    if ($shot) {
        $filePath = 'uploads/' . $shot['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        mysqli_query($conn, "DELETE FROM report_screenshots WHERE id = '$id'");
    }
}

header("Location: report_edit.php?id=$report_id");
exit;
?>
