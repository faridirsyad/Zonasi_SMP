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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/loginstyle.css?v=<?= time() ?>">
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
        <h2>Reset Password</h2>

        <?php if (!empty($error)) echo "<p class='error'>" . htmlspecialchars($error) . "</p>"; ?>
        <?php if (!empty($success)) echo "<p class='success'>$success</p>"; ?>

        <?php if (empty($success)) : ?>
            <form method="POST">
                <label for="password">Password Baru:</label>
                <input type="password" name="password" id="password" required>

                <label for="confirm_password">Konfirmasi Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <button type="submit">Reset Password</button>
            </form>
        <?php endif; ?>

        <div class="forgot-link">
            <a href="lupa_password.php">← Kembali</a>
        </div>
    </div>

</body>

</html>