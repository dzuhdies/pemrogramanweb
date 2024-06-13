<?php
session_start();
include 'koneksi.php';

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

// Mengambil daftar pengguna dari database
$stmt = $conn->prepare("SELECT id, username, email FROM users");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
    <link rel="stylesheet" href="manage.css">
</head>
<body>
    <header>
        <div class="header-left">
            <h1>Admin Dashboard</h1>
        </div>
        <div class="header-right">
            <a href="logout.php" class="logout-link">Logout</a>
        </div>
    </header>
    <main>
        <section class="user-section">
            <h2>Manage Users</h2>
            <?php
            if ($result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Username</th><th>Email</th><th>Action</th></tr>';
                while ($row = $result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['id']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['username']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['email']) . '</td>';
                    echo '<td><form action="delete_user.php" method="post" onsubmit="return confirm(\'Are you sure you want to delete this user?\');">
                            <input type="hidden" name="user_id" value="' . htmlspecialchars($row['id']) . '">
                            <input type="submit" value="Delete">
                          </form></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo 'No users available.';
            }

            $stmt->close();
            $conn->close();
            ?>
        </section>
    </main>
</body>
</html>
