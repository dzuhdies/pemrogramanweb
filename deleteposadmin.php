<?php
session_start();

// Pastikan hanya admin yang bisa mengakses halaman ini
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] !== true) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = $_POST['post_id'];

    // Query untuk menghapus postingan berdasarkan ID
    $stmt = $conn->prepare("DELETE FROM artikel WHERE idartikel = ?");
    $stmt->bind_param("i", $post_id);

    if ($stmt->execute()) {
        // Redirect kembali ke dashboard admin setelah menghapus
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: admin_dashboard.php");
    exit();
}
?>
