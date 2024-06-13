<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan ID pengguna yang sedang masuk
$userid = $_SESSION['userid'];

// Query untuk mendapatkan informasi penulis berdasarkan ID pengguna
$stmt_penulis = $conn->prepare("SELECT username FROM users WHERE id = ?");
$stmt_penulis->bind_param("i", $userid);
$stmt_penulis->execute();
$result_penulis = $stmt_penulis->get_result();
$penulis_info = $result_penulis->fetch_assoc();
$penulis = $penulis_info['username']; // Mengambil username penulis dari hasil query
$stmt_penulis->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Artikel</title>
    <link rel="stylesheet" href="porm.css">
    <script>
        // Fungsi untuk mengatur tanggal hari ini pada input tanggal
        function setTodayDate() {
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0'); // Bulan mulai dari 0
            const dd = String(today.getDate()).padStart(2, '0');
            const todayDate = `${yyyy}-${mm}-${dd}`;
            document.getElementById('tanggal').value = todayDate;
        }
        
        // Memastikan tanggal diatur ketika halaman dimuat
        window.onload = setTodayDate;
    </script>
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
                    <li><a href="">Posting</a></li>
                    <li><a href="keluarakun.php">Keluar Akun</a></li>
                </ul>
            </div>
        </aside>
        <section class="account-content">
            <h2>Tambah Artikel Baru</h2>
            <form action="prosesform.php" method="post" enctype="multipart/form-data">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" required>

                <label for="gambar">Gambar:</label>
                <input type="file" id="gambar" name="gambar" required>

                <label for="tanggal">Tanggal:</label>
                <input type="date" id="tanggal" name="tanggal" readonly>

                <label for="kategori">Kategori:</label>
                <select id="kategori" name="kategori" required>
                    <?php
                    $kategori_query = "SELECT idkategori, kategori FROM kategori";
                    $kategori_result = mysqli_query($conn, $kategori_query);

                    if (mysqli_num_rows($kategori_result) > 0) {
                        while ($kategori_row = mysqli_fetch_assoc($kategori_result)) {
                            echo '<option value="' . $kategori_row['idkategori'] . '">' . $kategori_row['kategori'] . '</option>';
                        }
                    } else {
                        echo '<option value="">No categories available</option>';
                    }

                    mysqli_close($conn);
                    ?>
                </select>

                <label for="penulis">Penulis:</label>
                <input type="text" id="penulis" name="penulis" value="<?php echo $penulis; ?>" readonly>

                <label for="konten">Konten:</label>
                <textarea id="konten" name="konten" rows="10" required></textarea>

                <input type="submit" value="Tambah Artikel">
            </form>
        </section>
    </main>
</body>
</html>
