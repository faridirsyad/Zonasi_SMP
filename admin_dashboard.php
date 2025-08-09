<?php
include 'admin_only.php';
include 'koneksi.php';

// Ambil semua sekolah yang memiliki koordinat
$markers = [];
$allSekolahQuery = "SELECT id, nama_sekolah, latitude, longitude, alamat_sekolah, korda, daya_tampung FROM sekolah WHERE latitude IS NOT NULL AND longitude IS NOT NULL";
$allResult = $conn->query($allSekolahQuery);
while ($row = $allResult->fetch_assoc()) {
    $markers[] = $row;
}

// Ambil data semua korda + nama geojson
$kordaData = [];
$result = $conn->query("SELECT id, nama_korda, geojson FROM korda");
while ($row = $result->fetch_assoc()) {
    $kordaData[] = $row;
}

// Hitung jumlah sekolah per Korda
$jumlahSekolah = [];
$query = "
    SELECT k.nama_korda, COUNT(s.id) AS total
    FROM sekolah s
    JOIN korda k ON s.korda = k.id
    WHERE s.latitude IS NOT NULL AND s.longitude IS NOT NULL
    GROUP BY k.nama_korda
";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $korda = trim($row['nama_korda']);
    $jumlahSekolah[$korda] = (int)$row['total'];
}

// Hitung jumlah total daya tampung per Korda
$jumlahTampung = [];
$query = "
    SELECT k.nama_korda, SUM(s.daya_tampung) AS total_tampung
    FROM sekolah s
    JOIN korda k ON s.korda = k.id
    WHERE s.latitude IS NOT NULL AND s.longitude IS NOT NULL
    GROUP BY k.nama_korda
";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $korda = trim($row['nama_korda']);
    $jumlahTampung[$korda] = (int)$row['total_tampung'];
}

// Statistik total
$totalSekolah = count($markers);
$totalKecamatan = $conn->query("SELECT COUNT(*) FROM kecamatan")->fetch_row()[0];
$totalDesa = $conn->query("SELECT COUNT(*) FROM desakel")->fetch_row()[0];
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/adminstyle.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
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
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="assets/image/logo.png" alt="Logo" width="40">
                <strong>DINDIKPORA BANJARNEGARA</strong>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php" class="active">Dashboard</a>
                <a href="admin_sekolah.php">Data Sekolah</a>
                <a href="admin_kecamatan.php">Data Kecamatan</a>
                <a href="admin_desakel.php">Data Desa/Kelurahan</a>
                <a href="admin_kelola.php">Kelola Akun Admin</a>
                <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </nav>
        </div>

        <button id="menuToggle" class="menu-button">☰ Menu</button>
        <div class="overlay" id="overlay"></div>

        <div class="main-content">
            <main>
                <h1>Dashboard Admin</h1>

                <div class="card-container">
                    <a href="admin_sekolah.php" class="card-link">
                        <div class="card">
                            <img src="assets/image/school.png" alt="Sekolah" width="40">
                            <p><?= $totalSekolah ?> Sekolah</p>
                        </div>
                    </a>
                    <a href="admin_kecamatan.php" class="card-link">
                        <div class="card">
                            <img src="assets/image/kec.png" alt="Kecamatan" width="40">
                            <p><?= $totalKecamatan ?> Kecamatan</p>
                        </div>
                    </a>
                    <a href="admin_desakel.php" class="card-link">
                        <div class="card">
                            <img src="assets/image/village.png" alt="Desa" width="40">
                            <p><?= $totalDesa ?> Desa/Kelurahan</p>
                        </div>
                    </a>
                </div>

                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Visualisasi Korda</h2>
                    </div>
                    <div id="map"></div>
                </div>

                <br>
                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Informasi Umum</h2>
                        <a href="tambah_informasi.php" class="btn-tambah">Tambah</a>
                    </div>
                    <div class="table-wrapper">
                        <table id="data-table" class="display">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Header</th>
                                    <th>Isi</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $result = $conn->query("SELECT * FROM informasi ORDER BY id DESC");
                                while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['header']) ?></td>
                                        <td><?= htmlspecialchars($row['isi']) ?></td>
                                        <td>
                                            <a href="edit_informasi.php?id=<?= $row['id'] ?>">
                                                <button class="btn-detail">Edit</button>
                                            </a>
                                            <a href="delete_informasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin ingin menghapus?')">
                                                <button class="btn-delete">Hapus</button>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <footer>
        &copy; <?= date('Y') ?> Dindikpora Kab. Banjarnegara. All Rights Reserved
    </footer>

    <script>
        const markersData = <?= json_encode($markers); ?>;
        const kordaData = <?= json_encode($kordaData); ?>;
        const jumlahSekolah = <?= json_encode($jumlahSekolah); ?>;
    </script>
    <script src="assets/script/admin.js"></script>
</body>

</html>