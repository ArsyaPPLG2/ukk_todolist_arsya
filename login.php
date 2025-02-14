<?php
session_start();

// Hapus session lama agar login baru benar-benar fresh
if (isset($_SESSION['user_id'])) {
    session_unset();
    session_destroy();
}

include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        $stmt = $conn->prepare("SELECT user_id, password FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($id, $hashed_password);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                header("Location: dashboard.php");
                exit;
            } else {
                $error = "Password salah.";
            }
        } else {
            $error = "Username tidak ditemukan.";
        }
    } else {
        $error = "Harap isi semua bidang.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <div class="login-box">
            <img src="img/buled.png" alt="Logo" class="logo"> <!-- Tambahkan logo -->
            <h2>Login</h2>

            <?php if (isset($error)): ?>
                <p class="error-message"> <?= htmlspecialchars($error) ?> </p>
            <?php endif; ?>

            <form method="POST">
                <label>Username:</label>
                <input type="text" name="username" placeholder="Masukkan Username" autocomplete="off" required>

                <label>Password:</label>
                <input type="password" name="password" placeholder="Masukkan Password" autocomplete="new-password" required>

                <button type="submit">Login</button>
            </form>
            
            <div class="register-link">
                Belum punya akun? <a href="register.php">Daftar di sini</a>
            </div>
        </div>
    </div>
</body>
</html>
