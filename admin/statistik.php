<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Statistik — SAPA Admin</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">

</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">

        <div>

            <div class="sidebar-brand">

                <div class="sidebar-logo">
                    SA
                </div>

                <div>
                    <div class="sidebar-brand-name">
                        SAPA Admin
                    </div>

                    <div class="sidebar-brand-sub">
                        Panel Administrator
                    </div>
                </div>

            </div>

            <nav class="sidebar-nav">

                <a href="dashboard.php" class="sidebar-item">
                    Dashboard
                </a>

                <a href="laporan.php" class="sidebar-item">
                    Laporan Masuk
                </a>

                <a href="tracking.php" class="sidebar-item">
                    Tracking
                </a>

                <a href="statistik.php" class="sidebar-item active">
                    Statistik
                </a>

                <a href="petugas.php" class="sidebar-item">
                    Manajemen Petugas
                </a>

                <a href="akun.php" class="sidebar-item">
                    Kelola Akun
                </a>

            </nav>

        </div>

        <div class="sidebar-footer">

            <div class="sidebar-user">

                <div class="sidebar-avatar">
                    <?= strtoupper(substr($nama, 0, 1)) ?>
                </div>

                <div>

                    <div class="sidebar-user-name">
                        <?= htmlspecialchars($nama) ?>
                    </div>

                    <div class="sidebar-user-role">
                        Administrator
                    </div>

                </div>

            </div>

            <a href="#" class="sidebar-logout" onclick="confirmLogout()">
                Keluar
            </a>

        </div>

    </aside>

    <main class="main-content">

        <div class="topbar">

            <div>
                <h2>Statistik</h2>
                <p>Ringkasan data dan performa sistem SAPA</p>
            </div>

            <div class="topbar-user">

                <div class="topbar-avatar">
                    <?= strtoupper(substr($nama, 0, 1)) ?>
                </div>

                <span>
                    <?= htmlspecialchars($nama) ?>
                </span>

            </div>

        </div>

        <div class="content-body">

            <div class="page-header">
                <h1>Statistik Laporan</h1>
                <p>
                    Data performa laporan masyarakat SAPA Lampung.
                </p>
            </div>

            <div class="stats-row">

                <div class="stat-box green-top">
                    <div class="stat-box-label">
                        Tingkat Penyelesaian
                    </div>

                    <div class="stat-box-num green-text">
                        85,1%
                    </div>

                    <div class="stat-box-change up">
                        +2,3% dari bulan lalu
                    </div>
                </div>

                <div class="stat-box blue-top">
                    <div class="stat-box-label">
                        Rata-rata Respons
                    </div>

                    <div class="stat-box-num blue-text">
                        3,2 Jam
                    </div>

                    <div class="stat-box-change up">
                        Lebih cepat 0,8 jam
                    </div>
                </div>

                <div class="stat-box yellow-top">
                    <div class="stat-box-label">
                        Kepuasan Pelapor
                    </div>

                    <div class="stat-box-num yellow-text">
                        4,7/5
                    </div>

                    <div class="stat-box-change up">
                        Naik dari bulan lalu
                    </div>
                </div>

                <div class="stat-box orange-top">
                    <div class="stat-box-label">
                        Petugas Aktif
                    </div>

                    <div class="stat-box-num orange-text">
                        48
                    </div>

                    <div class="stat-box-change">
                        Dari 52 total petugas
                    </div>
                </div>

            </div>

            <div class="grid-2">

                <div class="card">

                    <div class="card-header">
                        <div class="card-title">
                            Laporan per Kategori
                        </div>

                        <div class="card-subtitle">
                            Total laporan berdasarkan kategori
                        </div>
                    </div>

                    <div class="card-body">

                        <div class="bar-chart">

                            <div class="bar-item">
                                <div class="bar-val">1240</div>
                                <div class="bar-fill blue-bar" style="height:100%"></div>
                                <div class="bar-label">Infrastruktur</div>
                            </div>

                            <div class="bar-item">
                                <div class="bar-val">856</div>
                                <div class="bar-fill purple-bar" style="height:75%"></div>
                                <div class="bar-label">Fasilitas</div>
                            </div>

                            <div class="bar-item">
                                <div class="bar-val">412</div>
                                <div class="bar-fill orange-bar" style="height:40%"></div>
                                <div class="bar-label">Keamanan</div>
                            </div>

                            <div class="bar-item">
                                <div class="bar-val">1089</div>
                                <div class="bar-fill green-bar" style="height:88%"></div>
                                <div class="bar-label">Kebersihan</div>
                            </div>

                            <div class="bar-item">
                                <div class="bar-val">183</div>
                                <div class="bar-fill red-bar" style="height:20%"></div>
                                <div class="bar-label">Darurat</div>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="card">

                    <div class="card-header">

                        <div class="card-title">
                            Status Laporan
                        </div>

                        <div class="card-subtitle">
                            Total 4.821 laporan
                        </div>

                    </div>

                    <div class="card-body">

                        <div class="donut-row">

                            <div class="donut-visual">

                                <div class="donut-inner">

                                    <div>
                                        <div class="donut-pct">
                                            85%
                                        </div>

                                        <div class="donut-pct-label">
                                            Selesai
                                        </div>
                                    </div>

                                </div>

                            </div>

                            <div class="donut-legend">

                                <div class="legend-item">
                                    <div class="legend-dot green-dot"></div>
                                    <span>Selesai</span>
                                    <strong>4102</strong>
                                </div>

                                <div class="legend-item">
                                    <div class="legend-dot orange-dot"></div>
                                    <span>Diproses</span>
                                    <strong>342</strong>
                                </div>

                                <div class="legend-item">
                                    <div class="legend-dot blue-dot"></div>
                                    <span>Verifikasi</span>
                                    <strong>200</strong>
                                </div>

                                <div class="legend-item">
                                    <div class="legend-dot yellow-dot"></div>
                                    <span>Pending</span>
                                    <strong>177</strong>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </main>

</div>

<script>
function confirmLogout() {

    let yakin = confirm("Yakin ingin keluar dari akun?");

    if(yakin){
        window.location.href = "../logout.php";
    }

}
</script>

<script>
function confirmLogout() {

    let yakin = confirm("Yakin ingin keluar dari akun?");

    if(yakin){
        window.location.href = "../logout.php";
    }

}
</script>

</body>
</html>