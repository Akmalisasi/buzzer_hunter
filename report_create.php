<?php
require 'config.php';
requireLogin();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $sosmed = $_POST['sosmed'];
    $nama_akun_buzzer = $_POST['nama_akun_buzzer'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];
    $user_id = $_SESSION['user_id'];

    $query = "INSERT INTO reports (user_id, judul, sosmed, nama_akun_buzzer, tanggal, deskripsi)
              VALUES ('$user_id', '$judul', '$sosmed', '$nama_akun_buzzer', '$tanggal', '$deskripsi')";

    if (mysqli_query($conn, $query)) {
        $report_id = mysqli_insert_id($conn);

        // ---- Screenshot: upload file dan/atau import via URL (gabungan maks 5) ----
        $maxFiles = 5;
        $uploadedCount = 0;

        // a. Upload dari komputer
        if (isset($_FILES['screenshots'])) {
            $totalFiles = count($_FILES['screenshots']['name']);
            $allowedExt = ['jpg', 'jpeg', 'png'];

            for ($i = 0; $i < $totalFiles && $uploadedCount < $maxFiles; $i++) {
                if ($_FILES['screenshots']['error'][$i] === 0) {
                    $fileName = $_FILES['screenshots']['name'][$i];
                    $tmpName = $_FILES['screenshots']['tmp_name'][$i];
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowedExt)) {
                        $newFileName = uniqid('shot_') . '.' . $ext;
                        $destination = 'uploads/' . $newFileName;
                        move_uploaded_file($tmpName, $destination);

                        mysqli_query($conn, "INSERT INTO report_screenshots (report_id, file_path) VALUES ('$report_id', '$newFileName')");
                        $uploadedCount++;
                    }
                }
            }
        }

        // b. Import via URL (satu URL per baris)
        if (!empty($_POST['screenshot_urls'])) {
            $urls = array_filter(array_map('trim', explode("\n", $_POST['screenshot_urls'])));

            foreach ($urls as $url) {
                if ($uploadedCount >= $maxFiles) break;

                $newFileName = downloadFromUrl($url, 'shot_');

                if ($newFileName !== false) {
                    mysqli_query($conn, "INSERT INTO report_screenshots (report_id, file_path) VALUES ('$report_id', '$newFileName')");
                    $uploadedCount++;
                }
            }
        }

        header("Location: dashboard.php?msg=Laporan berhasil dibuat");
        exit;
    } else {
        $error = "Gagal menyimpan laporan: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Dokumentasi - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Buat Dokumentasi Buzzer</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="report_create.php" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="judul" required>
        </div>

        <div class="form-group">
            <label>Sosmed</label>
            <select name="sosmed" required>
                <option value="">-- Pilih Sosmed --</option>
                <option value="Twitter/X">Twitter/X</option>
                <option value="Facebook">Facebook</option>
                <option value="Instagram">Instagram</option>
                <option value="TikTok">TikTok</option>
                <option value="YouTube">YouTube</option>
                <option value="Lainnya">Lainnya</option>
            </select>
        </div>

        <div class="form-group">
            <label>Nama Akun Buzzer</label>
            <input type="text" name="nama_akun_buzzer" required>
        </div>

        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" required>
        </div>

        <div class="form-group">
            <label>Deskripsi (opsional)</label>
            <textarea name="deskripsi"></textarea>
        </div>

        <div class="form-group">
            <label>Screenshot (bisa pilih lebih dari 1, maks 5, format jpg/jpeg/png)</label>
            <input type="file" name="screenshots[]" id="screenshotInput" multiple accept=".jpg,.jpeg,.png">
            <div id="previewContainer" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:8px;"></div>
        </div>

        <div class="form-group">
            <label>atau Import Screenshot via URL (satu URL per baris, maks 5 gabungan, format jpg/jpeg/png/webp, boleh link gambar tanpa ekstensi)</label>
            <textarea name="screenshot_urls" placeholder="https://contoh.com/gambar1.jpg&#10;https://encrypted-tbn0.gstatic.com/images?q=tbn:..."></textarea>
        </div>

        <button type="submit" class="btn">Simpan Laporan</button>
        <a href="dashboard.php" class="btn btn-secondary">Batal</a>
    </form>
</div>

<script>
document.getElementById('screenshotInput').addEventListener('change', function (e) {
    const previewContainer = document.getElementById('previewContainer');
    previewContainer.innerHTML = '';

    const files = Array.from(e.target.files).slice(0, 5); // maks 5 preview

    files.forEach(function (file) {
        if (!file.type.startsWith('image/')) return;

        const reader = new FileReader();
        reader.onload = function (event) {
            const img = document.createElement('img');
            img.src = event.target.result;
            img.className = 'screenshot-thumb';
            previewContainer.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
</body>
</html>