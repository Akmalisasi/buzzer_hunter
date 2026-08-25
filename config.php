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

// Helper: download/import file gambar dari URL, simpan ke folder uploads/
// Return: nama file baru jika sukses, false jika gagal
function downloadFromUrl($url, $prefix = 'import_') {
    // Validasi: hanya izinkan protokol http/https
    $scheme = strtolower(parse_url($url, PHP_URL_SCHEME));
    if (!in_array($scheme, ['http', 'https'])) {
        return false;
    }

    $maxSize = 2 * 1024 * 1024; // maksimal 2 MB

    // Download dengan timeout supaya tidak menggantung
    $context = stream_context_create([
        'http' => [
            'timeout'       => 15,
            'max_redirects' => 5,
            'user_agent'    => 'Mozilla/5.0 (compatible; BuzzerDocumentation/1.0)',
            'header'        => "Accept: image/jpeg, image/png, image/webp, image/*;q=0.8\r\n",
        ]
    ]);
    $data = @file_get_contents($url, false, $context);

    if ($data === false || strlen($data) === 0 || strlen($data) > $maxSize) {
        return false;
    }

    // Deteksi tipe gambar dari ISI file (magic bytes), bukan dari ekstensi URL.
    // Jadi link tanpa ekstensi seperti https://encrypted-tbn0.gstatic.com/images?q=tbn:...
    // tetap bisa diimport selama isinya benar-benar gambar.
    if (substr($data, 0, 3) === "\xFF\xD8\xFF") {
        $ext = 'jpg';   // JPEG
    } elseif (substr($data, 0, 8) === "\x89PNG\r\n\x1a\n") {
        $ext = 'png';   // PNG
    } elseif (strlen($data) > 12 && substr($data, 0, 4) === "RIFF" && substr($data, 8, 4) === "WEBP") {
        $ext = 'webp';  // WebP (sering dipakai thumbnail Google)
    } else {
        return false;   // bukan gambar yang dikenali
    }

    // Simpan dengan nama acak, ekstensi mengikuti isi asli file
    $newFileName = uniqid($prefix) . '.' . $ext;

    if (file_put_contents('uploads/' . $newFileName, $data) === false) {
        return false;
    }

    return $newFileName;
}

// Helper: hapus file lama di uploads/ (untuk ganti avatar/screenshot)
function deleteUpload($fileName) {
    if ($fileName && file_exists('uploads/' . $fileName)) {
        unlink('uploads/' . $fileName);
    }
}
?>
