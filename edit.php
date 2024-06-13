<?php
session_start();
include 'koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_SESSION['userid'])) {
        $userid = $_SESSION['userid'];

        // Query untuk mengambil username pengguna berdasarkan ID
        $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
        $stmt->bind_param("i", $userid);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $penulis_username = $user['username'];
        $stmt->close();
    } else {
        header("Location: login.php");
        exit();
    }

    $idartikel = $_POST['idartikel'];
    $judul = $_POST['judul'];
    $tanggal = $_POST['tanggal'];
    $kategori_nama = $_POST['kategori'];
    $konten = $_POST['konten'];

    // Query untuk mengambil ID kategori berdasarkan nama kategori
    $stmt_kategori = $conn->prepare("SELECT idkategori FROM kategori WHERE kategori = ?");
    $stmt_kategori->bind_param("s", $kategori_nama);
    $stmt_kategori->execute();
    $result_kategori = $stmt_kategori->get_result();
    $kategori = $result_kategori->fetch_assoc();
    $kategori_id = $kategori['idkategori'];
    $stmt_kategori->close();

    // Cek apakah ada gambar yang diunggah
    if (!empty($_FILES["gambar"]["name"])) {
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
        if ($_FILES["gambar"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            // Hapus gambar lama jika ada
            $stmt_select_gambar = $conn->prepare("SELECT gambar FROM artikel WHERE idartikel = ?");
            $stmt_select_gambar->bind_param("i", $idartikel);
            $stmt_select_gambar->execute();
            $result_select_gambar = $stmt_select_gambar->get_result();
            $gambar_row = $result_select_gambar->fetch_assoc();
            $gambar_lama = $gambar_row['gambar'];

            if (!empty($gambar_lama)) {
                unlink($gambar_lama);
            }

            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_baru = $target_file;

                // Update data artikel dengan gambar baru
                $stmt_update = $conn->prepare("UPDATE artikel SET judul = ?, gambar = ?, tanggal = ?, kategori = ?, isi = ? WHERE idartikel = ?");
                $stmt_update->bind_param("sssssi", $judul, $gambar_baru, $tanggal, $kategori_id, $konten, $idartikel);

                if ($stmt_update->execute()) {
                    echo "Artikel berhasil diperbarui!";
                    header("Location: dashbord2.php");
                    exit();
                } else {
                    echo "Error updating article: " . $stmt_update->error;
                }

                $stmt_update->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // Tidak ada gambar baru diunggah, update artikel tanpa mengubah gambar
        $stmt_update = $conn->prepare("UPDATE artikel SET judul = ?, tanggal = ?, kategori = ?, isi = ? WHERE idartikel = ?");
        $stmt_update->bind_param("ssssi", $judul, $tanggal, $kategori_id, $konten, $idartikel);
        
        if ($stmt_update->execute()) {
            echo "Artikel berhasil diperbarui!";
            header("Location: postingansaya.php");
            exit();
        } else {
            echo "Error updating article: " . $stmt_update->error;
        }

        $stmt_update->close();
    }

    $conn->close();
}
?>
