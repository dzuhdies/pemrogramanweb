<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan ID pengguna yang sedang masuk dari sesi
    if (isset($_SESSION['userid'])) {
        $userid = $_SESSION['userid'];

        // Query untuk mengambil username pengguna berdasarkan ID
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $penulis_username = $user['username']; // Ambil username pengguna

        $stmt->close();
    } else {
        // Jika ID pengguna tidak tersedia, lakukan sesuatu, seperti mengarahkan pengguna ke halaman login
        header("Location: login.php");
        exit();
    }

    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $kategori_id = $_POST['kategori']; // Ambil ID kategori dari formulir
    $konten = $_POST['konten'];

    // Upload Gambar
    $target_dir = "upload/";
    $target_file = $target_dir . basename($_FILES["gambar"]["name"]);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["gambar"]["tmp_name"]);
    if ($check !== false) {
        $uploadOk = 1;
    } else {
        echo "File is not an image.";
        $uploadOk = 0;
    }

    // Check file size
    if ($_FILES["gambar"]["size"] > 50000000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $uploadOk = 0;
    }

    // Check if $uploadOk is set to 0 by an error
    if ($uploadOk == 0) {
        echo "Sorry, your file was not uploaded.";
    // if everything is ok, try to upload file
    } else {
        if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
            $gambar = $target_file;

            // Prepare an insert statement
            $stmt = $conn->prepare("INSERT INTO artikel (judul, gambar, tanggal, kategori, penulis, isi) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $judul, $gambar, $tanggal, $kategori_id, $penulis_username, $konten);

            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                echo "Artikel berhasil ditambahkan!";
                header("Location: dashbord2.php");
            } else {
                echo "Error: " . $stmt->error;
            }

            // Close statement
            $stmt->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }

    // Close connection
    $conn->close();
}
?>
