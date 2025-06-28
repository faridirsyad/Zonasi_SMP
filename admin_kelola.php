<?php
include 'admin_only.php';
include 'koneksi.php';

$currentAdminId = $_SESSION['admin_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Admin - Zonasi SMP Banjarnegara</title>
    <link rel="stylesheet" href="assets/adminstyle.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
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
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_sekolah.php">Data Sekolah</a>
                <a href="admin_kecamatan.php">Data Kecamatan</a>
                <a href="admin_desakel.php">Data Desa/Kelurahan</a>
                <a href="admin_kelola.php" class="active">Kelola Akun Admin</a>
                <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </nav>
        </div>

        <!-- Tombol Sidebar Mobile -->
        <button id="menuToggle" class="menu-button">☰ Menu</button>
        <div class="overlay" id="overlay"></div>

        <!-- Konten Utama -->
        <div class="main-content">
            <main>
                <h1>Kelola Akun</h1>

                <!-- Akun Anda -->
                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Akun Anda</h2>
                        <a href="edit_admin.php" class="btn-tambah">Edit</a>
                    </div>
                    <div class="akun-box">
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Password</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmtSelf = $conn->prepare("SELECT username, email FROM admin WHERE id = ?");
                                    $stmtSelf->bind_param("i", $currentAdminId);
                                    $stmtSelf->execute();
                                    $resultSelf = $stmtSelf->get_result();

                                    if ($rowSelf = $resultSelf->fetch_assoc()):
                                        $maskedPassword = str_repeat('*', 6); // Tampilkan selalu 6 bintang
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($rowSelf['username']) ?></td>
                                            <td><?= htmlspecialchars($rowSelf['email']) ?></td>
                                            <td><?= $maskedPassword ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="3">Data akun tidak ditemukan.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <br><br>

                <!-- Akun Lain -->
                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Akun Lain</h2>
                        <a href="tambah_admin.php" class="btn-tambah">Tambah</a>
                    </div>
                    <div class="akun-box">
                        <div class="table-wrapper">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $stmtOthers = $conn->prepare("SELECT username, email FROM admin WHERE id != ?");
                                    $stmtOthers->bind_param("i", $currentAdminId);
                                    $stmtOthers->execute();
                                    $resultOthers = $stmtOthers->get_result();

                                    while ($row = $resultOthers->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['username']) ?></td>
                                            <td><?= htmlspecialchars($row['email']) ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        &copy; Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
    </footer>

    <!-- Script Toggle Sidebar -->
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
</body>

</html>