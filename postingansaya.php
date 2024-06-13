<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan ID pengguna yang sedang login
$userid = $_SESSION['userid'];

// Query untuk mendapatkan postingan pengguna yang sedang login berdasarkan ID pengguna
$stmt = $conn->prepare("SELECT a.idartikel, a.judul, a.gambar, a.tanggal, k.kategori 
                        FROM artikel a
                        JOIN kategori k ON a.kategori = k.idkategori
                        WHERE a.penulis_id = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Postingan Saya</title>
    <link rel="stylesheet" href="saya.css">
</head>
<body>
    <header>
        <div class="header-left">
            <h1>DZUI</h1>
        </div>
        <div class="header-right">
            <a href="dashbord2.php" class="dashboard-link">Dashboard</a>
        </div>
    </header>
    <main>
        <aside class="sidebar-container">
            <div class="sidebar">
                <ul>
                    <li><a href="akun.php">Akun Saya</a></li>
                    <li><a href="postingansaya.php">Postingan Saya</a></li>
                    <li><a href="form.php">Posting</a></li>
                    <li><a href="keluarakun.php">Keluar Akun</a></li>
                </ul>
            </div>
        </aside>
        <section class="post-section">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="post-card">';
                    echo '<div class="post-image"><img src="' . $row['gambar'] . '" alt="Gambar Postingan"></div>';
                    echo '<div class="post-content">';
                    echo '<h3>' . $row['judul'] . '</h3>';
                    echo '<p>' . date("d/m/Y", strtotime($row['tanggal'])) . '</p>';
                    echo '<p>Kategori: ' . $row['kategori'] . '</p>';
                    echo '<div class="post-actions">';
                    echo '<button> <a href="editberita.php?id=' . $row['idartikel'] . '">Edit</a></button>';
                    echo '<form action="hapus.php" method="post" style="display: inline-block;">';
                    echo '<input type="hidden" name="id" value="' . $row['idartikel'] . '">';
                    echo '<button type="submit" onclick="return confirm(\'Apakah Anda yakin ingin menghapus postingan ini?\')">Hapus</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p class="no-post">Belum ada postingan.</p>';
            }
            ?>
        </section>
    </main>
</body>
</html>
