<?php
session_start();

$host = "localhost";
$dbuser = "abu";
$dbpass = "1";
$dbname = "buzzer_documentation";

$conn = mysqli_connect($host, $dbuser, $dbpass, $dbname);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}

// Helper: cek sudah login atau belum
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Helper: cek role admin
function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Redirect kalau belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Redirect kalau bukan admin
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: dashboard.php");
        exit;
    }
}
?>
