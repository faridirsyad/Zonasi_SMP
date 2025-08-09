<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kecamatan = trim($_POST['nama_kecamatan']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);
    $korda = trim($_POST['korda']);
    $geojson = trim($_POST['geojson']);

    $stmt = $conn->prepare("INSERT INTO kecamatan (nama_kecamatan, latitude, longitude, korda, geojson) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_kecamatan, $latitude, $longitude, $korda, $geojson);
    $stmt->execute();

    header("Location: tambah_kecamatan.php?success=1");
    exit;
}
?>



<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Kecamatan</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Data Kecamatan</h2>

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
            <label for="nama_kecamatan">Nama Kecamatan:</label>
            <input type="text" name="nama_kecamatan" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: -7.123456">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: 109.123456">

            <label for="korda">Korda:</label>
            <input type="text" name="korda" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="geojson">GeoJSON (contoh: nama_file.geojson):</label>
            <input type="text" name="geojson" required
                pattern="[a-zA-Z0-9_\-\.]+\.geojson"
                title="Nama file harus diakhiri .geojson">

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_kecamatan.php">← Kembali</a>
    </div>
</body>

</html>