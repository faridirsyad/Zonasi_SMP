<?php
include 'admin_only.php';
include 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

$success = isset($_GET['success']);

$query = $conn->prepare("SELECT * FROM kecamatan WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kecamatan = $_POST['nama_kecamatan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $geojson = $_POST['geojson'];

    $stmt = $conn->prepare("UPDATE kecamatan SET nama_kecamatan = ?, latitude = ?, longitude = ?, geojson = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nama_kecamatan, $latitude, $longitude, $geojson, $id);
    $stmt->execute();

    header("Location: edit_kecamatan.php?id=$id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Kecamatan</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Data Kecamatan</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil diupdate.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="nama_kecamatan">Nama Kecamatan:</label>
            <input type="text" name="nama_kecamatan" id="nama_kecamatan" value="<?= htmlspecialchars($data['nama_kecamatan']) ?>" required pattern=".*\S.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" value="<?= htmlspecialchars($data['latitude']) ?>" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: -7.123456">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" value="<?= htmlspecialchars($data['longitude']) ?>" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: 109.123456">

            <label for="geojson">Geojson:</label>
            <input type="text" name="geojson" id="geojson" value="<?= htmlspecialchars($data['geojson']) ?>" required
                pattern="[a-zA-Z0-9_\-\.]+\.geojson"
                title="Nama file harus diakhiri .geojson">

            <button type="submit">Update</button>
        </form>

        <a href="admin_kecamatan.php">← Kembali</a>
    </div>
</body>

</html>