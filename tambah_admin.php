<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO admin (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashedPassword);
    $stmt->execute();

    header("Location: tambah_admin.php?success=1");
    exit;
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
        <?php endif; ?>

        <form method="POST">
            <label for="username"><strong>Username:</strong></label>
            <input type="text" name="username" id="username" required>

            <label for="email"><strong>Email:</strong></label>
            <input type="email" name="email" id="email" required>

            <label for="password"><strong>Password:</strong></label>
            <input type="password" name="password" id="password" required>

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_kelola.php" style="display: inline-block; margin-top: 15px; color: #007bff; text-decoration: none;">← Kembali</a>
    </div>
</body>

</html>