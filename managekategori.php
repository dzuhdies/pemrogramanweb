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

// Ambil semua kategori dari database
$category_stmt = $conn->prepare("SELECT idkategori, kategori FROM kategori");
$category_stmt->execute();
$category_result = $category_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="manakat.css">
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
                    <li><a href="managepostingan.php">Semua Postingan</a></li>
                    <li><a href="">Manage Kategori</a></li>
                    <li><a href="logout.php">Keluar Akun</a></li>
                </ul>
            </div>
        </aside>
        <section class="category-section">
            <h2>Manage Categories</h2>
            <form action="addkategori.php" method="post">
                <div class="form-group">
                    <label for="category_id">Category ID:</label>
                    <input type="text" id="category_id" name="category_id" required>
                </div>
                <div class="form-group">
                    <label for="category_name">Category Name:</label>
                    <input type="text" id="category_name" name="category_name" required>
                </div>
                <input type="submit" value="Add">
            </form>
            <?php
            if ($category_result->num_rows > 0) {
                echo '<table>';
                echo '<tr><th>ID</th><th>Category</th><th>Action</th></tr>';
                while ($row = $category_result->fetch_assoc()) {
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($row['idkategori']) . '</td>';
                    echo '<td>' . htmlspecialchars($row['kategori']) . '</td>';
                    echo '<td><form action="deletekategori.php" method="post" onsubmit="return confirm(\'Are you sure you want to delete this category?\');">
                            <input type="hidden" name="category_id" value="' . htmlspecialchars($row['idkategori']) . '">
                            <input type="submit" value="Delete">
                          </form></td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo 'No categories available.';
            }

            $category_stmt->close();
            $conn->close();
            ?>
        </section>
    </main>
</body>
</html>
