<?php
include 'admin_only.php';
include 'koneksi.php';

$currentAdminId = $_SESSION['admin_id'];
$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hashedPassword = !empty($password) ? password_hash($password, PASSWORD_BCRYPT) : null;

    if ($hashedPassword) {
        $stmt = $conn->prepare("UPDATE admin SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->bind_param("sssi", $username, $email, $hashedPassword, $currentAdminId);
    } else {
        $stmt = $conn->prepare("UPDATE admin SET username = ?, email = ? WHERE id = ?");
        $stmt->bind_param("ssi", $username, $email, $currentAdminId);
    }

    if ($stmt->execute()) {
        $success = true;
        $_SESSION['admin_username'] = $username;
    } else {
        $error = "Gagal memperbarui akun.";
    }
}

$stmt = $conn->prepare("SELECT username, email FROM admin WHERE id = ?");
$stmt->bind_param("i", $currentAdminId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Akun Anda</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Akun Anda</h2>

        <!-- Notifikasi sukses -->
        <?php if (!empty($success)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Akun berhasil diperbarui.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" value="<?= htmlspecialchars($data['username']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($data['email']) ?>" required>

            <label for="password">Password Baru (opsional):</label>
            <input type="password" name="password" id="password" placeholder="Kosongkan jika tidak ingin mengganti">

            <button type="submit">Simpan Perubahan</button>
        </form>

        <a href="admin_kelola.php">← Kembali</a>
    </div>
</body>

</html>