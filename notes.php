<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_message = '';
$error_message = '';

// Ambil nama pengguna
$user_query = "SELECT username FROM users WHERE id='$user_id'";
$user_result = $conn->query($user_query);
$user = $user_result->fetch_assoc();
$username = $user['username'];

// Handle new note addition
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['content'])) {
    $content = $_POST['content'];
    $sql = "INSERT INTO notes (user_id, content) VALUES ('$user_id', '$content')";
    if ($conn->query($sql) === TRUE) {
        $success_message = "Catatan berhasil ditambahkan.";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Handle note deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_notes'])) {
    if (isset($_POST['note_ids'])) {
        $note_ids = implode(',', $_POST['note_ids']);
        $sql = "DELETE FROM notes WHERE id IN ($note_ids) AND user_id='$user_id'";
        if ($conn->query($sql) === TRUE) {
            $success_message = "Catatan berhasil dihapus.";
        } else {
            $error_message = "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        $error_message = "Tidak ada catatan yang dipilih untuk dihapus.";
    }
}

// Handle delete all notes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_all_notes'])) {
    $sql = "DELETE FROM notes WHERE user_id='$user_id'";
    if ($conn->query($sql) === TRUE) {
        $success_message = "Semua catatan berhasil dihapus.";
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

$sql = "SELECT * FROM notes WHERE user_id='$user_id' ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <head>
    <link rel="icon" href="/note/asset/notesapp_logo.png" type="image/png">
</head>

    <title>Notes App</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
            margin: 0;
        }
        .notes-container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }
        .notes-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .notes-container textarea {
            width: 100%;
            height: 100px;
            padding: 0px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        .notes-container input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 4px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }
        .notes-container input[type="submit"]:hover {
            background-color: #45a049;
        }
        .notes-container ul {
            list-style-type: none;
            padding: 0;
        }
        .notes-container ul li {
            background-color: #f9f9f9;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notes-container ul li .date {
            font-size: 0.9em;
            color: #777;
            text-align: right;
        }
        .success-message, .error-message {
            text-align: center;
            margin: 10px 0;
            padding: 10px;
            border-radius: 4px;
            width: 80%;
            max-width: 600px;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .logout-link {
            text-align: center;
            margin-top: 20px;
        }
        .logout-link a {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }
        .logout-link a:hover {
            background-color: #0056b3;
        }
        .notes-container form {
            margin-top: 20px;
        }
        .notes-container .delete-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .notes-container .delete-actions button {
            background-color: #ff4c4c;
            color: white;
            border: none;
            border-radius: 4px;
            padding: 10px;
            cursor: pointer;
        }
        .notes-container .delete-actions button:hover {
            background-color: #ff0000;
        }
        .username {
    font-family: 'Courier New', Courier, monospace; /* Ganti dengan font yang diinginkan */
    color: #007bff; /* Ganti dengan warna yang diinginkan */
    font-weight: bold;
}

    </style>
</head>
<body>
    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="notes-container">
        <div style="text-align: center;">
            <img src="/note/asset/notesapp_logo.png" alt="Catatan Anda" style="max-width: 150px; height: auto;">
            <div style="font-size: 18px; margin-top: 10px;">
    Selamat datang, <span class="username"><?php echo htmlspecialchars($username); ?></span>!
</div>

        </div>

        <form method="post">
            <textarea name="content" placeholder="Tulis catatan Anda di sini..." required></textarea><br>
            <input type="submit" value="Tambah Catatan">
        </form>
        
        <h3>Semua Catatan</h3>
        <form method="post">
            <ul>
                <?php while($row = $result->fetch_assoc()): ?>
                    <li>
                        <input type="checkbox" name="note_ids[]" value="<?php echo $row['id']; ?>">
                        <?php echo nl2br($row['content']); ?>
                        <div class="date"><?php echo $row['created_at']; ?></div>
                    </li>
                <?php endwhile; ?>
            </ul>
            <div class="delete-actions">
                <button type="submit" name="delete_notes">Hapus Catatan Terpilih</button>
                <button type="submit" name="delete_all_notes">Hapus Semua Catatan</button>
            </div>
        </form>
    </div>
    <div class="logout-link" style="text-align: center;">
        <a href="logout.php">
            Logout
        </a>
    </div>
</body>
</html>
