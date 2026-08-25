<div class="navbar">
    <div class="brand">Buzzer Documentation</div>
    <div>
        <a href="dashboard.php">Dashboard</a>
        <?php if (isLoggedIn()): ?>
            <a href="profile.php">Profile</a>
            <a href="change_password.php">Ganti Password</a>
            <?php if (isAdmin()): ?>
                <a href="admin_users.php">Kelola Users</a>
            <?php endif; ?>
            <a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Daftar</a>
        <?php endif; ?>
    </div>
</div>
