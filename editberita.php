<?php
session_start();
include 'koneksi.php';

// Cek apakah pengguna sudah login
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Mendapatkan ID artikel dari URL
$artikel_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($artikel_id) {
    // Query untuk mendapatkan data artikel berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM artikel WHERE idartikel = ?");
    $stmt->bind_param("i", $artikel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $artikel = $result->fetch_assoc();
    $stmt->close();

    if (!$artikel) {
        echo "Artikel tidak ditemukan.";
        exit();
    }
} else {
    echo "ID artikel tidak diberikan.";
    exit();
}

// Update artikel jika form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = $_POST['judul'];
    $kategori_nama = $_POST['kategori'];
    $konten = $_POST['konten'];
    $gambar = $artikel['gambar']; // Keep existing image if no new image is uploaded

    // Inisialisasi variabel $uploadOk
    $uploadOk = 1;

    // Jika ada gambar yang diupload
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["gambar"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["gambar"]["size"] > 5000000) { // Limit to 5MB
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk === 1) {
            // Hapus gambar lama jika ada
            if (!empty($gambar)) {
                unlink($gambar);
            }

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    }

    // Mengambil idkategori berdasarkan nama kategori
    $stmt = $conn->prepare("SELECT idkategori FROM kategori WHERE kategori = ?");
    $stmt->bind_param("s", $kategori_nama);
    $stmt->execute();
    $result = $stmt->get_result();
    $kategori = $result->fetch_assoc();
    $idkategori = $kategori['idkategori'];
    $stmt->close();

    if ($uploadOk === 1) {
        $stmt = $conn->prepare("UPDATE artikel SET judul = ?, gambar = ?, kategori = ?, isi = ? WHERE idartikel = ?");
        $stmt->bind_param("ssssi", $judul, $gambar, $idkategori, $konten, $artikel_id);

        if ($stmt->execute()) {
            header("Location: postingansaya.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Artikel</title>
    <link rel="stylesheet" href="editberita.css">
</head>
<body>
    <header>
        <div class="header-left">
            <h1>DZUI</h1>
        </div>
        <div class="header-right">
            <a href="postingansaya.php" class="dashboard-link">Back</a>
        </div>
    </header>
    <main>
        <form action="editberita.php?id=<?php echo htmlspecialchars($artikel_id); ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="judul">Judul:</label>
                <input type="text" id="judul" name="judul" value="<?php echo htmlspecialchars($artikel['judul']); ?>" required>
            </div>
            <div class="form-group">
                <label for="gambar">Gambar:</label>
                <input type="file" id="gambar" name="gambar">
                <?php if ($artikel['gambar']): ?>
                    <img src="<?php echo htmlspecialchars($artikel['gambar']); ?>" alt="Gambar Artikel" style="width: 100px; height: auto; margin-top: 10px;">
                    <p>Gambar saat ini</p>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label for="kategori">Kategori:</label>
                <select id="kategori" name="kategori" required>
                    <?php
                        $kategori_query = "SELECT idkategori, kategori FROM kategori";
                        $kategori_result = mysqli_query($conn, $kategori_query);
                        while ($kategori_row = mysqli_fetch_assoc($kategori_result)) {
                            $selected = ($kategori_row['kategori'] == $artikel['kategori']) ? 'selected' : '';
                            echo '<option value="' . htmlspecialchars($kategori_row['kategori']) . '" ' . $selected . '>' . htmlspecialchars($kategori_row['kategori']) . '</option>';
                        }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="konten">Konten:</label>
                <textarea id="konten" name="konten" rows="10" required><?php echo htmlspecialchars($artikel['isi']); ?></textarea>
            </div>
            <button type="submit">Update Artikel</button>
        </form>
    </main>
</body>
</html>
