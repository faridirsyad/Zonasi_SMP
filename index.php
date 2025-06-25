<?php
include 'koneksi.php';

// Ambil semua sekolah
$markers = [];
$allSekolahQuery = "SELECT id, nama_sekolah, latitude, longitude, alamat_sekolah FROM sekolah WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
$allResult = $conn->query($allSekolahQuery);
while ($row = $allResult->fetch_assoc()) {
    $markers[] = $row;
}

// Ambil data kecamatan + geojson
$kecamatanData = [];
$result = $conn->query("SELECT nama_kecamatan, geojson FROM kecamatan");
while ($row = $result->fetch_assoc()) {
    $kecamatanData[] = $row;
}

$jumlahSekolah = [];
$query = "SELECT alamat_sekolah FROM sekolah WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $alamat = $row['alamat_sekolah'];
    preg_match('/Kecamatan\s+([a-zA-Z\s]+)/i', $alamat, $match);
    $kecamatan = $match[1] ?? trim(array_slice(explode(',', $alamat), -1)[0]);
    $kecamatan = trim($kecamatan);
    if (!isset($jumlahSekolah[$kecamatan])) {
        $jumlahSekolah[$kecamatan] = 0;
    }
    $jumlahSekolah[$kecamatan]++;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Informasi Zonasi SMP Banjarnegara</title>
    <link rel="stylesheet" href="assets/style.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .leaflet-top.leaflet-right {
            top: 10px;
            right: 10px;
        }

        .custom-control {
            background: white;
            padding: 8px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(13, 3, 151, 0.2);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <!-- Header -->
        <header>
            <div style="display: flex; align-items: center;">
                <img src="assets/image/logo.png" alt="Logo" width="60">
                <div>
                    <strong>Dinas Pendidikan Kepemudaan<br>dan Olahraga<br>Kabupaten Banjarnegara</strong>
                </div>
            </div>
            <nav>
                <a class="active" href="index.php">Home</a>
                <a href="informasi_sekolah.php">Informasi Sekolah</a>
                <div class="dropdown">
                    <a href="#">Informasi Zonasi ▾</a>
                    <div class="dropdown-content">
                        <a href="dasar_alamat.php">Berdasarkan Alamat</a>
                        <a href="dasar_sekolah.php">Berdasarkan Sekolah</a>
                    </div>
                </div>
                <a href="login.php">Admin</a>
            </nav>
        </header>

        <!-- Main -->
        <main>
            <h2>Informasi Zonasi SMP<br>Kabupaten Banjarnegara</h2>
            <div class="container">
                <div id="map"></div>

                <!-- Info Umum -->
                <div class="info-box">
                    <h4>Informasi Umum</h4>
                    <?php
                    $query = "SELECT * FROM informasi";
                    $result = $conn->query($query);
                    while ($row = $result->fetch_assoc()):
                    ?>
                        <p><?= htmlspecialchars($row['header']) ?><br><strong><?= htmlspecialchars($row['isi']) ?></strong></p><br>
                    <?php endwhile; ?>
                </div>
            </div>
        </main>

        <footer>
            © Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
        </footer>
    </div>

    <script>
        const markersData = <?= json_encode($markers); ?>;
        const kecamatanData = <?= json_encode($kecamatanData); ?>;
        const jumlahSekolah = <?= json_encode($jumlahSekolah); ?>;
    </script>
    <script src="assets/script/index.js"></script>
</body>

</html>