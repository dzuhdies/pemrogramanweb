<?php
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id']; // Ambil ID kategori dari formulir
    $category_name = $_POST['category_name'];

    // Query untuk menambahkan kategori baru
    $stmt = $conn->prepare("INSERT INTO kategori (idkategori, kategori) VALUES (?, ?)");
    $stmt->bind_param("ss", $category_id, $category_name);

    if ($stmt->execute()) {
        // Redirect kembali ke halaman admin setelah menambahkan kategori
        header("Location: managekategori.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: managekategori.php");
    exit();
}
?>
