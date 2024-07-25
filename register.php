<?php
session_start();
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES ('$username', '$password')";
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php");
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .register-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .register-container h2 {
            margin-bottom: 20px;
            color: #333;
        }
        .register-container input[type="text"],
        .register-container input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .register-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }
        .register-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .error-message {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
        .login-link {
            margin-top: 15px;
        }
        .login-link a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: bold;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
    <div style="text-align: center;">
            <img src="/note/asset/notesapp_logo.png" alt="Catatan Anda" style="max-width: 150px; height: auto;">
    </div>
        <h2>Register</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="submit" value="Register">
        </form>
        <div class="login-link">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
