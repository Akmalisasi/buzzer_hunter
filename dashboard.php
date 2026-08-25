<?php
require 'config.php';
// Dashboard bisa dilihat tanpa login (publik). Untuk menambah dokumentasi wajib login.

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
$limit = 5;
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

// Helper: slug + inisial buat tag warna platform (tanpa pakai logo asli)
function sosmedMeta($s) {
    $map = [
        'Twitter/X'  => ['slug' => 'twitter',   'initial' => 'X'],
        'Facebook'   => ['slug' => 'facebook',  'initial' => 'F'],
        'Instagram'  => ['slug' => 'instagram', 'initial' => 'IG'],
        'TikTok'     => ['slug' => 'tiktok',    'initial' => 'TT'],
        'YouTube'    => ['slug' => 'youtube',   'initial' => 'YT'],
    ];
    return isset($map[$s]) ? $map[$s] : ['slug' => 'other', 'initial' => '•'];
}
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

    <?php if (isLoggedIn()): ?>
        <a href="report_create.php" class="btn">+ Buat Dokumentasi Baru</a>
    <?php else: ?>
        <a href="report_create.php" class="btn">+ Buat Dokumentasi Baru</a>
    <?php endif; ?>

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

    <!-- Kartu laporan -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="report-card-grid">
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <?php $meta = sosmedMeta($row['sosmed']); ?>
                <a href="report_detail.php?id=<?php echo $row['id']; ?>" class="report-card sm-<?php echo $meta['slug']; ?>">
                    <div class="report-card-head">
                        <span class="sm-avatar"><?php echo $meta['initial']; ?></span>
                        <span class="sm-tag"><?php echo htmlspecialchars($row['sosmed']); ?></span>
                    </div>
                    <div class="report-card-title"><?php echo htmlspecialchars($row['judul']); ?></div>
                    <div class="report-card-account">@<?php echo htmlspecialchars($row['nama_akun_buzzer']); ?></div>
                    <div class="report-card-time"><?php echo date('d M Y', strtotime($row['tanggal'])); ?></div>
                </a>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p style="margin-top:20px;">Belum ada laporan.</p>
    <?php endif; ?>

    <div class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <a href="dashboard.php?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&sosmed=<?php echo urlencode($sosmed_filter); ?>"
               class="<?php echo ($i == $page) ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>