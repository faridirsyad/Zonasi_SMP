<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_desakel = $_POST['nama_desakel'];
    $kecamatan = $_POST['kecamatan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];


    $stmt = $conn->prepare("INSERT INTO desakel (nama_desakel, kecamatan, latitude, longitude ) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nama_desakel, $kecamatan, $latitude, $longitude);
    $stmt->execute();

    header("Location: tambah_desakel.php?success=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Desa/Kelurahan</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Data Desa/Kelurahan</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil ditambahkan.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="nama_desakel">Nama Desa/Kelurahan:</label>
            <input type="text" name="nama_desakel" required>

            <label for="kecamatan">Kecamatan:</label>
            <input type="text" name="kecamatan" required>

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" required>

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" required>

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_desakel.php">← Kembali</a>
    </div>
</body>


</html>