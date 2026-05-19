<?php
session_start();
include '../config/koneksi.php';

$is_logged_in = false;
$nama_user = 'Tamu / Umum';
if (isset($_SESSION['role']) && $_SESSION['role'] === 'masyarakat') {
    $is_logged_in = true;
    $nama_user = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Warga';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keuntungan Akun — SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/masyarakat.css">
    <style>
        .sidebar-public {
            background: linear-gradient(160deg, #c62828 0%, #7b0000 100%) !important;
        }
        .benefit-badge {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 8px;
        }
        .table-compare {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        .table-compare th, .table-compare td {
            padding: 16px;
            border-bottom: 1px solid #e2e8f0;
            text-align: left;
            font-size: 14px;
            vertical-align: top;
        }
        .table-compare th {
            background-color: #f8fafc;
            color: #334155;
            font-weight: 700;
        }
        .check-yes {
            color: #16a34a;
            font-weight: bold;
        }
        .check-no {
            color: #dc2626;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="dashboard-layout">
    
    <aside class="sidebar <?php echo (!$is_logged_in) ? 'sidebar-public' : ''; ?>">
        <div>
            <div class="sidebar-brand">
                <div class="sidebar-logo">S</div>
                <div>
                    <div class="sidebar-brand-name">SAPA</div>
                    <div class="sidebar-brand-sub">Portal Masyarakat</div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item">&ensp;Dashboard</a>
                <a href="buat-laporan.php" class="sidebar-item">&ensp;Buat Laporan</a>
                <a href="riwayat.php" class="sidebar-item">&ensp;Riwayat Laporan</a>
                <a href="user-login.php" class="sidebar-item active">&ensp;Keuntungan Akun</a>
            </nav>
        </div>
        
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?php echo $is_logged_in ? '' : '?'; ?></div>
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
                <h1 class="page-title">Keuntungan Memiliki Akun</h1>
                <p class="page-subtitle">Bandingkan fasilitas pelaporan antara akun terverifikasi dan akses tamu anonim</p>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <div>
                        <span class="benefit-badge">Fitur Eksklusif</span>
                        <h3 class="card-title">Tabel Perbandingan Akses</h3>
                        <p class="card-subtitle">Kenapa Anda disarankan untuk melakukan registrasi dan masuk sistem</p>
                    </div>
                </div>

                <div style="padding: 0 20px 20px 20px; overflow-x: auto;">
                    <table class="table-compare">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Fitur & Layanan</th>
                                <th style="width: 35%;">Akses Tamu (Anonim)</th>
                                <th style="width: 35%;">Akses Akun (Login)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><strong>Pemantauan Progres</strong></td>
                                <td>Manual. Harus mencatat ID Laporan dan mencarinya satu per satu lewat dashboard pencarian.</td>
                                <td><span class="check-yes">Otomatis.</span> Semua status pengaduan langsung muncul di feed dashboard tanpa input kode.</td>
                            </tr>
                            <tr>
                                <td><strong>Penyimpanan Histori</strong></td>
                                <td><span class="check-no">Tidak Ada.</span> Riwayat akan hilang setelah browser ditutup atau cache dibersihkan.</td>
                                <td><span class="check-yes">Permanen.</span> Seluruh arsip aduan lama tersimpan raki dalam database akun Anda.</td>
                            </tr>
                            <tr>
                                <td><strong>Komunikasi dengan Petugas</strong></td>
                                <td>Satu arah. Hanya menerima pembaruan status sistem tanpa opsi umpan balik.</td>
                                <td><span class="check-yes">Dua arah.</span> Bisa memberikan tanggapan balik jika petugas meminta kejelasan detail keluhan.</td>
                            </tr>
                            <tr>
                                <td><strong>Unggah Bukti Tambahan</strong></td>
                                <td>Hanya pada form pembuatan laporan pertama kali di awal.</td>
                                <td><span class="check-yes">Fleksibel.</span> Dapat melampirkan foto perkembangan kondisi terbaru di menu detail.</td>
                            </tr>
                            <tr>
                                <td><strong>Sistem Poin Apresiasi</strong></td>
                                <td><span class="check-no">Tidak Berhak.</span> Kontribusi tidak tercatat oleh sistem daerah.</td>
                                <td><span class="check-yes">Berhak.</span> Mendapatkan poin kontribusi setiap laporan berhasil dituntaskan petugas.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <?php if (!$is_logged_in): ?>
                <div style="margin-top: 24px; text-align: center; background: #ffffff; padding: 30px; border-radius: 8px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);">
                    <h3 style="margin: 0 0 8px 0; color: #0f172a;">Belum Memiliki Akun SAPA Lampung?</h3>
                    <p style="margin: 0 0 20px 0; color: #64748b; font-size: 14px; max-width: 600px; margin-left: auto; margin-right: auto;">
                        Dapatkan kemudahan memantau pembangunan dan perbaikan infrastruktur di sekitar wilayah Anda secara real-time dan terstruktur.
                    </p>
                    <a href="../login.php" class="btn-primary" style="text-decoration: none; display: inline-block; padding: 10px 24px;">Daftar / Masuk Sekarang</a>
                </div>
            <?php endif; ?>
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