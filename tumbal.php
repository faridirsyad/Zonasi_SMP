<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npsn = $_POST['npsn'];
    $nama_sekolah = $_POST['nama_sekolah'];
    $alamat_sekolah = $_POST['alamat_sekolah'];
    $daya_tampung = $_POST['daya_tampung'];
    $akreditasi = $_POST['akreditasi'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $conn->prepare("INSERT INTO sekolah (npsn, nama_sekolah, alamat_sekolah, daya_tampung, akreditasi, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $npsn, $nama_sekolah, $alamat_sekolah, $daya_tampung, $akreditasi, $latitude, $longitude);
    $stmt->execute();

    header("Location: tambah_sekolah.php?success=1");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Kecamatan</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Data Kecamatan</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil ditambahkan.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="nama_kecamatan">Nama Kecamatan:</label>
            <input type="text" name="nama_kecamatan" required>

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" required>

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" required>

            <label for="geojson">Geojson:</label>
            <input type="text" name="geojson" required>


            <button type="submit">Simpan</button>
        </form>

        <a href="admin_kecamatan.php">← Kembali</a>
    </div>
</body>


</html>