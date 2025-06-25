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
    <title>Informasi Zonasi SMP Banjarnegara</title>
    <link rel="stylesheet" href="assets/adminstyle.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <style>
        .btn-edit,
        .btn-tambah {
            background-color: green;
            color: white;
            padding: 6px 14px;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }

        .akun-box {
            border: 2px solid blue;
            border-radius: 10px;
            padding: 30px;
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            color: black;
            background-color: white;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            padding: 10px;
            border: 1px solid #ccc;
        }

        th {
            background-color: rgb(39, 8, 239);
        }

        .wrapper {
            display: flex;
        }

        .main-content {
            flex: 1;
            padding: 20px;
        }

        .menu-button {
            display: none;
        }

        @media (max-width: 768px) {
            .wrapper {
                flex-direction: column;
            }

            .menu-button {
                display: block;
                margin: 10px;
                font-size: 18px;
            }

            .sidebar {
                position: absolute;
                left: -100%;
                top: 0;
                height: 100%;
                background: white;
                width: 250px;
                z-index: 1000;
                transition: left 0.3s;
            }

            .sidebar.active {
                left: 0;
            }

            .overlay {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
            }

            .overlay.active {
                display: block;
            }
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
                <a href="admin_dashboard.php">Dashboard</a>
                <a href="admin_sekolah.php">Data Sekolah</a>
                <a href="admin_kecamatan.php">Data Kecamatan</a>
                <a href="admin_desakel.php">Data Desa/Kelurahan</a>
                <a href="admin_kelola.php" class="active">Kelola Akun Admin</a>
                <a href="logout.php" class="logout" onclick="return confirm('Yakin ingin logout?')">Logout</a>
            </nav>
        </div>

        <button id="menuToggle" class="menu-button">☰ Menu</button>
        <div class="overlay" id="overlay"></div>

        <div class="main-content">
            <main>
                <h1>Kelola Akun</h1>

                <!-- Akun Anda -->
                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Akun Anda</h2>
                        <a href="edit_admin.php" class="btn-edit">Edit</a>
                    </div>
                    <div class="akun-box">
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
                                    $maskedPassword = str_repeat('*', 6);
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

                <br><br>

                <!-- Akun Lain -->
                <div class="info-box">
                    <div class="info-box-header">
                        <h2>Akun Lain</h2>
                        <a href="tambah_admin.php" class="btn-tambah">Tambah</a>
                    </div>
                    <div class="akun-box">
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
            </main>
        </div>
    </div>

    <footer style="text-align:center; padding: 10px;">
        &copy; Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
    </footer>

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