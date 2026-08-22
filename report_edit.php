<?php
require 'config.php';
requireLogin();

$id = $_GET['id'];

$query = "SELECT * FROM reports WHERE id = '$id'";
$result = mysqli_query($conn, $query);
$report = mysqli_fetch_assoc($result);

if (!$report) {
    header("Location: dashboard.php");
    exit;
}

// Hanya pemilik laporan atau admin yang boleh edit
if ($report['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = $_POST['judul'];
    $sosmed = $_POST['sosmed'];
    $nama_akun_buzzer = $_POST['nama_akun_buzzer'];
    $tanggal = $_POST['tanggal'];
    $deskripsi = $_POST['deskripsi'];

    $updateQuery = "UPDATE reports SET 
                        judul = '$judul',
                        sosmed = '$sosmed',
                        nama_akun_buzzer = '$nama_akun_buzzer',
                        tanggal = '$tanggal',
                        deskripsi = '$deskripsi'
                    WHERE id = '$id'";

    if (mysqli_query($conn, $updateQuery)) {

        // Tambah screenshot baru jika ada
        if (isset($_FILES['screenshots']) && $_FILES['screenshots']['name'][0] != '') {
            $totalFiles = count($_FILES['screenshots']['name']);
            $allowedExt = ['jpg', 'jpeg', 'png'];

            // Hitung screenshot yang sudah ada supaya tidak lebih dari 5
            $existingCount = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM report_screenshots WHERE report_id = '$id'"));

            for ($i = 0; $i < $totalFiles && ($existingCount + $i) < 5; $i++) {
                if ($_FILES['screenshots']['error'][$i] === 0) {
                    $fileName = $_FILES['screenshots']['name'][$i];
                    $tmpName = $_FILES['screenshots']['tmp_name'][$i];
                    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                    if (in_array($ext, $allowedExt)) {
                        $newFileName = uniqid('shot_') . '.' . $ext;
                        $destination = 'uploads/' . $newFileName;
                        move_uploaded_file($tmpName, $destination);

                        mysqli_query($conn, "INSERT INTO report_screenshots (report_id, file_path) VALUES ('$id', '$newFileName')");
                    }
                }
            }
        }

        header("Location: dashboard.php?msg=Laporan berhasil diperbarui");
        exit;
    } else {
        $error = "Gagal memperbarui laporan: " . mysqli_error($conn);
    }
}

$shots = mysqli_query($conn, "SELECT * FROM report_screenshots WHERE report_id = '$id'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Dokumentasi - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Edit Dokumentasi Buzzer</h2>

    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST" action="report_edit.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
        <div class="form-group">
            <label>Judul</label>
            <input type="text" name="judul" value="<?php echo htmlspecialchars($report['judul']); ?>" required>
        </div>

        <div class="form-group">
            <label>Sosmed</label>
            <select name="sosmed" required>
                <?php
                $sosmedOptions = ['Twitter/X', 'Facebook', 'Instagram', 'TikTok', 'YouTube', 'Lainnya'];
                foreach ($sosmedOptions as $opt) {
                    $selected = ($report['sosmed'] === $opt) ? 'selected' : '';
                    echo "<option value=\"$opt\" $selected>$opt</option>";
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label>Nama Akun Buzzer</label>
            <input type="text" name="nama_akun_buzzer" value="<?php echo htmlspecialchars($report['nama_akun_buzzer']); ?>" required>
        </div>

        <div class="form-group">
            <label>Tanggal</label>
            <input type="date" name="tanggal" value="<?php echo htmlspecialchars($report['tanggal']); ?>" required>
        </div>

        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi"><?php echo htmlspecialchars($report['deskripsi']); ?></textarea>
        </div>

        <div class="form-group">
            <label>Screenshot Saat Ini</label><br>
            <?php while ($shot = mysqli_fetch_assoc($shots)): ?>
                <img src="uploads/<?php echo htmlspecialchars($shot['file_path']); ?>" class="screenshot-thumb">
                <a href="screenshot_delete.php?id=<?php echo $shot['id']; ?>&report_id=<?php echo $id; ?>" 
                   class="btn btn-sm btn-danger" onclick="return confirm('Hapus screenshot ini?')">Hapus</a>
                <br><br>
            <?php endwhile; ?>
        </div>

        <div class="form-group">
            <label>Tambah Screenshot Baru (opsional, total maks 5)</label>
            <input type="file" name="screenshots[]" id="screenshotInput" multiple accept=".jpg,.jpeg,.png">
            <div id="previewContainer" style="margin-top:10px; display:flex; flex-wrap:wrap; gap:8px;"></div>
        </div>

        <button type="submit" class="btn">Update Laporan</button>
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