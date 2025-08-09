<?php
include 'admin_only.php';
include 'koneksi.php';

function countData($conn, $table)
{
    $result = $conn->query("SELECT COUNT(*) as total FROM kecamatan");
    $data = $result->fetch_assoc();
    return $data['total'];
}

$totalKecamatan = countData($conn, 'kecamatan');

?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Zonasi SMP Banjarnegara</title>
    <link rel="stylesheet" href="assets/adminstyle.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body>
    <div class="wrapper">
        <div class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <img src="assets/image/logo.png" alt="Logo" width="40">
                <strong>DINDIKPORA BANJARNEGARA</strong>
            </div>
            <nav class="sidebar-nav">
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_sekolah.php">Data Sekolah</a>
                <a href="admin_kecamatan.php" class="active">Data Kecamatan</a>
                <a href="admin_desakel.php">Data Desa/Kelurahan</a>
                <a href="admin_kelola.php">Kelola Akun Admin</a>
                <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </nav>
        </div>
        <!-- <button class="sidebar-toggle" onclick="toggleSidebar()">☰ Menu</button> -->
        <button id="menuToggle" class="menu-button">☰ Menu</button>
        <div class="overlay" id="overlay"></div>


        <div class="main-content">
            <!-- Main Content -->
            <main>
                <h1>Dashboard Admin</h1>

                <div class="container">
                    <div class="card-container">
                        <a href="admin_kecamatan.php" class="card-link">
                            <div class="card">
                                <i><img src="assets/image/kec.png" alt="Logo" width="40"></i>
                                <p><?= $totalKecamatan ?> Kecamatan</p>
                            </div>
                        </a>
                    </div>


                    <!-- Info Umum -->
                    <div class="info-box">
                        <div class="info-box-header">
                            <h2>Data Kecamatan</h2>
                            <a href="tambah_kecamatan.php" class="btn-tambah">Tambah</a>
                        </div>
                        <?php if (isset($_GET['success_delete'])): ?>
                            <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 5px;">
                                🗑️ Data berhasil dihapus.
                            </div>
                        <?php endif; ?>
                        <div class="table-wrapper">
                            <table id="data-table" class="display">
                                <thead>
                                    <th>No</th>
                                    <th>Nama Kecamatan</th>
                                    <th>Latitude</th>
                                    <th>Longitude</th>
                                    <th>Korda</th>
                                    <th>Geojson</th>
                                    <th>Aksi</th>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    $query = "SELECT * FROM kecamatan";
                                    $result = $conn->query($query);
                                    while ($row = $result->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= htmlspecialchars($row['nama_kecamatan']) ?></td>
                                            <td><?= htmlspecialchars($row['latitude']) ?></td>
                                            <td><?= htmlspecialchars($row['longitude']) ?></td>
                                            <td><?= htmlspecialchars($row['korda']) ?></td>
                                            <td><?= htmlspecialchars($row['geojson']) ?></td>
                                            <td>
                                                <a href="edit_kecamatan.php?id=<?= $row['id'] ?>">
                                                    <button class="btn-detail">Edit</button>
                                                </a>
                                                <a href="delete_kecamatan.php?id=<?= $row['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus informasi ini?')">
                                                    <button class="btn-delete">Hapus</button>
                                                </a>

                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
            <!-- Footer -->
        </div>

        <script>
            const menuButton = document.getElementById('menuToggle');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('active');
                overlay.classList.toggle('active');
                menuButton.textContent = sidebar.classList.contains('active') ? '✖ Close' : '☰ Menu';
            }

            menuButton.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        </script>

    </div>
    <footer>
        © Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                language: {
                    search: "Cari Nama Kecamatan : ",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "",
                    infoEmpty: "",
                    infoFiltered: ""
                },
                initComplete: function() {
                    $('.dataTables_length').parent().addClass('top');
                }
            });
        });
    </script>


    <script>
        function confirmLogout(event) {
            event.preventDefault(); // Menghentikan aksi default link

            if (confirm("Apakah Anda yakin ingin logout?")) {
                window.location.href = "logout.php";
            }
        }
    </script>

    <script src="assets/script/index.js"></script>
</body>

</html>