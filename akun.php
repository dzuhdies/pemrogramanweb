<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}

$userid = $_SESSION['userid'];
$success = "";
$error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update'])) {
        $full_name = htmlspecialchars(trim($_POST['full_name']));
        $username = htmlspecialchars(trim($_POST['username']));
        $email = htmlspecialchars(trim($_POST['email']));
        $phone = htmlspecialchars(trim($_POST['phone']));

        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $username, $email, $userid);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Update user profile
            $stmt = $conn->prepare("UPDATE users SET full_name = ?, username = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $full_name, $username, $email, $phone, $userid);

            if ($stmt->execute()) {
                $success = "Profile updated successfully.";
            } else {
                $error = "Error updating profile: " . $stmt->error;
            }
        }

        $stmt->close();
    } elseif (isset($_POST['delete'])) {
        // Delete account
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userid);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: login.php");
            exit();
        } else {
            $error = "Error deleting account: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Fetch user data
$stmt = $conn->prepare("SELECT username, email, full_name, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $userid);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya</title>
    <link rel="stylesheet" href="aku.css">
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
                    <li><a href="">Akun Saya</a></li>
                    <li><a href="postingansaya.php">Postingan Saya</a></li>
                    <li><a href="form.php">Posting</a></li>
                    <li><a href="keluarakun.php">Keluar Akun</a></li>
                </ul>
            </div>
        </aside>
        <section class="account-content">
            <h2>Data Pribadi</h2>
            <?php if ($error) echo '<p class="error">'.htmlspecialchars($error).'</p>'; ?>
            <?php if ($success) echo '<p class="success">'.htmlspecialchars($success).'</p>'; ?>
            <form id="profile-form" action="akun.php" method="post">
                <label for="full_name">Nama Lengkap</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

                <label for="username">Username <span style="font-size: smaller;">(username tidak bisa diganti)</span></label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>

                <div class="buttons">
                    <button type="submit" name="update" class="blue-button">Update Profile</button>
                    <button type="submit" name="delete" class="red-button" onclick="return confirm('Are you sure you want to delete your account?');">Hapus Akun</button>
                </div>
            </form>
        </section>
    </main>
</body>
</html>
