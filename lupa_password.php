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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <link rel="stylesheet" href="assets/loginstyle.css">
</head>

<body>

    <div class="logo">
        <img src="assets/image/logo.png" alt="Logo">
        <div class="instansi-info">
            Dinas Pendidikan Kepemudaan<br>
            dan Olahraga<br>
            Kabupaten Banjarnegara
        </div>
    </div>

    <div class="login-box">
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

        <div class="forgot-link">
            <a href="login.php">← Kembali ke Login</a>
        </div>
    </div>

</body>

</html>