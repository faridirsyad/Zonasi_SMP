<?php
include 'admin_only.php';
include 'koneksi.php';

// Tangkap dan validasi ID
$id = $_GET['id'] ?? null;
if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

// Tangkap parameter sukses
$success = isset($_GET['success']);

// Ambil data berdasarkan ID
$query = $conn->prepare("SELECT * FROM informasi WHERE id = ?");
$query->bind_param("i", $id);
$query->execute();
$result = $query->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    echo "Data tidak ditemukan.";
    exit;
}

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $header = $_POST['header'];
    $isi = $_POST['isi'];

    $stmt = $conn->prepare("UPDATE informasi SET header = ?, isi = ? WHERE id = ?");
    $stmt->bind_param("ssi", $header, $isi, $id);
    $stmt->execute();

    header("Location: edit_informasi.php?id=$id&success=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Informasi</title>
    <link rel="stylesheet" href="assets/datastyle.css">
</head>

<body>
    <div class="form-container">
        <h2>Edit Data Informasi</h2>

        <!-- Pesan sukses -->
        <?php if ($success): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border: 1px solid #c3e6cb; border-radius: 5px;">
                ✅ Data telah berhasil diupdate.
            </div>
        <?php endif; ?>

        <form method="POST">
            <label for="header">Header:</label>
            <input type="text" name="header" id="header" value="<?= htmlspecialchars($data['header']) ?>" required>

            <label for="isi">Isi:</label>
            <textarea name="isi" id="isi" rows="3" required><?= htmlspecialchars($data['isi']) ?></textarea>

            <button type="submit">Update</button>
        </form>

        <a href="admin_dashboard.php">← Kembali</a>
    </div>
</body>

</html>