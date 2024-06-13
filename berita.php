<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita</title>
    <link rel="stylesheet" href="berita.css"> <!-- Ganti dengan file CSS Anda -->
</head>
<body>
    <header>
        <div class="header-left">
            <h1>DZUI</h1>
        </div>
        <div class="header-right">
            <a href="dashbord2.php" class="login-link">dashbord</a>
        </div>
    </header>
    <main>
        <br>
        <br>
        <br>
        <section class="article-section">
            <?php
                include 'koneksi.php';

                // Mendapatkan ID artikel dari URL
                $article_id = $_GET['id'];

                // Query untuk mendapatkan artikel berdasarkan ID
                $stmt = $conn->prepare("SELECT judul, gambar, tanggal, kategori, penulis, isi FROM artikel WHERE idartikel = ?");
                $stmt->bind_param("i", $article_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    echo '<div class="article">';
                    echo '<h2>' . $row['judul'] . '</h2>';
                    echo '<div class="article-info">';
                    echo '<p>' . date("d/m/Y", strtotime($row['tanggal'])) . '</p>';
                    
                    // Query untuk mendapatkan nama kategori berdasarkan ID kategori
                    $stmt_kategori = $conn->prepare("SELECT kategori FROM kategori WHERE idkategori = ?");
                    $stmt_kategori->bind_param("i", $row['kategori']);
                    $stmt_kategori->execute();
                    $result_kategori = $stmt_kategori->get_result();
                    $kategori_row = $result_kategori->fetch_assoc();
                    $kategori = $kategori_row['kategori'];
                    echo '<p>' . $kategori . '</p>';
                    $stmt_kategori->close();

                    echo '<p>' . $row['penulis'] . '</p>';
                    echo '</div>';
                    echo '<div class="article-image"><img src="' . $row['gambar'] . '" alt="Gambar Artikel"></div>';
                    echo '<div class="article-content">' . nl2br($row['isi']) . '</div>'; // Menggunakan nl2br untuk mempertahankan enter
                    echo '</div>';
                } else {
                    echo 'Artikel tidak ditemukan.';
                }

                $stmt->close();
                $conn->close();
            ?>
        </section>
    </main>
</body>
</html>
