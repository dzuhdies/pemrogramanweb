<?php
session_start();

// Check if user is logged in
if (isset($_SESSION['userid'])) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Check if user confirms logout
        if (isset($_POST['confirm_logout'])) {
            // Destroy the session
            session_unset();
            session_destroy();
            header("Location: dashbord.php");
            exit();
        } else {
            // If user cancels logout, redirect back to account page or any other page
            header("Location: akun.php");
            exit();
        }
    }
} else {
    // If user is not logged in, redirect to login page
    header("Location: dashbord.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            background-color: #fff;
            width: 300px;
            margin: 100px auto;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h2 {
            margin-top: 0;
        }
        .btn {
            padding: 10px 20px;
            margin: 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        .btn-confirm {
            background-color: #4CAF50;
            color: #fff;
        }
        .btn-cancel {
            background-color: #f44336;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Are you sure you want to log out?</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="submit" name="confirm_logout" value="Yes" class="btn btn-confirm">
            <input type="submit" name="cancel_logout" value="No" class="btn btn-cancel">
        </form>
    </div>
</body>
</html>
