<?php
session_start();
include 'koneksi.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

// Pastikan metode yang digunakan adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil ID postingan yang akan dihapus
    $post_id = $_POST['id'];

    // Perintah SQL untuk menghapus postingan
    $stmt = $conn->prepare("DELETE FROM artikel WHERE idartikel = ?");
    $stmt->bind_param("i", $post_id);

    // Eksekusi perintah SQL
    if ($stmt->execute()) {
        // Jika penghapusan berhasil, arahkan pengguna kembali ke halaman postingan saya
        header("Location: postingansaya.php");
    } else {
        // Jika terjadi kesalahan, tampilkan pesan kesalahan
        echo "Error: " . $stmt->error;
    }

    // Tutup statement
    $stmt->close();
}

// Tutup koneksi database
$conn->close();
?>
