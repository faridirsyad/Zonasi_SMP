<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_desakel = trim($_POST['nama_desakel']);
    $kecamatan = trim($_POST['kecamatan']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);

    $stmt = $conn->prepare("INSERT INTO desakel (nama_desakel, kecamatan, latitude, longitude) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_desakel, $kecamatan, $latitude, $longitude);
    $stmt->execute();

    header("Location: tambah_desakel.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Desa/Kelurahan</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Data Desa/Kelurahan</h2>

        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil ditambahkan.
            </div>
        <?php elseif (!empty($error)): ?>
            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 5px;">
                ❌ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="nama_desakel">Nama Desa/Kelurahan:</label>
            <input type="text" name="nama_desakel" required pattern=".*\S+.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="kecamatan">Kecamatan:</label>
            <input type="text" name="kecamatan" required pattern=".*\S+.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: -7.123456">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: 109.123456">

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_desakel.php">← Kembali</a>
    </div>
</body>

</html>