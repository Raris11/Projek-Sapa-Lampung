<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'masyarakat') {
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
    <title>Tracking Laporan - SAPA Lampung</title>

    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/masyarakat.css">
</head>

<body>

<div class="dashboard-layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div>

            <div class="sidebar-brand">
                <div class="sidebar-logo">S</div>

                <div>
                    <div class="sidebar-brand-name">SAPA</div>
                    <div class="sidebar-brand-sub">Portal Masyarakat</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item">Dashboard</a>
                <a href="buat-laporan.php" class="sidebar-item">Buat Laporan</a>
                <a href="tracking.php" class="sidebar-item active">Tracking Laporan</a>
                <a href="riwayat.php" class="sidebar-item">Riwayat Laporan</a>
            </nav>

        </div>

        <div class="sidebar-footer">

            <div class="sidebar-user">

                <div class="sidebar-avatar">
                    <?= strtoupper(substr($nama,0,1)); ?>
                </div>

                <div>
                    <div class="sidebar-user-name"><?= $nama; ?></div>
                    <div class="sidebar-user-role">Masyarakat</div>
                </div>

            </div>

            <button class="sidebar-logout" onclick="confirmLogout()">
                Keluar
            </button>

        </div>

    </aside>

    <!-- MAIN -->
    <main class="main-content">

        <!-- TOPBAR -->
        <div class="topbar">

            <div>
                <h1 class="page-title">Tracking Laporan</h1>
                <p class="page-subtitle">
                    Pantau status laporan Anda secara realtime
                </p>
            </div>

            <div class="topbar-right">

                <div class="topbar-avatar">
                    <?= strtoupper(substr($nama,0,1)); ?>
                </div>

                <div>
                    <div class="topbar-name"><?= $nama; ?></div>
                    <div class="sidebar-user-role">Warga Lampung</div>
                </div>

            </div>

        </div>

        <!-- CONTENT -->
        <div class="content-body">

            <!-- SEARCH -->
            <div class="card">

                <div class="tracking-search">

                    <input
                        type="text"
                        class="tracking-input"
                        placeholder="Masukkan ID laporan"
                        value="RPT-2024-0842"
                    >

                    <button
                        class="tracking-btn"
                        id="openTrackingPopup"
                    >
                        Cari Laporan
                    </button>

                </div>

            </div>

        </div>

    </main>

</div>

<!-- =========================================
     POPUP TRACKING
========================================= -->
<div class="tracking-popup" id="trackingPopup">

    <div class="tracking-popup-content tracking-result-popup">

        <button class="close-popup" id="closeTrackingPopup">
            ×
        </button>

        <div class="popup-icon">
            📄
        </div>

        <h2>Hasil Tracking</h2>

        <p>
            Detail laporan berhasil ditemukan.
        </p>

        <!-- GRID -->
        <div class="tracking-grid popup-grid">

            <!-- LEFT -->
            <div class="tracking-card">

                <div class="tracking-header">

                    <div>
                        <h3>RPT-2024-0842</h3>
                        <p>Jalan Berlubang di Depan SD Negeri 1</p>
                    </div>

                    <span class="status-badge process">
                        Diproses
                    </span>

                </div>

                <div class="tracking-info">

                    <div class="info-row">
                        <span>Kategori</span>
                        <strong>🏗 Infrastruktur</strong>
                    </div>

                    <div class="info-row">
                        <span>Tanggal Lapor</span>
                        <strong>12 November 2024</strong>
                    </div>

                    <div class="info-row">
                        <span>Lokasi</span>
                        <strong>Bandar Lampung</strong>
                    </div>

                    <div class="info-row">
                        <span>Petugas</span>
                        <strong>Ahmad Fauzi</strong>
                    </div>

                </div>

                <div class="progress-section">

                    <div class="progress-top">
                        <span>Progress Penanganan</span>
                        <strong>75%</strong>
                    </div>

                    <div class="progress-bar-track">
                        <div class="progress-fill" style="width:75%"></div>
                    </div>

                </div>

            </div>

            <!-- RIGHT -->
            <div class="tracking-card">

                <h3 class="timeline-title">
                    Timeline Progress
                </h3>

                <div class="timeline">

                    <div class="timeline-item">

                        <div class="timeline-dot"></div>

                        <div class="timeline-content">
                            <h4>Laporan Dibuat</h4>
                            <span>12 Nov 2024</span>
                            <p>Laporan berhasil dikirimkan.</p>
                        </div>

                    </div>

                    <div class="timeline-item">

                        <div class="timeline-dot"></div>

                        <div class="timeline-content">
                            <h4>Diverifikasi Admin</h4>
                            <span>12 Nov 2024</span>
                            <p>Laporan diverifikasi admin.</p>
                        </div>

                    </div>

                    <div class="timeline-item">

                        <div class="timeline-dot"></div>

                        <div class="timeline-content">
                            <h4>Petugas Ditugaskan</h4>
                            <span>13 Nov 2024</span>
                            <p>Petugas menuju lokasi.</p>
                        </div>

                    </div>

                    <div class="timeline-item">

                        <div class="timeline-dot pending-dot"></div>

                        <div class="timeline-content">
                            <h4 class="pending-text">
                                Laporan Selesai
                            </h4>

                            <span>
                                Menunggu proses selesai
                            </span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<script>

function confirmLogout() {
    const yakin = confirm("Yakin mau keluar?");
    if (yakin) {
        window.location.href = "../logout.php";
    }
}

/* POPUP */
const trackingPopup = document.getElementById("trackingPopup");
const openTrackingPopup = document.getElementById("openTrackingPopup");
const closeTrackingPopup = document.getElementById("closeTrackingPopup");

openTrackingPopup.addEventListener("click", () => {
    trackingPopup.classList.add("show");
});

closeTrackingPopup.addEventListener("click", () => {
    trackingPopup.classList.remove("show");
});

window.addEventListener("click", (e) => {
    if (e.target == trackingPopup) {
        trackingPopup.classList.remove("show");
    }
});

</script>

</body>
</html>
