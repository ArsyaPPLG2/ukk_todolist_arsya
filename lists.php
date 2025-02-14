<?php
session_start();
include 'database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil semua daftar kegiatan milik user
$result = $conn->query("SELECT * FROM lists WHERE user_id = $user_id ORDER BY created_at DESC");

// Tambah List
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_list'])) {
    $name = trim($_POST['name']);
    if (!empty($name)) {
        // Masukkan list baru dengan status default 'Belum Selesai'
        $stmt = $conn->prepare("INSERT INTO lists (user_id, name, status) VALUES (?, ?, 'Belum Selesai')");
        $stmt->bind_param("is", $user_id, $name);
        if (!$stmt->execute()) {
            die("Error: " . $stmt->error);
        }
        $stmt->close();
        header("Location: lists.php");
        exit;
    }
}

// Edit List
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_list'])) {
    $list_id = $_POST['list_id'];
    $name = trim($_POST['name']);
    if (!empty($name)) {
        $stmt = $conn->prepare("UPDATE lists SET name = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("sii", $name, $list_id, $user_id);
        $stmt->execute();
        $stmt->close();
        header("Location: lists.php");
        exit;
    }
}

// Hapus List
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM lists WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $delete_id, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: lists.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Kegiatan</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="profile.php">Profile</a>
    <a href="logout.php">Logout</a>
  </nav>

  <h1>Daftar Kegiatan</h1>

  <!-- Form Tambah List -->
  <form method="POST">
    <input type="text" name="name" placeholder="Nama List" required>
    <button type="submit" name="add_list">Tambah List</button>
  </form>

  <ul>
  <?php while ($row = $result->fetch_assoc()): 
    // Gunakan trim() dan strcasecmp() untuk membandingkan status secara case-insensitive
    $status_clean = trim($row['status']);
    $status_color = (strcasecmp($status_clean, "Selesai") === 0) ? "green" : "red";
  ?>
    <li>
      <span style="color: <?= $status_color ?>;">
        <?= htmlspecialchars($row['name']) ?> - <?= htmlspecialchars($row['status']) ?>
      </span>
      <form method="POST" style="display:inline;">
        <input type="hidden" name="list_id" value="<?= $row['id'] ?>">
        <input type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>" required>
        <button type="submit" name="edit_list">Edit</button>
      </form>
      <a href="tasks.php?list_id=<?= $row['id'] ?>">Lihat Tugas</a>
      <a href="lists.php?delete_id=<?= $row['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus daftar ini?');">Hapus</a>
    </li>
  <?php endwhile; ?>
  </ul>
</body>
</html>
