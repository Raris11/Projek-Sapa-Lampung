<?php
session_start();
include '../config/koneksi.php';

$is_logged_in = false;
$nama_user = 'Tamu / Umum';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'masyarakat') {
    $is_logged_in = true;
    $nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Warga';
}

$query_riwayat = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori 
                                      FROM laporan 
                                      JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
                                      ORDER BY laporan.tanggal_lapor DESC");
$total_laporan = mysqli_num_rows($query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan — SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/masyarakat.css">
    <style>
        .sidebar-public { background: linear-gradient(160deg, #c62828 0%, #7b0000 100%) !important; }
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); padding-top: 60px; }
        .modal-content { background-color: #fefefe; margin: 5% auto; padding: 24px; border: 1px solid #cbd5e1; width: 50%; border-radius: 8px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .close-btn { color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close-btn:hover { color: #000; }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-top: 15px; }
        .modal-group { margin-bottom: 10px; }
        .modal-label { font-weight: bold; color: #475569; font-size: 13px; display: block; margin-bottom: 4px; }
        .modal-val { font-size: 14px; color: #1e293b; }
        .badge-status { display: inline-block; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 12px; text-transform: uppercase; }
        .icon-svg { fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; display: block !important; margin: 0 !important; padding: 0 !important; }
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
                <a href="dashboard.php" class="sidebar-item">&ensp;Dashboard</a>
                <a href="buat-laporan.php" class="sidebar-item">&ensp;Buat Laporan</a>
                <a href="riwayat.php" class="sidebar-item active">&ensp;Riwayat Laporan</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar" style="display: flex !important; align-items: center !important; justify-content: center !important; background: rgba(255,255,255,0.1) !important; color: #ffffff !important; padding: 0 !important; margin: 0 !important; width: 40px !important; height: 40px !important; border-radius: 50% !important; box-sizing: border-box !important;">
                    <svg class="icon-svg" style="width: 20px; height: 20px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
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
                <h1 class="page-title">Riwayat Pengaduan Publik</h1>
                <p class="page-subtitle">Daftar seluruh laporan transparansi masyarakat Provinsi Lampung</p>
            </div>
            <div class="topbar-right">
                <?php if (!$is_logged_in): ?>
                    <a href="../login.php" class="btn-primary" style="padding: 8px 16px; font-size: 13px; text-decoration: none;">Masuk Sistem</a>
                <?php else: ?>
                    <div class="topbar-user">
                        <div class="topbar-avatar" style="display: flex !important; align-items: center !important; justify-content: center !important; background: #e2e8f0 !important; color: #64748b !important; border-radius: 50% !important; padding: 0 !important; margin: 0 !important; width: 40px !important; height: 40px !important; box-sizing: border-box !important;">
                            <svg class="icon-svg" style="width: 20px; height: 20px;"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <div>
                        <h3 class="card-title">Semua Laporan Masuk (<?php echo $total_laporan; ?>)</h3>
                        <p class="card-subtitle">Data diperbarui secara real-time langsung dari sistem penanganan petugas</p>
                    </div>
                </div>

                <div style="overflow-x: auto; padding: 0 20px 20px 20px;">
                    <table class="table" style="width: 100%; border-collapse: collapse; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid #e2e8f0; color: #475569; font-size: 14px;">
                                <th style="padding: 12px 8px;">ID Laporan</th>
                                <th style="padding: 12px 8px;">Tanggal</th>
                                <th style="padding: 12px 8px;">Judul Aduan</th>
                                <th style="padding: 12px 8px;">Kategori</th>
                                <th style="padding: 12px 8px;">Urgensi</th>
                                <th style="padding: 12px 8px;">Status</th>
                                <th style="padding: 12px 8px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($total_laporan > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($query_riwayat)): ?>
                                    <tr style="border-bottom: 1px solid #f1f5f9; font-size: 14px; color: #334155;">
                                        <td style="padding: 14px 8px; font-weight: bold; color: #0f172a;"><?php echo htmlspecialchars($row['id_laporan']); ?></td>
                                        <td style="padding: 14px 8px;"><?php echo date('d M Y', strtotime($row['tanggal_lapor'])); ?></td>
                                        <td style="padding: 14px 8px; max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?php echo htmlspecialchars($row['judul_laporan']); ?></td>
                                        <td style="padding: 14px 8px;"><span style="background: #f1f5f9; padding: 4px 8px; border-radius: 4px; font-size: 12px;"><?php echo htmlspecialchars($row['nama_kategori']); ?></span></td>
                                        <td style="padding: 14px 8px;"><span style="color: <?php echo ($row['urgensi'] === 'Tinggi') ? '#dc2626' : (($row['urgensi'] === 'Sedang') ? '#d97706' : '#2563eb'); ?>; font-weight: 600;"> <?php echo htmlspecialchars($row['urgensi']); ?></span></td>
                                        <td style="padding: 14px 8px;">
                                            <?php 
                                            if($row['status'] === 'Diproses') { echo "<span class='badge-status' style='background-color: #fff3e0; color: #e65100;'>Diproses</span>"; }
                                            elseif ($row['status'] === 'Selesai') { echo "<span class='badge-status' style='background-color: #e8f5e9; color: #1b5e20;'>Selesai</span>"; }
                                            else { echo "<span class='badge-status' style='background-color: #fce4ec; color: #880e4f;'>Pending</span>"; }
                                            ?>
                                        </td>
                                        <td style="padding: 14px 8px; text-align: center;"><button class="btn-primary" style="padding: 6px 12px; font-size: 12px; cursor: pointer;" onclick='openModalDetail(<?php echo json_encode($row); ?>)'>Lihat Detail</button></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">Belum ada data pengaduan masuk.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModalDetail()">&times;</span>
        <h2 style="margin-top: 0; color: #0f172a; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Detail Data Pengaduan</h2>
        <div class="modal-grid">
            <div class="modal-group"><span class="modal-label">ID LAPORAN</span><span id="modal-id" class="modal-val" style="font-weight: bold; color: #000;"></span></div>
            <div class="modal-group"><span class="modal-label">STATUS SEKARANG</span><span id="modal-status" class="badge-status"></span></div>
            <div class="modal-group"><span class="modal-label">TANGGAL KEJADIAN / LAPOR</span><span id="modal-tanggal" class="modal-val"></span></div>
            <div class="modal-group"><span class="modal-label">TINGKAT URGENSI</span><span id="modal-urgensi" class="modal-val" style="font-weight: bold;"></span></div>
            <div class="modal-group"><span class="modal-label">KATEGORI MASALAH</span><span id="modal-kategori" class="modal-val"></span></div>
            <div class="modal-group"><span class="modal-label">ALAMAT LOKASI KEJADIAN</span><span id="modal-alamat" class="modal-val"></span></div>
        </div>
        <div class="modal-group" style="margin-top: 15px; border-top: 1px solid #f1f5f9; padding-top: 15px;"><span class="modal-label">JUDUL LAPORAN KELUHAN</span><span id="modal-judul" class="modal-val" style="font-weight: 600; font-size: 15px; color: #0f172a;"></span></div>
        <div class="modal-group" style="margin-top: 10px;"><span class="modal-label">DESKRIPSI KRONOLOGI MASALAH</span><p id="modal-deskripsi" class="modal-val" style="background: #f8fafc; padding: 12px; border-radius: 6px; margin: 5px 0 0 0; line-height: 1.6; border: 1px solid #e2e8f0; text-align: justify;"></p></div>
        <div class="modal-group" style="margin-top: 15px;"><span class="modal-label">FOTO BUKTI PENDUKUNG</span><img id="modal-img" src="" alt="Foto Bukti" style="width: 100%; max-height: 250px; object-fit: contain; border-radius: 6px; border: 1px solid #cbd5e1; margin-top: 5px;"></div>
    </div>
</div>

<script>
function openModalDetail(data) {
    document.getElementById('modal-id').innerText = data.id_laporan;
    document.getElementById('modal-tanggal').innerText = data.tanggal_lapor;
    document.getElementById('modal-judul').innerText = data.judul_laporan;
    document.getElementById('modal-kategori').innerText = data.nama_kategori;
    document.getElementById('modal-urgensi').innerText = data.urgensi;
    document.getElementById('modal-alamat').innerText = data.alamat_kejadian;
    document.getElementById('modal-deskripsi').innerText = data.deskripsi;
    if(data.foto_bukti) { document.getElementById('modal-img').src = "../assets/img/laporan/" + data.foto_bukti; document.getElementById('modal-img').style.display = "block"; }
    else { document.getElementById('modal-img').style.display = "none"; }
    const statusLabel = document.getElementById('modal-status');
    statusLabel.innerText = data.status;
    if(data.status === 'Diproses') { statusLabel.style.backgroundColor = '#fff3e0'; statusLabel.style.color = '#e65100'; }
    else if (data.status === 'Selesai') { statusLabel.style.backgroundColor = '#e8f5e9'; statusLabel.style.color = '#1b5e20'; }
    else { statusLabel.style.backgroundColor = '#fce4ec'; statusLabel.style.color = '#880e4f'; }
    document.getElementById('detailModal').style.display = "block";
}
function closeModalDetail() { document.getElementById('detailModal').style.display = "none"; }
window.onclick = function(event) { if (event.target == document.getElementById('detailModal')) { closeModalDetail(); } }
function confirmLogout() { if (confirm("Yakin mau keluar dari akun?")) { window.location.href = "../logout.php"; } }
</script>
</body>
</html>