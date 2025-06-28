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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin</title>
    <link rel="stylesheet" href="assets/loginstyle.css">
</head>

<body>

    <div class="logo">
        <img src="assets/image/logo.png" alt="Logo">
        <div class="instansi-info">
            <p>Dinas Pendidikan Kepemudaan<br>dan Olahraga<br>Kabupaten Banjarnegara</p>
        </div>
    </div>


    <div class="login-box">
        <h2>LOGIN ADMIN</h2>

        <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Masukkan username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>

            <div class="captcha-row">
                <img src="captcha.php" alt="captcha" onclick="this.src='captcha.php?rand=' + Math.random()">
                <input type="text" name="captcha" placeholder="Masukkan captcha" required>
            </div>


            <button class="submit-btn" type="submit">Login</button>
        </form>

        <div class="forgot-link">
            <a href="lupa_password.php">Lupa Password?</a><br><br>
            <a href="index.php">← Kembali ke Home</a>
        </div>
    </div>

</body>

</html>