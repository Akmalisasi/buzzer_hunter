<?php
require 'config.php';
requireLogin();

// ---- Filter & Search ----
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sosmed_filter = isset($_GET['sosmed']) ? $_GET['sosmed'] : '';

$where = [];
if ($search !== '') {
    $where[] = "(r.judul LIKE '%$search%' OR r.nama_akun_buzzer LIKE '%$search%')";
}
if ($sosmed_filter !== '') {
    $where[] = "r.sosmed = '$sosmed_filter'";
}
$whereSql = count($where) > 0 ? "WHERE " . implode(" AND ", $where) : "";

// ---- Pagination ----
$limit = 3;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$countQuery = "SELECT COUNT(*) as total FROM reports r $whereSql";
$countResult = mysqli_query($conn, $countQuery);
$totalRows = mysqli_fetch_assoc($countResult)['total'];
$totalPages = ceil($totalRows / $limit);

$query = "SELECT r.*, u.username 
          FROM reports r 
          JOIN users u ON r.user_id = u.id 
          $whereSql
          ORDER BY r.created_at DESC
          LIMIT $limit OFFSET $offset";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Buzzer Documentation</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container">
    <h2>Dashboard - Semua Laporan Dokumentasi Buzzer</h2>

    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <a href="report_create.php" class="btn">+ Buat Dokumentasi Baru</a>

    <form method="GET" action="dashboard.php" class="filter-bar" style="margin-top:20px;">
        <input type="text" name="search" placeholder="Cari judul / nama akun buzzer" value="<?php echo htmlspecialchars($search); ?>">
        <select name="sosmed">
            <option value="">-- Semua Sosmed --</option>
            <?php
            $sosmedOptions = ['Twitter/X', 'Facebook', 'Instagram', 'TikTok', 'YouTube', 'Lainnya'];
            foreach ($sosmedOptions as $opt) {
                $selected = ($sosmed_filter === $opt) ? 'selected' : '';
                echo "<option value=\"$opt\" $selected>$opt</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-secondary">Filter</button>
        <a href="dashboard.php" class="btn btn-secondary">Reset</a>
    </form>

    <table>
        <tr>
            <th>Judul</th>
            <th>Sosmed</th>
            <th>Akun Buzzer</th>
            <th>Tanggal</th>
            <th>Dilaporkan Oleh</th>
            <th>Screenshot</th>
            <th>Aksi</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['judul']); ?></td>
                    <td><?php echo htmlspecialchars($row['sosmed']); ?></td>
                    <td><?php echo htmlspecialchars($row['nama_akun_buzzer']); ?></td>
                    <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td>
                        <?php
                        $shotQuery = mysqli_query($conn, "SELECT * FROM report_screenshots WHERE report_id = " . $row['id'] . " LIMIT 3");
                        while ($shot = mysqli_fetch_assoc($shotQuery)) {
                            $imgPath = 'uploads/' . htmlspecialchars($shot['file_path']);
                            echo '<img src="' . $imgPath . '" class="screenshot-thumb" onclick="openImageModal(\'' . $imgPath . '\')" style="cursor:pointer;">';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if ($row['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                            <a href="report_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm">Edit</a>
                            <a href="report_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus laporan ini?')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7">Belum ada laporan.</td></tr>
        <?php endif; ?>
    </table>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="dashboard.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sosmed=<?php echo urlencode($sosmed_filter); ?>"
               class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>

<!-- Modal untuk lihat gambar full -->
<div id="imageModal" onclick="closeImageModal()" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.85); z-index:999; cursor:zoom-out; justify-content:center; align-items:center;">
    <span onclick="closeImageModal()" style="position:absolute; top:20px; right:35px; color:#fff; font-size:35px; font-weight:bold; cursor:pointer;">&times;</span>
    <img id="modalImage" src="" style="max-width:90%; max-height:90%; border-radius:6px; box-shadow:0 0 20px rgba(0,0,0,0.5);">
</div>

<script>
function openImageModal(src) {
    document.getElementById('modalImage').src = src;
    document.getElementById('imageModal').style.display = 'flex';
}
function closeImageModal() {
    document.getElementById('imageModal').style.display = 'none';
    document.getElementById('modalImage').src = '';
}
</script>
</body>
</html>