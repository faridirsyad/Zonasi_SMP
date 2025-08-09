<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Sekolah</title>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/style.css">

    <style>
        .btn-detail {
            background-color: #0000FF;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-detail:hover {
            background-color: #4169e1;
        }

        .dataTables_wrapper .dataTables_paginate {
            text-align: right;
            margin-top: 10px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 5px 10px;
            margin: 0 2px;
            border: 1px solid #0000FF;
            border-radius: 4px;
            background-color: #f0f8ff;
            color: #0000FF !important;
            cursor: pointer;
            transition: 0.3s;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current,
        .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
            background-color: #0000FF !important;
            /* Biru tua */
            color: white !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) {
            background-color: #87CEFA !important;
            /* Hover biru muda */
            color: white !important;
        }

        #sekolah-table thead th {
            text-align: center;
            vertical-align: middle;
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
            <h2>Informasi Sekolah</h2>
            <div class="tabel-container">
                <table id="sekolah-table" class="display">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>NPSN</th>
                            <th>Nama Sekolah</th>
                            <th>Alamat</th>
                            <th>Daya Tampung</th>
                            <th>Akreditasi</th>
                            <th>Korda</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $no = 1;
                        $query = "SELECT * FROM sekolah";
                        $result = $conn->query($query);
                        while ($row = $result->fetch_assoc()):
                        ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= htmlspecialchars($row['npsn']) ?></td>
                                <td><?= htmlspecialchars($row['nama_sekolah']) ?></td>
                                <td><?= htmlspecialchars($row['alamat_sekolah']) ?></td>
                                <td><?= htmlspecialchars($row['daya_tampung']) ?></td>
                                <td><?= htmlspecialchars($row['akreditasi']) ?></td>
                                <td><?= htmlspecialchars($row['korda']) ?></td>
                                <td>
                                    <a href="detail_sekolah.php?id=<?= $row['id'] ?>">
                                        <button class="btn-detail">Profil Sekolah</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>

        <footer>
            © Copyright Dindikpora Kab. Banjarnegara. All Rights Reserved
        </footer>
    </div>

    <!-- JS DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- Inisialisasi DataTables -->
    <script>
        $(document).ready(function() {
            var table = $('#sekolah-table').DataTable({
                language: {
                    search: "", // Kosongkan label default
                    lengthMenu: "Tampilkan _MENU_ data",
                    zeroRecords: "Data tidak ditemukan",
                    info: "",
                    infoEmpty: "",
                    infoFiltered: ""
                },
                initComplete: function() {
                    // Ganti label default filter dengan filter khusus Nama Sekolah
                    $('#sekolah-table_filter').html(`
                    <label>Cari Nama Sekolah :
                        <input type="text" id="search-nama">
                    </label>
                `);

                    // Kolom ke-2 (index 2) adalah Nama Sekolah
                    $('#search-nama').on('keyup', function() {
                        table.column(2).search(this.value).draw();
                    });
                }
            });
        });
    </script>




</body>

</html>