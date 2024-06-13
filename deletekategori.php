<?php
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = $_POST['category_id'];

    // Query untuk menghapus kategori berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM kategori WHERE idkategori = ?");
    $stmt->bind_param("s", $category_id);

    if ($stmt->execute()) {
        // Redirect kembali ke halaman admin setelah menghapus kategori
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
