<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi username tidak boleh mengandung spasi
    if (preg_match('/\s/', $username)) {
        $error = "Username tidak boleh mengandung spasi.";
    }
    // Validasi email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    }
    // Validasi password: panjang minimal 8, gabungan huruf dan angka, tidak hanya spasi
    elseif (
        strlen($password) < 8 ||
        !preg_match('/[a-zA-Z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        trim($password) === ''
    ) {
        $error = "Password minimal 8 karakter dan harus mengandung huruf & angka (tidak boleh hanya spasi).";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $hashedPassword);
        $stmt->execute();

        header("Location: tambah_admin.php?success=1");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Akun Admin</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Akun Admin</h2>

        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Akun admin berhasil ditambahkan.
            </div>
        <?php elseif (!empty($error)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 5px;">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="username"><strong>Username:</strong></label>
            <input type="text" name="username" id="username" required
                pattern="^\S+$"
                title="Username tidak boleh mengandung spasi">

            <label for="email"><strong>Email:</strong></label>
            <input type="email" name="email" id="email" required>

            <label for="password"><strong>Password:</strong></label>
            <input type="password" name="password" id="password"
                minlength="8"
                pattern="^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d\S]{8,}$"
                title="Password minimal 8 karakter, harus mengandung huruf dan angka, dan tidak boleh hanya spasi"
                required>

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_kelola.php" style="display: inline-block; margin-top: 15px; color: #007bff; text-decoration: none;">← Kembali</a>
    </div>
</body>

</html>