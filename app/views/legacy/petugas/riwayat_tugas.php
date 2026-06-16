<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}

require_once BASE_PATH . '/config/koneksi.php';

$nama = $_SESSION['nama'] ?? 'Petugas';
$id_ptg_log = $_SESSION['id_petugas'] ?? 0;
$kode_petugas = $_SESSION['kode_petugas'] ?? 'PTG-000';
$divisi_petugas = $_SESSION['divisi'] ?? 'Umum';

$arr_nama = explode(' ', $nama);
$inisial = strtoupper(substr($arr_nama[0], 0, 1) . (isset($arr_nama[1]) ? substr($arr_nama[1], 0, 1) : ''));

// Hanya ambil tugas yang sudah SELESAI milik petugas yang login
$query_riwayat = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori,
                                        (SELECT foto_progres FROM tracking_progress WHERE tracking_progress.id_laporan = laporan.id_laporan ORDER BY id_progress DESC LIMIT 1) AS foto_terakhir,
                                        (SELECT keterangan FROM tracking_progress WHERE tracking_progress.id_laporan = laporan.id_laporan ORDER BY id_progress DESC LIMIT 1) AS catatan_terakhir
                                      FROM laporan
                                      JOIN kategori ON laporan.id_kategori = kategori.id_kategori
                                      WHERE laporan.id_petugas = '$id_ptg_log' AND laporan.status = 'Selesai'
                                      ORDER BY laporan.tanggal_lapor DESC");
