<?php
include 'admin_only.php';
include 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

$success = isset($_GET['success']);

$query = $conn->prepare("SELECT * FROM sekolah WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $npsn = $_POST['npsn'];
    $nama_sekolah = $_POST['nama_sekolah'];
    $alamat_sekolah = $_POST['alamat_sekolah'];
    $daya_tampung = $_POST['daya_tampung'];
    $akreditasi = $_POST['akreditasi'];
    $korda = $_POST['korda'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $conn->prepare("UPDATE sekolah SET npsn = ?, nama_sekolah = ?, alamat_sekolah = ?, daya_tampung = ?, akreditasi = ?, korda = ?, latitude = ?, longitude = ? WHERE id = ?");
    $stmt->bind_param("ssssssssi", $npsn, $nama_sekolah, $alamat_sekolah, $daya_tampung, $akreditasi, $korda, $latitude, $longitude, $id);
    $stmt->execute();

    header("Location: edit_sekolah.php?id=$id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Sekolah</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Data Sekolah</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil diupdate.
            </div>
        <?php endif; ?>
        <form method="POST">
            <label for="npsn">NPSN:</label>
            <input type="text" name="npsn" value="<?= htmlspecialchars($data['npsn']) ?>" required pattern="\d+" title="Hanya angka tanpa spasi">

            <label for="nama_sekolah">Nama Sekolah:</label>
            <input type="text" name="nama_sekolah" value="<?= htmlspecialchars($data['nama_sekolah']) ?>" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for=" alamat_sekolah">Alamat Sekolah:</label>
            <input type="text" name="alamat_sekolah" value="<?= htmlspecialchars($data['alamat_sekolah']) ?>" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for=" daya_tampung">Daya Tampung:</label>
            <input type="text" name="daya_tampung" value="<?= htmlspecialchars($data['daya_tampung']) ?>" required pattern="\d+" title="Hanya angka tanpa spasi">

            <label for="akreditasi">Akreditasi:</label>
            <input type="text" name="akreditasi" value="<?= htmlspecialchars($data['akreditasi']) ?>" required pattern="^[A-C]{1}$" title="Hanya huruf A, B, atau C saja" maxlength="1" autocomplete="off">

            <label for="korda">Korda:</label>
            <input type="text" name="korda" value="<?= htmlspecialchars($data['korda']) ?>" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" value="<?= htmlspecialchars($data['latitude']) ?>" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: -7.123456">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" value="<?= htmlspecialchars($data['longitude']) ?>" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: 109.123456">

            <button type="submit">Update</button>
        </form>

        <a href="admin_sekolah.php">← Kembali</a>
    </div>
</body>

</html>