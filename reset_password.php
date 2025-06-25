<?php
include 'koneksi.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Token tidak ditemukan.");
}

$stmt = $conn->prepare("SELECT * FROM admin WHERE reset_token = ? AND reset_expire > NOW()");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Token tidak valid atau sudah kadaluarsa.");
}

$admin = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $newPassword = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];

    if ($newPassword !== $confirmPassword) {
        $error = "Password tidak cocok.";
    } else {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("UPDATE admin SET password = ?, reset_token = NULL, reset_expire = NULL WHERE id = ?");
        $stmt->bind_param("si", $hashedPassword, $admin['id']);
        $stmt->execute();

        $success = "Password berhasil diubah. <a href='login.php'>Klik di sini untuk login</a>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .reset-box {
            background-color: white;
            border: 2px solid #007BFF;
            border-radius: 10px;
            padding: 30px 40px;
            width: 350px;
            box-shadow: 0 0 15px rgba(0, 123, 255, 0.1);
        }

        .reset-box h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="password"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #007BFF;
            border-radius: 5px;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        .message {
            text-align: center;
            margin-top: 15px;
        }

        .message a {
            color: #007BFF;
            text-decoration: none;
        }

        .message a:hover {
            text-decoration: underline;
        }

        .error {
            color: red;
            text-align: center;
            margin-bottom: 10px;
        }

        .success {
            color: green;
            text-align: center;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

    <div class="reset-box">
        <h2>Reset Password</h2>

        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

        <?php if (empty($success)) : ?>
            <form method="POST">
                <label>Password Baru:</label>
                <input type="password" name="password" required>

                <label>Konfirmasi Password:</label>
                <input type="password" name="confirm_password" required>

                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>
    </div>

</body>

</html>