$total_selesai = mysqli_num_rows($query_riwayat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Tugas - SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/petugas.css">
    <style>
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 24px; border-radius: 16px; width: 90%; max-width: 560px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); position: relative; animation: animatetop 0.3s ease; }
        @keyframes animatetop { from {top: -100px; opacity: 0} to {top: 0; opacity: 1} }
        .close-btn { position: absolute; right: 20px; top: 15px; color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close-btn:hover { color: #c62828; }
        .modal-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-top: 14px; }
        .modal-group { margin-bottom: 10px; }
        .modal-label { font-weight: 700; color: #64748b; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; display: block; margin-bottom: 4px; }
        .modal-val { font-size: 14px; color: #1e293b; }
        .badge-selesai { display: inline-block; padding: 4px 10px; border-radius: 999px; font-weight: 700; font-size: 12px; background: #dcfce7; color: #166534; }
        .badge-urgensi-tinggi { color: #dc2626; font-weight: 700; }
        .badge-urgensi-sedang { color: #d97706; font-weight: 700; }
        .badge-urgensi-rendah { color: #2563eb; font-weight: 700; }
        .foto-progres-img { width: 100%; max-height: 220px; object-fit: contain; border-radius: 8px; border: 1px solid #e2e8f0; margin-top: 6px; }
        .empty-state { text-align: center; padding: 48px 20px; color: #94a3b8; }
        .empty-state-icon { font-size: 48px; margin-bottom: 12px; }
        .empty-state-title { font-size: 16px; font-weight: 700; color: #64748b; margin-bottom: 6px; }
        .empty-state-sub { font-size: 13px; }
        .selesai-row td { background: #f0fdf4; }
        .catatan-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px; font-size: 13px; color: #334155; line-height: 1.6; margin-top: 4px; }
    </style>
</head>
<body>

<div class="dashboard-layout">

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="sidebar-logo">P</div>
                <div>
                    <div class="sidebar-brand-name">SAPA Petugas</div>
                    <div class="sidebar-brand-sub">Panel Petugas Lapangan</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="petugas.php" class="sidebar-item">Dashboard</a>
                <a href="daftar-tugas.php" class="sidebar-item">Daftar Tugas</a>
                <a href="riwayat_tugas.php" class="sidebar-item active">Riwayat Tugas</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= $inisial; ?></div>
                <div>
                    <div class="sidebar-user-name"><?= htmlspecialchars($nama); ?></div>
                    <div class="sidebar-user-role"><?= htmlspecialchars($kode_petugas); ?></div>
                </div>
            </div>
            <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
        </div>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h1 class="page-title">Riwayat Tugas Saya</h1>
                <p class="page-subtitle">Daftar seluruh tugas yang telah berhasil diselesaikan</p>
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar"><?= $inisial; ?></div>
            </div>
        </div>

        <div class="content-body">

            <div class="stats-row" style="margin-bottom: 20px;">
                <div class="stat-box stat-green">
                    <div class="stat-box-label">Total Tugas Selesai</div>
                    <div class="stat-box-num"><?= $total_selesai; ?></div>
                    <div class="stat-box-change up">Riwayat pekerjaan tuntas</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Riwayat Penanganan Laporan</div>
                        <div class="card-subtitle">Total <?= $total_selesai; ?> tugas berhasil diselesaikan</div>
                    </div>
                </div>

                <?php if ($total_selesai > 0): ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Kode Tiket</th>
                                <th>Judul Laporan</th>
                                <th>Kategori</th>
                                <th>Tgl Lapor</th>
                                <th>Urgensi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = mysqli_fetch_assoc($query_riwayat)): ?>
                            <tr class="selesai-row">
                                <td><span class="id-badge"><?= htmlspecialchars($row['kode_tiket'] ?: $row['id_laporan']); ?></span></td>
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($row['judul_laporan']); ?></div>
                                    <div class="text-small text-muted">📍 <?= htmlspecialchars($row['alamat_kejadian']); ?></div>
                                </td>
                                <td><span style="background:#f1f5f9; padding:4px 8px; border-radius:4px; font-size:12px;"><?= htmlspecialchars($row['nama_kategori']); ?></span></td>
                                <td><?= date('d M Y', strtotime($row['tanggal_lapor'])); ?></td>
                                <td>
                                    <?php
                                    $urgClass = 'badge-urgensi-rendah';
                                    if ($row['urgensi'] === 'Tinggi') $urgClass = 'badge-urgensi-tinggi';
                                    elseif ($row['urgensi'] === 'Sedang') $urgClass = 'badge-urgensi-sedang';
                                    ?>
                                    <span class="<?= $urgClass; ?>"><?= htmlspecialchars($row['urgensi']); ?></span>
                                </td>
                                <td><span class="badge-selesai">✓ Selesai</span></td>
                                <td>
                                    <button class="action-btn" onclick='openModalDetail(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Detail</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <div class="empty-state-icon">📋</div>
                    <div class="empty-state-title">Belum Ada Tugas Selesai</div>
                    <div class="empty-state-sub">Tugas yang telah selesai ditangani akan muncul di sini.</div>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </main>
</div>

<!-- Modal Detail -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 style="font-size: 16px; font-weight: 800; border-bottom: 2px solid #f0f0f5; padding-bottom: 10px; margin-bottom: 4px; color:#1a1a2e;">Detail Laporan Selesai</h3>

        <div class="modal-grid">
            <div class="modal-group">
                <span class="modal-label">Kode Tiket</span>
                <span id="m-tiket" class="modal-val" style="font-weight:700; color:#0f172a;"></span>
            </div>
            <div class="modal-group">
                <span class="modal-label">Status</span>
                <span class="badge-selesai">✓ Selesai</span>
            </div>
            <div class="modal-group">
                <span class="modal-label">Tanggal Lapor</span>
                <span id="m-tanggal" class="modal-val"></span>
            </div>
            <div class="modal-group">
                <span class="modal-label">Urgensi</span>
                <span id="m-urgensi" class="modal-val" style="font-weight:700;"></span>
            </div>
            <div class="modal-group">
                <span class="modal-label">Kategori</span>
                <span id="m-kategori" class="modal-val"></span>
            </div>
            <div class="modal-group">
                <span class="modal-label">Lokasi Kejadian</span>
                <span id="m-alamat" class="modal-val"></span>
            </div>
        </div>

        <div class="modal-group" style="margin-top:12px; border-top:1px solid #f1f5f9; padding-top:12px;">
            <span class="modal-label">Judul Laporan</span>
            <div id="m-judul" class="modal-val" style="font-weight:700; font-size:15px; color:#0f172a;"></div>
        </div>

        <div class="modal-group" style="margin-top:10px;">
            <span class="modal-label">Deskripsi Masalah</span>
            <div id="m-deskripsi" class="catatan-box"></div>
        </div>

        <div class="modal-group" id="catatan-wrap" style="margin-top:10px;">
            <span class="modal-label">Catatan Penanganan Terakhir</span>
            <div id="m-catatan" class="catatan-box"></div>
        </div>

        <div class="modal-group" id="foto-wrap" style="margin-top:12px;">
            <span class="modal-label">Foto Bukti Progres</span>
            <img id="m-foto" src="" alt="Foto Progres" class="foto-progres-img">
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin ingin keluar dari akun petugas?")) { window.location.href = "../logout.php"; }
}

function openModalDetail(data) {
    document.getElementById('m-tiket').innerText   = data.kode_tiket || data.id_laporan;
    document.getElementById('m-tanggal').innerText = data.tanggal_lapor;
    document.getElementById('m-urgensi').innerText = data.urgensi;
    document.getElementById('m-kategori').innerText = data.nama_kategori;
    document.getElementById('m-alamat').innerText  = data.alamat_kejadian;
    document.getElementById('m-judul').innerText   = data.judul_laporan;
    document.getElementById('m-deskripsi').innerText = data.deskripsi || '-';

    const catatanWrap = document.getElementById('catatan-wrap');
    if (data.catatan_terakhir) {
        document.getElementById('m-catatan').innerText = data.catatan_terakhir;
        catatanWrap.style.display = 'block';
    } else {
        catatanWrap.style.display = 'none';
    }

    const fotoWrap = document.getElementById('foto-wrap');
    const fotoEl   = document.getElementById('m-foto');
    if (data.foto_terakhir) {
        fotoEl.src = "../assets/img/laporan/" + data.foto_terakhir;
        fotoWrap.style.display = 'block';
    } else {
        fotoWrap.style.display = 'none';
    }

    document.getElementById('detailModal').style.display = 'block';
}

function closeModal() {
    document.getElementById('detailModal').style.display = 'none';
}

window.onclick = function(event) {
    const modal = document.getElementById('detailModal');
    if (event.target == modal) closeModal();
}
</script>

</body>
</html>