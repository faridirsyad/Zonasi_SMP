<?php
include 'koneksi.php';

// Ambil data sekolah dari database
$sekolahData = [];
$result = $conn->query("SELECT * FROM sekolah");
while ($row = $result->fetch_assoc()) {
    $sekolahData[] = $row;
}

$desaData = [];
$result = $conn->query("SELECT * FROM desakel");
while ($row = $result->fetch_assoc()) {
    $desaData[] = $row;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Zonasi Berdasarkan Sekolah</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .container2 {
            display: flex;
            flex-direction: row;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .map-container2 {
            flex: 2;
            min-width: 300px;
        }

        .info-box2 {
            flex: 1;
            background-color: #f9f9f9;
            border: 2px solid #ccc;
            padding: 20px;
            border-radius: 10px;
            max-height: 600px;
            overflow-y: auto;
            min-width: 250px;
        }

        #map2 {
            height: 600px;
            width: 100%;
            border: 3px solid blue;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .container2 {
                flex-direction: column;
            }

            .map-container2,
            .info-box2 {
                width: 100%;
            }

            #map2 {
                height: 400px;
            }
        }

        .school-card {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .school-card h4 {
            margin: 0;
            font-size: 16px;
            color: #2c3e50;
        }

        .school-card p {
            margin: 2px 0;
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="wrapper">
        <header>
            <div style="display: flex; align-items: center;">
                <img src="assets/image/logo.png" alt="Logo" width="60">
                <div>
                    <strong>Dinas Pendidikan Kepemudaan<br>dan Olahraga<br>Kabupaten Banjarnegara</strong>
                </div>
            </div>
            <nav>
                <a href="index.php">Home</a>
                <a href="informasi_sekolah.php">Informasi Sekolah</a>
                <div class="dropdown">
                    <a href="#">Informasi Zonasi ▾</a>
                    <div class="dropdown-content">
                        <a href="dasar_alamat.php">Berdasarkan Alamat</a>
                        <a class="active" href="dasar_sekolah.php">Berdasarkan Sekolah</a>
                    </div>
                </div>
                <a href="login.php">Admin</a>
            </nav>
        </header>

        <main>
            <h2>Informasi Zonasi</h2>
            <h3>Berdasarkan Sekolah</h3><br>

            <div style="margin-bottom: 10px;">
                <label for="dropdown-sekolah"><strong>Pilih Sekolah:</strong></label>
                <select id="dropdown-sekolah" onchange="fokusKeSekolahDropdown(this.value)">
                    <option value="">-- Pilih Sekolah --</option>
                </select>
            </div>

            <div class="container2">
                <div class="map-container2">
                    <div id="map2"></div>
                </div>
                <div class="info-box2" id="info-box">
                    <p>Pilih sekolah dari dropdown untuk melihat radius zonasi dan desa terdekat.</p>
                </div>
            </div>
        </main>

        <footer>
            &copy; Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
        </footer>
    </div>

    <script>
        const sekolahData = <?= json_encode($sekolahData) ?>;
        const desaData = <?= json_encode($desaData) ?>;
    </script>

    <script src="assets/script/dasar_sekolah.js"></script>
</body>

</html>