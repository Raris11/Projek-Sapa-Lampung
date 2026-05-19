<?php
session_start();
include '../config/koneksi.php';

$is_logged_in = false;
$nama_user = 'Tamu / Umum';
$id_user_logged = null;

if (isset($_SESSION['role']) && $_SESSION['role'] === 'masyarakat') {
    $is_logged_in = true;
    $nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Warga';
    $id_user_logged = $_SESSION['id_user'];
}

if ($is_logged_in) {
    $laporan_total   = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_pelapor = '$id_user_logged'"));
    $laporan_pending = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_pelapor = '$id_user_logged' AND status = 'Pending'"));
    $laporan_proses  = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_pelapor = '$id_user_logged' AND status = 'Proses'"));
    $laporan_selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_pelapor = '$id_user_logged' AND status = 'Selesai'"));
    
    $query_laporan_aktif = mysqli_query($conn, "SELECT * FROM laporan WHERE id_pelapor = '$id_user_logged' AND status != 'Selesai' ORDER BY id_laporan DESC");
} else {
    $laporan_total   = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan"));
    $laporan_pending = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE status = 'Pending'"));
    $laporan_proses  = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE status = 'Proses'"));
    $laporan_selesai = mysqli_num_rows(mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE status = 'Selesai'"));
}

$search_result = null;
$search_triggered = false;
$search_error = "";

if (isset($_POST['lacak_id'])) {
    $search_triggered = true;
    $id_laporan_cari = mysqli_real_escape_string($conn, $_POST['id_laporan_cari']);
    
    if (!empty($id_laporan_cari)) {
        $query_cari = mysqli_query($conn, "SELECT l.*, k.nama_kategori FROM laporan l 
                                           LEFT JOIN kategori k ON l.id_kategori = k.id_kategori 
                                           WHERE l.id_laporan = '$id_laporan_cari'");
        if (mysqli_num_rows($query_cari) > 0) {
            $search_result = mysqli_fetch_assoc($query_cari);
        } else {
            $search_error = "ID Laporan tidak ditemukan. Silakan periksa kembali kode yang Anda masukkan.";
        }
    } else {
        $search_error = "Silakan masukkan ID Laporan terlebih dahulu.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/masyarakat.css">
    <style>
        .sidebar-public { background: linear-gradient(160deg, #c62828 0%, #7b0000 100%) !important; }
        .search-card { background: #ffffff; border-radius: 8px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); margin-bottom: 24px; border: 1px solid #e2e8f0; }
        .search-box-group { display: flex; gap: 12px; margin-top: 15px; }
        .search-input { flex: 1; padding: 12px 16px; border: 2px solid #cbd5e1; border-radius: 6px; font-size: 15px; outline: none; }
        .search-input:focus { border-color: #c62828; }
        .btn-search { background-color: #c62828; color: #ffffff; border: none; padding: 0 24px; border-radius: 6px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .result-box { margin-top: 20px; padding: 18px; border-radius: 6px; background: #f8fafc; border-left: 4px solid #3b82f6; }
        .result-box.error { background: #fef2f2; border-left-color: #ef4444; color: #991b1b; font-size: 14px; }
        .result-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-top: 10px; font-size: 14px; }
        .result-label { color: #64748b; font-weight: 500; }
        .result-value { color: #1e293b; font-weight: 600; }
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .status-badge.pending { background: #fef3c7; color: #d97706; }
        .status-badge.proses { background: #dbeafe; color: #2563eb; }
        .status-badge.selesai { background: #dcfce7; color: #16a34a; }
        .icon-svg { fill: currentColor; display: block !important; margin: 0 !important; padding: 0 !important; }
    </style>
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar <?php echo (!$is_logged_in) ? 'sidebar-public' : ''; ?>">
        <div>
            <div class="sidebar-brand">
                <div class="sidebar-logo" style="background: transparent; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; padding: 0; margin: 0;">
                    <img src="../assets/img/logo-sapa.jpeg" alt="Logo SAPA" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                </div>
                <div>
                    <div class="sidebar-brand-name">SAPA</div>
                    <div class="sidebar-brand-sub">Portal Masyarakat</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item active">&ensp;Dashboard</a>
                <a href="buat-laporan.php" class="sidebar-item">&ensp;Buat Laporan</a>
                <a href="riwayat.php" class="sidebar-item">&ensp;Riwayat Laporan</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar" style="display: flex !important; align-items: center !important; justify-content: center !important; background: rgba(255,255,255,0.1) !important; color: #ffffff !important; padding: 0 !important; margin: 0 !important; width: 40px !important; height: 40px !important; border-radius: 50% !important; box-sizing: border-box !important;">
                    <svg class="icon-svg" viewBox="0 0 24 24" style="width: 20px; height: 20px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </div>
                <div>
                    <div class="sidebar-user-name"><?php echo htmlspecialchars($nama_user); ?></div>
                    <div class="sidebar-user-role">Masyarakat</div>
                </div>
            </div>
            <?php if ($is_logged_in): ?>
                <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
            <?php else: ?>
                <a href="../index.php" class="sidebar-logout">Kembali ke Beranda</a>
            <?php endif; ?>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h2 class="page-title">Dashboard</h2>
                <p class="page-subtitle">Selamat datang kembali, <strong><?php echo htmlspecialchars($nama_user); ?></strong></p>
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar" style="display: flex !important; align-items: center !important; justify-content: center !important; background: #e2e8f0 !important; color: #64748b !important; border-radius: 50% !important; padding: 0 !important; margin: 0 !important; width: 40px !important; height: 40px !important; box-sizing: border-box !important;">
                    <svg class="icon-svg" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </div>
            </div>
        </div>

        <div class="content-body">
            <?php if (!$is_logged_in): ?>
                <div class="search-card">
                    <h3 style="margin: 0; color: #1e293b;">Lacak Status Pengaduan Instan</h3>
                    <p style="margin: 5px 0 0 0; font-size: 14px; color: #64748b;">Sebagai pengguna umum/tamu, Anda dapat memantau perkembangan laporan langsung menggunakan kode ID Laporan di bawah ini.</p>
                    
                    <form action="dashboard.php" method="POST" class="search-box-group">
                        <input type="text" name="id_laporan_cari" class="search-input" placeholder="Masukkan ID Laporan Anda (Contoh: RPT-2026-0001)" required value="<?php echo isset($_POST['id_laporan_cari']) ? htmlspecialchars($_POST['id_laporan_cari']) : ''; ?>">
                        <button type="submit" name="lacak_id" class="btn-search">Cari Laporan</button>
                    </form>

                    <?php if ($search_triggered): ?>
                        <?php if ($search_result): ?>
                            <div class="result-box">
                                <h4 style="margin: 0 0 8px 0; color: #1e293b;">Hasil Pelacakan Laporan:</h4>
                                <div class="result-grid">
                                    <div><span class="result-label">ID Laporan:</span> <span class="result-value"><?php echo htmlspecialchars($search_result['id_laporan']); ?></span></div>
                                    <div><span class="result-label">Status:</span> <span class="status-badge <?php echo strtolower($search_result['status']); ?>"><?php echo htmlspecialchars($search_result['status']); ?></span></div>
                                    <div style="grid-column: span 2;"><span class="result-label">Judul Keluhan:</span> <span class="result-value"><?php echo htmlspecialchars($search_result['judul_laporan']); ?></span></div>
                                    <div><span class="result-label">Kategori:</span> <span class="result-value"><?php echo htmlspecialchars($search_result['nama_kategori']); ?></span></div>
                                    <div><span class="result-label">Urgensi:</span> <span class="result-value"><?php echo htmlspecialchars($search_result['urgensi']); ?></span></div>
                                    <div style="grid-column: span 2;"><span class="result-label">Lokasi Kejadian:</span> <span class="result-value"><?php echo htmlspecialchars($search_result['alamat_kejadian']); ?></span></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="result-box error"><strong>Pencarian Gagal:</strong> <?php echo htmlspecialchars($search_error); ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <h3 style="margin: 20px 0 12px 0; color: #1e293b;"><?php echo $is_logged_in ? "Ringkasan Laporan Anda" : "Statistik Pengaduan Masyarakat Provinsi Lampung"; ?></h3>
            
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-label">Total Laporan</div><div class="stat-number"><?php echo $laporan_total; ?></div><div class="stat-info">Aduan masuk sistem</div></div>
                <div class="stat-card"><div class="stat-label">Menunggu Verifikasi</div><div class="stat-number" style="color: #d97706;"><?php echo $laporan_pending; ?></div><div class="stat-info">Status pending admin</div></div>
                <div class="stat-card"><div class="stat-label">Sedang Diproses</div><div class="stat-number" style="color: #2563eb;"><?php echo $laporan_proses; ?></div><div class="stat-info">Penanganan oleh petugas</div></div>
                <div class="stat-card"><div class="stat-label">Laporan Selesai</div><div class="stat-number" style="color: #16a34a;"><?php echo $laporan_selesai; ?></div><div class="stat-info">Telah dituntaskan</div></div>
            </div>

            <?php if ($is_logged_in): ?>
                <div class="card" style="margin-top: 24px;">
                    <div class="card-header"><div><h3 class="card-title">Daftar Laporan Aktif Anda</h3><p class="card-subtitle">Memantau aduan yang sedang berjalan</p></div></div>
                    <?php if (mysqli_num_rows($query_laporan_aktif) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($query_laporan_aktif)): ?>
                            <div class="laporan-item">
                                <div class="laporan-top">
                                    <div><div class="laporan-title"><?php echo htmlspecialchars($row['judul_laporan']); ?></div><div class="laporan-id"><?php echo htmlspecialchars($row['id_laporan']); ?></div></div>
                                    <span class="status <?php echo strtolower($row['status']); ?>"><?php echo htmlspecialchars($row['status']); ?></span>
                                </div>
                                <div class="laporan-lokasi"><?php echo htmlspecialchars($row['alamat_kejadian']); ?></div>
                                <div class="progress"><div class="progress-bar" style="width: <?php echo ($row['status'] == 'Pending') ? '25%' : '75%'; ?>;"></div></div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p style="padding: 20px; text-align: center; color: #64748b; font-size: 14px;">Anda belum memiliki data laporan aktif yang sedang diproses.</p>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <div style="margin-top: 24px; text-align: center; background: #ffffff; padding: 24px; border-radius: 8px; border: 1px dashed #cbd5e1;">
                    <p style="margin: 0 0 15px 0; color: #64748b; font-size: 14px;">Apakah Anda menemukan infrastruktur rusak atau kendala fasilitas umum di sekitar Anda?</p>
                    <a href="buat-laporan.php" class="btn-primary" style="text-decoration: none; display: inline-block;">Kirim Pengaduan Baru Sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin mau keluar dari akun?")) { window.location.href = "../logout.php"; }
}
</script>
</body>
</html>