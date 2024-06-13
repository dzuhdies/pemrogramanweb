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
    $kategori = $_POST['kategori'];
    $konten = $_POST['konten'];
    $gambar = $artikel['gambar']; // Keep existing image if no new image is uploaded

    // Jika ada gambar yang diupload
    if (!empty($_FILES['gambar']['name'])) {
        $target_dir = "upload/";
        $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

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
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk === 1) {
        // Ambil ID kategori berdasarkan nama kategori
        $stmt_kategori = $conn->prepare("SELECT idkategori FROM kategori WHERE kategori = ?");
        $stmt_kategori->bind_param("s", $kategori);
        $stmt_kategori->execute();
        $result_kategori = $stmt_kategori->get_result();
        $kategori_row = $result_kategori->fetch_assoc();
        $kategori_id = $kategori_row['idkategori'];
        $stmt_kategori->close();

        $stmt = $conn->prepare("UPDATE artikel SET judul = ?, gambar = ?, kategori = ?, isi = ? WHERE idartikel = ?");
        $stmt->bind_param("ssssi", $judul, $gambar, $kategori_id, $konten, $artikel_id);

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
