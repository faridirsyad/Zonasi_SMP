<?php
include 'koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: informasi_sekolah.php');
    exit;
}

$id = intval($_GET['id']);
$query = "SELECT * FROM sekolah WHERE id = $id";
$result = $conn->query($query);
$data = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Detail Sekolah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .container2 {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 20px;
            align-items: center;
            /* untuk memastikan konten terpusat */
        }

        .map-container2 {
            width: 80%;
            margin: 0 auto;
        }

        .info-box2 {
            background-color: #f9f9f9;
            border: 3px solid blue;
            padding: 20px;
            border-radius: 10px;
            max-height: 600px;
            overflow-y: auto;
            min-width: 250px;
            width: 80%;
            margin: 0 auto;
        }

        #map2 {
            height: 600px;
            width: 100%;
            border: 3px solid blue;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            #map2 {
                height: 400px;
            }

            .map-container2,
            .info-box2 {
                width: 95%;
            }
        }

        .btn-direction,
        .btn-back {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 14px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .btn-direction:hover,
        .btn-back:hover {
            background-color: #0056b3;
        }

        .info-box2 h3 {
            text-align: center;
            color: #0000FF;
            margin-bottom: 15px;
        }

        .info-box2 table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-box2 td {
            padding: 6px;
            vertical-align: top;
        }

        .info-box2 td:first-child {
            font-weight: bold;
            width: 40%;
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
                <a class="active" href="informasi_sekolah.php">Informasi Sekolah</a>
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

        <main>
            <h2>Detail Sekolah</h2>
            <div class="container2">
                <div class="map-container2">
                    <div id="map2"></div>
                    <?php if ($data && $data['latitude'] && $data['longitude']): ?>
                        <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $data['latitude'] ?>,<?= $data['longitude'] ?>"
                            target="_blank"
                            class="btn-direction">
                            Penunjuk Arah
                        </a>
                    <?php endif; ?>
                </div>

                <div class="info-box2">

                    <br><br><br>
                    <?php if ($data): ?>
                        <h3><?= htmlspecialchars($data['nama_sekolah']) ?></h3>
                        <table>
                            <tr>
                                <td>NPSN</td>
                                <td><?= htmlspecialchars($data['npsn']) ?></td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td><?= htmlspecialchars($data['alamat_sekolah']) ?></td>
                            </tr>
                            <tr>
                                <td>Akreditasi</td>
                                <td><?= htmlspecialchars($data['akreditasi']) ?></td>
                            </tr>
                            <tr>
                                <td>Korda</td>
                                <td><?= htmlspecialchars($data['korda']) ?></td>
                            </tr>
                            <tr>
                                <td>Daya Tampung</td>
                                <td><?= htmlspecialchars($data['daya_tampung']) ?> siswa</td>
                            </tr>
                        </table>
                        <a class="btn-back" href="informasi_sekolah.php">← Kembali ke daftar sekolah</a>
                    <?php else: ?>
                        <p style="color:red;">Data sekolah tidak ditemukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </main>

        <footer>
            &copy; Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
        </footer>
    </div>

    <script>
        const school = <?= json_encode([
                            'nama_sekolah' => $data['nama_sekolah'],
                            'latitude' => $data['latitude'],
                            'longitude' => $data['longitude'],
                            'id' => $data['id'],
                        ]) ?>;

        const map = L.map('map2').setView([school.latitude, school.longitude], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        if (school.latitude && school.longitude) {
            L.marker([school.latitude, school.longitude])
                .addTo(map)
                .bindPopup(`<strong>${school.nama_sekolah}</strong>`)
                .openPopup();
        }

        var legend = L.control({
            position: "bottomleft"
        });
        legend.onAdd = function(map) {
            var div = L.DomUtil.create("div", "info legend");
            div.innerHTML += `
                <h4>Keterangan</h4>
                <i style="background: blue; width: 10px; height: 10px; display: inline-block; margin-right: 5px;"></i> Lokasi Sekolah<br>
            `;
            return div;
        };
        legend.addTo(map);
    </script>
</body>

</html>