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

    <title>Tracking Laporan — SAPA Lampung</title>

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
                    <div class="sidebar-brand-name">SAPA Admin</div>
                    <div class="sidebar-brand-sub">Panel Administrator</div>
                </div>

            </div>

            <nav class="sidebar-nav">

                <a href="dashboard.php" class="sidebar-item">
                    Dashboard
                </a>

                <a href="laporan.php" class="sidebar-item">
                    Laporan Masuk
                </a>

                <a href="tracking.php" class="sidebar-item active">
                    Tracking
                </a>

                <a href="statistik.php" class="sidebar-item">
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

            <div class="topbar-title">

                <h2>
                    Tracking Laporan
                </h2>

                <p>
                    Pantau seluruh progres laporan masyarakat
                </p>

            </div>

            <div class="topbar-user">

                <div class="topbar-avatar">
                    <?= strtoupper(substr($nama, 0, 1)) ?>
                </div>

                <span class="topbar-username">
                    <?= htmlspecialchars($nama) ?>
                </span>

            </div>

        </div>

        <div class="content-body">

            <div class="stats-row">

                <div class="stat-box">
                    <div class="stat-box-label">Laporan Baru</div>
                    <div class="stat-box-num">24</div>
                    <div class="stat-box-change">Hari ini</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Sedang Diproses</div>
                    <div class="stat-box-num">342</div>
                    <div class="stat-box-change">Aktif di lapangan</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Selesai Hari Ini</div>
                    <div class="stat-box-num">18</div>
                    <div class="stat-box-change up">+3 dari kemarin</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Terlambat</div>
                    <div class="stat-box-num">7</div>
                    <div class="stat-box-change down">Perlu tindak lanjut</div>
                </div>

            </div>

            <div class="card mb-20">

                <div class="filter-bar">

                    <input
                        type="text"
                        class="form-input"
                        placeholder="Cari ID laporan atau petugas..."
                    >

                    <select class="form-select">
                        <option>Semua Status</option>
                        <option>Pending</option>
                        <option>Diverifikasi</option>
                        <option>Diproses</option>
                        <option>Selesai</option>
                    </select>

                    <select class="form-select">
                        <option>Semua Petugas</option>
                        <option>Ahmad Fauzi</option>
                        <option>Siti Rahayu</option>
                        <option>Bambang Wijaya</option>
                    </select>

                    <button class="btn btn-primary">
                        Filter
                    </button>

                </div>

            </div>

            <div class="card">

                <div class="card-header">

                    <div>
                        <div class="card-title">
                            Semua Laporan
                        </div>

                        <div class="card-subtitle">
                            Monitoring progres laporan masyarakat
                        </div>
                    </div>

                    <button class="btn btn-outline">
                        Export
                    </button>

                </div>

                <div class="table-wrap">

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Petugas</th>
                                <th>Status</th>
                                <th>Progress</th>
                            </tr>
                        </thead>

                        <tbody>

                            <tr>
                                <td>
                                    <span class="id-badge">RPT-0842</span>
                                </td>

                                <td>
                                    Jalan Berlubang di Palapa
                                </td>

                                <td>
                                    Ahmad Fauzi
                                </td>

                                <td>
                                    <span class="pill pill-diproses">
                                        Diproses
                                    </span>
                                </td>

                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width:75%"></div>
                                    </div>

                                    <div class="progress-text">
                                        75%
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="id-badge">RPT-0851</span>
                                </td>

                                <td>
                                    Lampu Jalan Mati
                                </td>

                                <td>
                                    Bambang Wijaya
                                </td>

                                <td>
                                    <span class="pill pill-verifikasi">
                                        Diverifikasi
                                    </span>
                                </td>

                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width:40%"></div>
                                    </div>

                                    <div class="progress-text">
                                        40%
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="id-badge">RPT-0838</span>
                                </td>

                                <td>
                                    Sampah Menumpuk di TPS
                                </td>

                                <td>
                                    Siti Rahayu
                                </td>

                                <td>
                                    <span class="pill pill-diproses">
                                        Diproses
                                    </span>
                                </td>

                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill danger" style="width:50%"></div>
                                    </div>

                                    <div class="progress-text danger-text">
                                        Terlambat
                                    </div>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <span class="id-badge">RPT-0821</span>
                                </td>

                                <td>
                                    Drainase Tersumbat
                                </td>

                                <td>
                                    Siti Rahayu
                                </td>

                                <td>
                                    <span class="pill pill-selesai">
                                        Selesai
                                    </span>
                                </td>

                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill success" style="width:100%"></div>
                                    </div>

                                    <div class="progress-text success-text">
                                        100%
                                    </div>
                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </main>

</div>

<script>
function confirmLogout() {
    const yakin = confirm("Yakin mau keluar dari akun?");

    if (yakin) {
        window.location.href = "../logout.php";
    }
}
</script>

</body>
</html>