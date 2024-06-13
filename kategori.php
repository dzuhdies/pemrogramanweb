<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori</title>
    <link rel="stylesheet" href="bord.css">
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
        <section class="news-section">
            <?php
                include 'koneksi.php';

                if (isset($_GET['kategori'])) {
                    $kategori_id = $_GET['kategori'];
                    $stmt = $conn->prepare("SELECT idartikel, judul, gambar, tanggal, kategori, penulis FROM artikel WHERE kategori = ? ORDER BY tanggal DESC");
                    $stmt->bind_param("i", $kategori_id);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="news-card">';
                            echo '<div class="news-image"><img src="' . $row['gambar'] . '" alt="Gambar Berita"></div>';
                            echo '<div class="news-content">';
                            echo '<a href="berita.php?id=' . $row['idartikel'] . '"><h3>' . $row['judul'] . '</h3></a>';
                            echo '<p>' . date("d/m/Y", strtotime($row['tanggal'])) . '</p>';
                            echo '<p>' . $row['kategori'] . '</p>';
                            echo '<p>' . $row['penulis'] . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo 'No news available for this category.';
                    }

                    $stmt->close();
                } else {
                    echo 'Category not specified.';
                }

                mysqli_close($conn);
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
                                echo '<li><a href="kategori.php?kategori=' . $kategori_row['idkategori'] . '">' . $kategori_row['kategori'] . '</a></li>';
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
                    <li>pendidikan</li>
                    <li>politik</li>
                    <li>kesehatan</li>
                    <li>bisnis</li>
                    <li>teknologi</li>
                </ul>
            </div>
        </aside>
    </main>
</body>
</html>
