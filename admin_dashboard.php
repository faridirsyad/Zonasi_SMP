<?php
include 'admin_only.php';
include 'koneksi.php';

function countData($conn, $table)
{
    $result = $conn->query("SELECT COUNT(*) as total FROM $table");
    $data = $result->fetch_assoc();
    return $data['total'];
}

$totalSekolah = countData($conn, 'sekolah');
$totalKecamatan = countData($conn, 'kecamatan');
$totalDesa = countData($conn, 'desakel');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Zonasi SMP Banjarnegara</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Style -->
    <link rel="stylesheet" href="assets/adminstyle.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- jQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
</head>

<body>
    <div class="wrapper">
        <!-- Sidebar -->
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

        <!-- Button mobile -->
        <button id="menuToggle" class="menu-button">☰ Menu</button>
        <div class="overlay" id="overlay"></div>

        <!-- Main Content -->
        <div class="main-content">
            <main>
                <h1>Dashboard Admin</h1>

                <!-- Statistik -->
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

                <!-- Informasi Umum -->
                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Informasi Umum</h2>
                        <a href="tambah_informasi.php" class="btn-tambah">Tambah</a>
                    </div>

                    <?php if (isset($_GET['success_delete'])): ?>
                        <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin-bottom: 15px; border: 1px solid #f5c6cb; border-radius: 5px;">
                            🗑️ Data berhasil dihapus.
                        </div>
                    <?php endif; ?>

                    <div class="tabel-container">
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
                                while ($row = $result->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= htmlspecialchars($row['header']) ?></td>
                                        <td><?= htmlspecialchars($row['isi']) ?></td>
                                        <td>
                                            <a href="edit_informasi.php?id=<?= $row['id'] ?>">
                                                <button class="btn-detail">Edit</button>
                                            </a>
                                            <a href="delete_informasi.php?id=<?= $row['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus informasi ini?')">
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
    <!-- Footer -->
    <footer>
        © <?= date('Y') ?> Dindikpora Kab. Banjarnegara. All Rights Reserved
    </footer>

    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                language: {
                    search: "Cari Informasi : ",
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "",
                    infoEmpty: "",
                    infoFiltered: ""
                },
                initComplete: function() {
                    // Tambahkan kelas .top ke parent agar flexbox aktif
                    $('.dataTables_length').parent().addClass('top');
                }
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#informasiTable').DataTable();
        });

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
</body>

</html>