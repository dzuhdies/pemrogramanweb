<?php
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Ambil semua pengguna dari database
$user_stmt = $conn->prepare("SELECT id, username, email FROM users");
$user_stmt->execute();
$user_result = $user_stmt->get_result();

// Ambil semua postingan dari database
$post_stmt = $conn->prepare("SELECT idartikel, judul, penulis, tanggal FROM artikel");
$post_stmt->execute();
$post_result = $post_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="manepos.css">
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
        <aside class="sidebar-container">
            <div class="sidebar">
                <ul>
                    <li><a href="admin.php">Admin</a></li>
                    <li><a href="">Semua Postingan</a></li>
                    <li><a href="managekategori.php">Manage Kategori</a></li>
                    <li><a href="logout.php">Keluar Akun</a></li>
                </ul>
            </div>
        </aside>
        <section class="post-section">
            <h2>Manage Posts</h2>
            <?php
            if ($post_result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Title</th><th>Author</th><th>Date</th><th>Actions</th></tr>';
                while ($row = $post_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['idartikel']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['judul']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['penulis']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['tanggal']) . '</td>';
                    echo '<td>
                            <form action="deleteposadmin.php" method="post" onsubmit="return confirm(\'Are you sure you want to delete this post?\');" style="display:inline;">
                                <input type="hidden" name="post_id" value="' . htmlspecialchars($row['idartikel']) . '">
                                <input type="submit" value="Delete">
                            </form>
                          </td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo 'No posts available.';
            }

            $post_stmt->close();
            $conn->close();
            ?>
        </section>
    </main>
</body>
</html>
