<?php
session_start();
include 'koneksi.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $passwordInput = $_POST['password'];
    $captchaInput = $_POST['captcha'] ?? '';

    if ($captchaInput !== ($_SESSION['captcha'] ?? '')) {
        $error = "Captcha salah.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($passwordInput, $admin['password'])) {
            $_SESSION['admin'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Admin</title>
    <style>
        body {
            background-color: #f2f2f2;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo img {
            width: 80px;
        }

        .logo h3 {
            margin: 5px 0;
        }

        .login-box {
            background-color: white;
            border: 2px solid blue;
            border-radius: 10px;
            padding: 25px 35px;
            width: 320px;
            box-shadow: 0 0 15px rgba(0, 0, 255, 0.05);
        }

        .login-box h2 {
            text-align: center;
            margin-bottom: 20px;
            color: black;
        }

        label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }

        .form-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }

        .form-group label {
            width: 100px;
        }

        .form-group input {
            flex: 1;
            padding: 6px;
            border: 1px solid blue;
            border-radius: 5px;
            outline: none;
        }

        .captcha {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .captcha img {
            height: 40px;
            margin-right: 10px;
            cursor: pointer;
        }

        .submit-btn {
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

        .submit-btn:hover {
            background-color: darkblue;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 10px;
        }

        .forgot-link {
            text-align: center;
            margin-top: 15px;
        }

        .forgot-link a {
            color: blue;
            text-decoration: none;
        }

        .forgot-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <div class="logo">
        <img src="assets/image/logo.png" alt="Logo">
        <h3>Dinas Pendidikan Kepemudaan<br>dan Olahraga<br>Kabupaten Banjarnegara</h3>
    </div>

    <div class="login-box">
        <h2>LOGIN ADMIN</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username :</label>
                <input type="text" name="username" placeholder="username" required>
            </div>

            <div class="form-group">
                <label>Password :</label>
                <input type="password" name="password" placeholder="password" required>
            </div>

            <div class="form-group captcha">
                <img src="captcha.php" alt="captcha" onclick="this.src='captcha.php?rand=' + Math.random()">
                <input type="text" name="captcha" placeholder="captcha" required>
            </div>

            <button class="submit-btn" type="submit">Submit</button>
        </form>

        <div class="forgot-link">
            <a href="lupa_password.php">Lupa Password?</a>
            <br><br>
            <a href="index.php">← Kembali Ke Home</a>
        </div>

    </div>

</body>

</html>