<?php
session_start();
include 'database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pengguna
$stmt = $conn->prepare("SELECT username, email FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Update profil
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);

    if (!empty($name) && !empty($email)) {
        $update_stmt = $conn->prepare("UPDATE users SET username = ?, email = ? WHERE user_id = ?");
        $update_stmt->bind_param("ssi", $name, $email, $user_id);
        $update_stmt->execute();

        // Perbarui data di session
        $_SESSION['user_name'] = $username;

        header("Location: profile.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="lists.php">Daftar Kegiatan</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <h1>Profil Pengguna</h1>
    
    <form method="POST">
        <label for="name">Nama:</label>
        <input type="text" name="name" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>

        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>

        <button type="submit" name="update_profile">Perbarui Profil</button>
    </form>

</body>
</html>
