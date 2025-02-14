<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
$success = isset($_SESSION['success']) ? $_SESSION['success'] : '';
unset($_SESSION['error'], $_SESSION['success']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="register-container">
    <h2>REGISTRASI</h2>
    <br>
    <br>
    
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>
    
    <form action="proses_register.php" method="post">
        <input type="text" name="username" placeholder="Username" autocomplete="off" required>
        <input type="email" name="email" placeholder="Example@gmail.com" autocomplete="off" required>
        <input type="password" name="password" placeholder="Password" autocomplete="new-password" required>
        <button type="submit" value="Register">Register</button>
    </form>
    <div class="login-link">
        Already have an account? <a href="login.php">Login here</a>
    </div>
</div>
</body>
</html>
