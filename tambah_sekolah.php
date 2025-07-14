<?php
include 'admin_only.php';
include 'koneksi.php';

$success = isset($_GET['success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npsn = trim($_POST['npsn']);
    $nama_sekolah = trim($_POST['nama_sekolah']);
    $alamat_sekolah = trim($_POST['alamat_sekolah']);
    $daya_tampung = trim($_POST['daya_tampung']);
    $akreditasi = trim($_POST['akreditasi']);
    $latitude = trim($_POST['latitude']);
    $longitude = trim($_POST['longitude']);

    $stmt = $conn->prepare("INSERT INTO sekolah (npsn, nama_sekolah, alamat_sekolah, daya_tampung, akreditasi, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $npsn, $nama_sekolah, $alamat_sekolah, $daya_tampung, $akreditasi, $latitude, $longitude);
    $stmt->execute();

    header("Location: tambah_sekolah.php?success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Sekolah</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Tambah Data Sekolah</h2>

        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil ditambahkan.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="npsn">NPSN:</label>
            <input type="text" name="npsn" required pattern="\d+" title="Hanya angka tanpa spasi">

            <label for="nama_sekolah">Nama Sekolah:</label>
            <input type="text" name="nama_sekolah" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="alamat_sekolah">Alamat Sekolah:</label>
            <input type="text" name="alamat_sekolah" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="daya_tampung">Daya Tampung:</label>
            <input type="text" name="daya_tampung" required pattern="\d+" title="Hanya angka tanpa spasi">

            <label for="akreditasi">Akreditasi:</label>
            <input type="text" name="akreditasi" required pattern="[A-E]" title="Hanya huruf A, B, C, D, atau E saja">

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" required pattern="^-?\d{1,3}(\.\d+)?$" title="Masukkan angka desimal, contoh: -7.123456">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" required pattern="^-?\d{1,3}(\.\d+)?$" title="Masukkan angka desimal, contoh: 109.123456">

            <button type="submit">Simpan</button>
        </form>

        <a href="admin_sekolah.php">← Kembali</a>
    </div>
</body>

</html>