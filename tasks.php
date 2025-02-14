<?php
session_start();
include 'database.php';

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$list_id = $_GET['list_id'] ?? 0;

// Ambil daftar tugas berdasarkan list_id
$result = $conn->query("SELECT * FROM tasks WHERE list_id = $list_id ORDER BY created_at DESC");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_task'])) {
    $task_name = trim($_POST['task_name']);

    if (!empty($task_name)) {
        $tanggal = date("Y-m-d"); // Gunakan tanggal saat ini
        $user_id = $_SESSION['user_id']; // Ambil user_id dari session

        $stmt = $conn->prepare("INSERT INTO tasks (list_id, user_id, task_name, status, tanggal) VALUES (?, ?, ?, 'Belum Selesai', ?)");
        $stmt->bind_param("iiss", $list_id, $user_id, $task_name, $tanggal);
        $stmt->execute();

        header("Location: tasks.php?list_id=$list_id");
        exit;
    }
}


// Update status tugas ketika checkbox dicentang
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id']) && isset($_POST['update_status'])) {
    $task_id = $_POST['task_id'];
    $status = $_POST['status'] == "Selesai" ? "Belum Selesai" : "Selesai";

    $conn->query("UPDATE tasks SET status = '$status' WHERE id = $task_id");

    // Periksa apakah semua tugas selesai
    $check = $conn->query("SELECT COUNT(*) AS unfinished FROM tasks WHERE list_id = $list_id AND status = 'Belum Selesai'");
    $row = $check->fetch_assoc();

    if ($row['unfinished'] == 0) {
        $conn->query("UPDATE lists SET status = 'Selesai' WHERE id = $list_id");
    } else {
        $conn->query("UPDATE lists SET status = 'Belum Selesai' WHERE id = $list_id");
    }

    header("Location: tasks.php?list_id=$list_id");
    exit;
}

// Edit tugas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_task'])) {
    $task_id = $_POST['task_id'];
    $new_task_name = trim($_POST['new_task_name']);

    if (!empty($new_task_name)) {
        $stmt = $conn->prepare("UPDATE tasks SET task_name = ? WHERE id = ?");
        $stmt->bind_param("si", $new_task_name, $task_id);
        $stmt->execute();
    }

    header("Location: tasks.php?list_id=$list_id");
    exit;
}

// Hapus tugas
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_task'])) {
    $task_id = $_POST['task_id'];

    // Nonaktifkan sementara foreign key constraint
    $conn->query("SET FOREIGN_KEY_CHECKS = 0");

    // Hapus tugas
    $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();

    // Aktifkan kembali foreign key constraint
    $conn->query("SET FOREIGN_KEY_CHECKS = 1");

    header("Location: tasks.php?list_id=$list_id");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        function confirmDelete() {
            return confirm("Apakah Anda yakin ingin menghapus tugas ini?");
        }
    </script>
</head>
<body>
    <nav>
        <a href="dashboard.php">Dashboard</a>
        <a href="lists.php">Daftar Kegiatan</a>
        <a href="logout.php">Logout</a>
    </nav>
    
    <h1>Daftar Tugas</h1>

    <!-- Form Tambah Tugas -->
    <form method="POST">
        <input type="text" name="task_name" placeholder="Nama Tugas" required>
        <button type="submit" name="add_task">Tambah Tugas</button>
    </form>
    
    <ul>
        <?php while ($row = $result->fetch_assoc()): ?>
            <li>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="status" value="<?= $row['status'] ?>">
                    <input type="checkbox" name="update_status" onchange="this.form.submit()" <?= $row['status'] == 'Selesai' ? 'checked' : '' ?>>
                    <?= htmlspecialchars($row['task_name']) ?> - <strong><?= $row['status'] ?></strong>
                </form>
                
                <!-- Form Edit -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                    <input type="text" name="new_task_name" value="<?= htmlspecialchars($row['task_name']) ?>" required>
                    <button type="submit" name="edit_task">Edit</button>
                </form>

                <!-- Form Hapus -->
                <form method="POST" style="display:inline;" onsubmit="return confirmDelete();">
                    <input type="hidden" name="task_id" value="<?= $row['id'] ?>">
                    <button type="submit" name="delete_task">Hapus</button>
                </form>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>
