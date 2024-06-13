<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="bord.css">
    <style>
        .login-link {
            color: white; /* Mengatur warna teks menjadi putih */
            text-decoration: none; /* Menghilangkan dekorasi tautan */
        }
    </style>
</head>
<body>
    <header>
        <div class="header-left">
        <h1><a href="dashbord2.php" class="login-link">DZUI</a></h1>
        </div>
        <div class="header-right">
            <a href="akun.php" class="login-link">Akun</a>
        </div>
    </header>
    <main>
        <section class="news-section">
            <?php
                include 'koneksi.php';

                // Check if category is set in URL
                $category_id = isset($_GET['kategori']) ? $_GET['kategori'] : null;

                if ($category_id) {
                    $stmt = $conn->prepare("SELECT idartikel, judul, gambar, tanggal, kategori, penulis FROM artikel WHERE kategori = ? ORDER BY tanggal DESC");
                    $stmt->bind_param("i", $category_id);
                } else {
                    $stmt = $conn->prepare("SELECT idartikel, judul, gambar, tanggal, kategori, penulis FROM artikel ORDER BY tanggal DESC");
                }

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="news-card">';
                        echo '<div class="news-image"><img src="' . htmlspecialchars($row['gambar']) . '" alt="Gambar Berita"></div>';
                        echo '<div class="news-content">';
                        echo '<h3>' . htmlspecialchars($row['judul']) . '</h3>';
                        echo '<p>' . date("d/m/Y", strtotime($row['tanggal'])) . '</p>';
                        echo '<p>' . htmlspecialchars($row['penulis']) . '</p>';
                        echo '</div>';
                        echo '<a href="berita.php?id=' . htmlspecialchars($row['idartikel']) . '" class="read-more-btn">Baca Selengkapnya >> </a>';
                        echo '</div>';
                    }
                } else {
                    echo 'No news available.';
                }

                $stmt->close();
                $conn->close();
            ?>
        </section>
        <aside>
            <div class="kategori">
                <h4>Kategori</h4>
                <ul>
                    <?php
                        include 'koneksi.php';

                        $kategori_query = "SELECT idkategori, kategori FROM kategori";
                        $kategori_result = mysqli_query($conn, $kategori_query);

                        if (mysqli_num_rows($kategori_result) > 0) {
                            while ($kategori_row = mysqli_fetch_assoc($kategori_result)) {
                                echo '<li><a href="dashbord2.php?kategori=' . htmlspecialchars($kategori_row['idkategori']) . '">' . htmlspecialchars($kategori_row['kategori']) . '</a></li>';
                            }
                        } else {
                            echo '<li>No categories available.</li>';
                        }

                        mysqli_close($conn);
                    ?>
                </ul>
            </div>
            <div class="hot-news">
                <h4>Hot News</h4>
                <ul>
                    <?php
                        include 'koneksi.php';

                        $hot_news_query = "SELECT idartikel, judul FROM artikel ORDER BY tanggal DESC LIMIT 4";
                        $hot_news_result = mysqli_query($conn, $hot_news_query);

                        if (mysqli_num_rows($hot_news_result) > 0) {
                            while ($hot_news_row = mysqli_fetch_assoc($hot_news_result)) {
                                echo '<li><a href="berita.php?id=' . htmlspecialchars($hot_news_row['idartikel']) . '">' . htmlspecialchars($hot_news_row['judul']) . '</a></li>';
                            }
                        } else {
                            echo '<li>No hot news available.</li>';
                        }

                        mysqli_close($conn);
                    ?>
                </ul>
            </div>
        </aside>
    </main>
</body>
</html>
