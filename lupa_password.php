<?php
include 'koneksi.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Format email tidak valid.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $token = bin2hex(random_bytes(50));
            $expires = date("Y-m-d H:i:s", strtotime("+8 hour"));

            $stmt = $conn->prepare("UPDATE admin SET reset_token=?, reset_expire=? WHERE email=?");
            $stmt->bind_param("sss", $token, $expires, $email);
            $stmt->execute();

            $resetLink = "http://localhost/Skripsi/reset_password.php?token=$token";

            // Di sistem nyata, kirim email ke $email dengan $resetLink
            $message = "Link reset password telah dikirim:<br><a href='$resetLink'>$resetLink</a>";
        } else {
            $message = "Email tidak ditemukan di sistem.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Lupa Password</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .box {
            background-color: white;
            border: 2px solid blue;
            border-radius: 10px;
            padding: 30px;
            width: 320px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            color: blue;
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        input[type="email"] {
            width: 100%;
            padding: 8px;
            margin-top: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            margin-top: 20px;
            width: 100%;
            background-color: blue;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background-color: darkblue;
        }

        .message {
            margin-top: 15px;
            color: green;
        }

        .error {
            color: red;
        }

        a {
            word-break: break-all;
        }
    </style>
</head>

<body>

    <div class="box">
        <h2>Lupa Password</h2>

        <?php if ($message): ?>
            <p class="<?= strpos($message, 'Link reset') !== false ? 'message' : 'error' ?>">
                <?= $message ?>
            </p>
        <?php endif; ?>

        <form method="POST">
            <label for="email">Masukkan Email:</label>
            <input type="email" name="email" id="email" placeholder="Email Anda" required>
            <button type="submit">Kirim Link Reset</button>
        </form>
    </div>

</body>

</html>