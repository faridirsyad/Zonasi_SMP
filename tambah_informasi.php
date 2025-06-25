<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header = $_POST['header'];
    $isi = $_POST['isi'];

    $stmt = $conn->prepare("INSERT INTO informasi (header, isi) VALUES (?, ?)");
    $stmt->bind_param("ss", $header, $isi);
    $stmt->execute();

    header("Location: tambah_informasi.php?success=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Informasi</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Data Informasi</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil ditambahkan.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="header">Header:</label>
            <input type="text" name="header" required>

            <label for="isi">Isi:</label>
            <textarea name="isi" rows="2" required></textarea>

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_dashboard.php">← Kembali</a>
    </div>
</body>


</html>