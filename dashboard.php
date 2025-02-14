<?php
session_start();
include 'database.php';

// Cek apakah user sudah login
$is_logged_in = isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/style.css">

</head>
<body>
    <nav>
        <?php if ($is_logged_in): ?>
            <span>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
    
    <h1>Dashboard</h1>
    
    <?php if ($is_logged_in): ?>
        <a href="lists.php"><button>Lihat Daftar</button></a>
    <?php else: ?>
        <p>Silakan login untuk mengakses daftar kegiatan.</p>
    <?php endif; ?>
</body>
</html>
