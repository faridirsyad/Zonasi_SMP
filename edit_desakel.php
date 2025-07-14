<?php
include 'admin_only.php';
include 'koneksi.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

$success = isset($_GET['success']);

$query = $conn->prepare("SELECT * FROM desakel WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_desakel = $_POST['nama_desakel'];
    $kecamatan = $_POST['kecamatan'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $stmt = $conn->prepare("UPDATE desakel SET nama_desakel = ?, kecamatan = ?, latitude = ?, longitude = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $nama_desakel, $kecamatan, $latitude, $longitude, $id);
    $stmt->execute();

    header("Location: edit_desakel.php?id=$id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Desa/Kelurahan</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Data Desa/Kelurahan</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil diupdate.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="nama_desakel">Nama Kecamatan:</label>
            <input type="text" name="nama_desakel" id="nama_desakel" value="<?= htmlspecialchars($data['nama_desakel']) ?>" required pattern=".*\S+.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="kecamatan">Kecamatan:</label>
            <input type="text" name="kecamatan" id="kecamatan" value="<?= htmlspecialchars($data['kecamatan']) ?>" required pattern=".*\S+.*" title="Tidak boleh kosong atau hanya spasi">

            <label for="latitude">Latitude:</label>
            <input type="text" name="latitude" value="<?= htmlspecialchars($data['latitude']) ?>" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: -7.123456">

            <label for="longitude">Longitude:</label>
            <input type="text" name="longitude" value="<?= htmlspecialchars($data['longitude']) ?>" required pattern="^-?\d{1,3}(\.\d+)?$" step="any"
                title="Masukkan angka desimal, contoh: 109.123456">


            <button type="submit">Update</button>
        </form>

        <a href="admin_desakel.php">← Kembali</a>
    </div>
</body>

</html>