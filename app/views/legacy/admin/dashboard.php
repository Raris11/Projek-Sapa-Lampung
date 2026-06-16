<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once BASE_PATH . '/config/koneksi.php'; 
$nama = $_SESSION['nama'];

$q_total    = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan");
$total_lap  = mysqli_fetch_assoc($q_total)['total'];

$q_proses   = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Diproses'");
$total_pros = mysqli_fetch_assoc($q_proses)['total'];

$q_selesai  = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Selesai'");
$total_sel  = mysqli_fetch_assoc($q_selesai)['total'];

$q_pending  = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Menunggu Verifikasi'");
$total_pend = mysqli_fetch_assoc($q_pending)['total'];

$persen_resolved = ($total_lap > 0) ? round(($total_sel / $total_lap) * 100, 1) : 0;

// === STATISTIK KATEGORI ===
$query_kat = mysqli_query($conn, "SELECT k.nama_kategori, COUNT(l.id_laporan) as total
                                   FROM kategori k
                                   LEFT JOIN laporan l ON k.id_kategori = l.id_kategori
                                   GROUP BY k.id_kategori, k.nama_kategori
                                   ORDER BY total DESC");
$kategori_data = [];
$max_kat = 1;
while ($row = mysqli_fetch_assoc($query_kat)) {
    $kategori_data[] = $row;
    if ($row['total'] > $max_kat) $max_kat = $row['total'];
}

// === STATISTIK STATUS ===
$total_ditolak = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM laporan WHERE status = 'Ditolak'"))[0];

$query_laporan = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori 
                                      FROM laporan 
                                      JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
                                      ORDER BY laporan.tanggal_lapor DESC LIMIT 3");

$query_petugas = mysqli_query($conn, "SELECT users.nama, petugas.divisi, petugas.id_petugas,
                                      (SELECT COUNT(*) FROM laporan WHERE laporan.id_petugas = petugas.id_petugas AND laporan.status != 'Selesai' AND laporan.status != 'Ditolak') as beban_tugas
                                      FROM petugas
                                      JOIN users ON petugas.id_user = users.id_user 
                                      LIMIT 3");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background-color: #ffffff;
            margin: 4vh auto;
            padding: 28px;
            border-radius: 16px;
            width: 90%;
            max-width: 850px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            box-sizing: border-box;
            position: relative;
        }
        .close-btn {
            color: #94a3b8;
            position: absolute;
            right: 24px;
            top: 24px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            line-height: 1;
        }
        .close-btn:hover {
            color: #334155;
        }
        .modal-title-text {
            margin: 0 0 20px 0;
            color: #0f172a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 16px;
            font-size: 22px;
            font-weight: 700;
        }
        .modal-body-layout {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 28px;
            align-items: start;
        }
        .info-group {
            margin-bottom: 14px;
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 10px;
        }
        .info-group:last-of-type {
            border-bottom: none;
        }
        .modal-label {
            font-size: 11px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            display: block;
            margin-bottom: 4px;
            letter-spacing: 0.5px;
        }
        .modal-val {
            font-size: 15px;
            color: #0f172a;
            font-weight: 600;
        }
        .modal-image-preview {
            width: 100%;
            max-height: 200px;
            object-fit: contain;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-top: 6px;
            background-color: #f8fafc;
        }
        .action-box-admin {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }
        .action-box-title {
            font-size: 15px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 16px 0;
        }
        .form-select-group {
            margin-bottom: 16px;
        }
        .form-select-label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 8px;
        }
        .modal-select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            color: #334155;
            background-color: #ffffff;
            outline: none;
        }
        .modal-select:focus {
            border-color: #2563eb;
        }
        .btn-submit-change {
            width: 100%;
            background-color: #2563eb;
            color: #ffffff;
            border: none;
            padding: 12px;
            font-size: 14px;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.2);
            transition: background 0.2s;
        }
        .btn-submit-change:hover {
            background-color: #1d4ed8;
        }
        .sidebar-avatar-reset {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            margin: 0 !important;
            line-height: 0 !important;
        }
    </style>
</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="sidebar-logo">A</div>
                <div>
                    <div class="sidebar-brand-name">SAPA Admin</div>
                    <div class="sidebar-brand-sub">Panel Administrator</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item active">Dashboard</a>
                <a href="laporan.php" class="sidebar-item">Laporan Masuk</a>
                <a href="tracking.php" class="sidebar-item">Tracking</a>
                <!-- <a href="statistik.php" class="sidebar-item">Statistik</a> -->
                <a href="petugas.php" class="sidebar-item">Manajemen Petugas</a>
                <a href="akun.php" class="sidebar-item">Kelola Akun</a>
            </nav>
        </div>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar sidebar-avatar-reset">
                    <svg viewBox="0 0 24 24" style="width: 20px; height: 20px; fill: currentColor;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                </div>
                <div>
                    <div class="sidebar-user-name"><?= htmlspecialchars($nama); ?></div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </div>
            <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Dashboard Admin</h1>
                <p class="page-subtitle">Selamat datang kembali, <?= htmlspecialchars($nama); ?></p>
            </div>
        </div>

        <!-- yg di ubah -->

        <!-- yg di ubah -->

        <div class="content-body">
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-box-label">Total Laporan</div>
                    <div class="stat-box-num"><?= number_format($total_lap); ?></div>
                    <div class="stat-box-change up">Akumulasi sistem</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Sedang Diproses</div>
                    <div class="stat-box-num"><?= number_format($total_pros); ?></div>
                    <div class="stat-box-change">+ Tugas lapangan</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Laporan Selesai</div>
                    <div class="stat-box-num"><?= number_format($total_sel); ?></div>
                    <div class="stat-box-change up"><?= $persen_resolved; ?>% resolved</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Menunggu Verifikasi</div>
                    <div class="stat-box-num"><?= number_format($total_pend); ?></div>
                    <div class="stat-box-change down">Perlu tindakan</div>
                </div>
            </div>

            <div class="grid-2">
                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Laporan Terbaru</div>
                            <div class="card-subtitle">Perlu tindakan segera</div>
                        </div>
                        <a href="laporan.php" class="btn btn-outline" style="text-decoration:none;">Lihat Semua</a>
                    </div>

                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID Laporan</th>
                                    <th>Kategori</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($query_laporan) > 0) : ?>
                                    <?php while($lap = mysqli_fetch_assoc($query_laporan)) : ?>
                                    <tr>
                                        <td><span class="id-badge"><?= htmlspecialchars($lap['kode_tiket'] ?: $lap['id_laporan']); ?></span></td>
                                        <td>
                                            <span class="cat-tag cat-<?= strtolower($lap['nama_kategori'] == 'Fasilitas Umum' ? 'fasilitas' : $lap['nama_kategori']); ?>">
                                                <?= $lap['nama_kategori']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="pill pill-<?= strtolower($lap['status'] == 'Diverifikasi' || $lap['status'] == 'Ditugaskan' ? 'verifikasi' : ($lap['status'] == 'Diproses' ? 'diproses' : ($lap['status'] == 'Selesai' ? 'selesai' : 'pending'))); ?>">
                                                <?= $lap['status']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="action-btn" style="border:none; background:none; cursor:pointer; font-family:inherit;" onclick='openModalDetail(<?= json_encode($lap); ?>)'>Detail</button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else : ?>
                                    <tr><td colspan="4" style="text-align:center; padding:15px; color:#8a8a9a;">Belum ada data masuk.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div>
                            <div class="card-title">Petugas Aktif</div>
                            <div class="card-subtitle">Status beban tugas saat ini</div>
                        </div>
                        <a href="petugas.php" class="btn btn-outline" style="text-decoration:none;">Kelola</a>
                    </div>

                    <div class="petugas-list">
                        <?php while($ptg = mysqli_fetch_assoc($query_petugas)) : 
                            $words = explode(" ", $ptg['nama']);
                            $inisial = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                            $color_class = ($ptg['divisi'] == 'Infrastruktur') ? 'green' : (($ptg['divisi'] == 'Kebersihan') ? 'amber' : 'blue');
                        ?>
                        <div class="petugas-item">
                            <div class="petugas-left">
                                <div class="mini-avatar <?= $color_class; ?>"><?= $inisial; ?></div>
                                <div>
                                    <div class="petugas-name"><?= htmlspecialchars($ptg['nama']); ?></div>
                                    <div class="petugas-role"><?= $ptg['divisi']; ?></div>
                                </div>
                            </div>
                            <span class="pill pill-<?= ($ptg['beban_tugas'] > 0) ? 'diproses' : 'selesai'; ?>">
                                <?= $ptg['beban_tugas']; ?> Tugas
                            </span>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <!-- ===== SECTION STATISTIK ===== -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px; margin-top:24px;">

                <!-- Bar Chart Kategori -->
                <div style="background:#fff; border-radius:16px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,.06);">
                    <div style="margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #f0f0f5;">
                        <div style="font-size:17px; font-weight:800; color:#1a1a2e; margin:0 0 4px;">Laporan per Kategori</div>
                        <div style="font-size:13px; color:#8a8a9a;">Total laporan berdasarkan kategori</div>
                    </div>
                    <div style="display:flex; align-items:flex-end; gap:16px; height:180px; padding-bottom:8px;">
                        <?php
                        $kat_colors = ['#2563eb','#7c3aed','#16a34a','#ea580c','#dc2626','#0891b2'];
                        foreach ($kategori_data as $i => $kat):
                            $height = $max_kat > 0 ? max(round(($kat['total'] / $max_kat) * 100), 4) : 4;
                            $color  = $kat_colors[$i % count($kat_colors)];
                        ?>
                        <div style="display:flex; flex-direction:column; align-items:center; flex:1; height:100%; justify-content:flex-end; gap:6px;">
                            <span style="font-size:12px; font-weight:700; color:#1a1a2e;"><?= $kat['total'] ?></span>
                            <div style="width:100%; background:<?= $color ?>; height:<?= $height ?>%; border-radius:6px 6px 0 0; transition:height .3s;"></div>
                            <span style="font-size:11px; color:#8a8a9a; text-align:center; line-height:1.3;"><?= htmlspecialchars($kat['nama_kategori']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if (empty($kategori_data)): ?>
                            <p style="color:#8a8a9a; font-size:14px; text-align:center; width:100%;">Belum ada data.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Donut / Legend Status -->
                <div style="background:#fff; border-radius:16px; padding:24px; box-shadow:0 2px 12px rgba(0,0,0,.06);">
                    <div style="margin-bottom:20px; padding-bottom:14px; border-bottom:1px solid #f0f0f5;">
                        <div style="font-size:17px; font-weight:800; color:#1a1a2e; margin:0 0 4px;">Status Laporan</div>
                        <div style="font-size:13px; color:#8a8a9a;">Total <?= $total_lap ?> laporan</div>
                    </div>
                    <div style="display:flex; align-items:center; gap:32px;">
                        <!-- Lingkaran tengah -->
                        <div style="position:relative; width:130px; height:130px; flex-shrink:0;">
                            <svg viewBox="0 0 36 36" style="width:130px; height:130px; transform:rotate(-90deg);">
                                <!-- track -->
                                <circle cx="18" cy="18" r="15.9" fill="none" stroke="#f0f0f5" stroke-width="3.5"/>
                                <?php
                                $statuses = [
                                    ['val'=>$total_sel,    'color'=>'#16a34a'],
                                    ['val'=>$total_pros,   'color'=>'#ea580c'],
                                    ['val'=>$total_pend,   'color'=>'#2563eb'],
                                    ['val'=>$total_ditolak,'color'=>'#dc2626'],
                                ];
                                $circ = 2 * M_PI * 15.9; // ~99.9
                                $offset = 0;
                                foreach ($statuses as $s):
                                    $pct = $total_lap > 0 ? ($s['val'] / $total_lap) : 0;
                                    $dash = $pct * $circ;
                                    $gap  = $circ - $dash;
                                ?>
                                <circle cx="18" cy="18" r="15.9" fill="none"
                                    stroke="<?= $s['color'] ?>" stroke-width="3.5"
                                    stroke-dasharray="<?= round($dash,2) ?> <?= round($gap,2) ?>"
                                    stroke-dashoffset="-<?= round($offset,2) ?>"/>
                                <?php $offset += $dash; endforeach; ?>
                            </svg>
                            <div style="position:absolute; inset:0; display:flex; flex-direction:column; align-items:center; justify-content:center;">
                                <span style="font-size:22px; font-weight:800; color:#1a1a2e;"><?= $persen_resolved ?>%</span>
                                <span style="font-size:11px; color:#8a8a9a;">Selesai</span>
                            </div>
                        </div>
                        <!-- Legend -->
                        <div style="display:flex; flex-direction:column; gap:14px; flex:1;">
                            <?php
                            $legend = [
                                ['label'=>'Selesai',  'val'=>$total_sel,     'color'=>'#16a34a'],
                                ['label'=>'Diproses', 'val'=>$total_pros,    'color'=>'#ea580c'],
                                ['label'=>'Menunggu', 'val'=>$total_pend,    'color'=>'#2563eb'],
                                ['label'=>'Ditolak',  'val'=>$total_ditolak, 'color'=>'#dc2626'],
                            ];
                            foreach ($legend as $l): ?>
                            <div style="display:flex; align-items:center; gap:10px;">
                                <div style="width:12px; height:12px; border-radius:50%; background:<?= $l['color'] ?>; flex-shrink:0;"></div>
                                <span style="font-size:13px; color:#64748b; flex:1;"><?= $l['label'] ?></span>
                                <strong style="font-size:14px; color:#1a1a2e;"><?= $l['val'] ?></strong>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>
</div>

<div id="adminDetailModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModalDetail()">&times;</span>
        <h2 class="modal-title-text">Detail & Penanganan Pengaduan</h2>
        
        <form action="laporan.php" method="POST">
            <input type="hidden" name="id_laporan" id="form-id-laporan">
            
            <div class="modal-body-layout">
                
                <div class="modal-left-info">
                    <div class="info-group">
                        <span class="modal-label">ID Laporan</span>
                        <span id="m-id" class="modal-val" style="font-weight: 700;"></span>
                    </div>
                    <div class="info-group">
                        <span class="modal-label">Kategori Bidang</span>
                        <span id="m-kategori" class="modal-val" style="color: #2563eb;"></span>
                    </div>
                    <div class="info-group">
                        <span class="modal-label">Judul Masalah</span>
                        <span id="m-judul" class="modal-val"></span>
                    </div>
                    <div class="info-group">
                        <span class="modal-label">Alamat Lokasi</span>
                        <span id="m-alamat" class="modal-val"></span>
                    </div>
                    <div class="info-group">
                        <span class="modal-label">Deskripsi Keluhan</span>
                        <span id="m-deskripsi" class="modal-val" style="display:block; font-weight: 500; font-size: 14px; margin-top: 2px;"></span>
                    </div>
                    <div class="info-group">
                        <span class="modal-label">Foto Bukti Lampiran</span>
                        <img id="m-img" src="" alt="Foto Bukti" class="modal-image-preview">
                    </div>
                </div>

                <div class="modal-right-action">
                    <div class="action-box-admin">
                        <h3 class="action-box-title">Aksi Administrator</h3>
                        
                        <div class="form-select-group">
                            <label class="form-select-label">Set Status Progres</label>
                            <select name="status" id="form-status" class="modal-select">
                                <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                <option value="Diverifikasi">Diverifikasi</option>
                                <option value="Ditugaskan">Ditugaskan</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>

                        <div class="form-select-group">
                            <label class="form-select-label">Tunjuk Petugas Lapangan</label>
                            <select name="id_petugas" id="form-petugas" class="modal-select">
                                <option value="">-- Memuat Petugas... --</option>
                            </select>
                        </div>

                        <button type="submit" name="update_laporan" class="btn-submit-change">Simpan Perubahan</button>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>

<script>
function openModalDetail(data) {
    document.getElementById('form-id-laporan').value = data.id_laporan;
    document.getElementById('m-id').innerText = data.kode_tiket || data.id_laporan;
    document.getElementById('m-kategori').innerText = data.nama_kategori;
    document.getElementById('m-judul').innerText = data.judul_laporan;
    document.getElementById('m-alamat').innerText = data.alamat_kejadian;
    document.getElementById('m-deskripsi').innerText = data.deskripsi;
    
    if (data.foto_bukti) {
        document.getElementById('m-img').src = "../assets/img/laporan/" + data.foto_bukti;
        document.getElementById('m-img').style.display = "block";
    } else {
        document.getElementById('m-img').style.display = "none";
    }

    document.getElementById('form-status').value = data.status;

    const selectPetugas = document.getElementById('form-petugas');
    selectPetugas.innerHTML = '<option value="">-- Memuat Petugas... --</option>';

    fetch('ambil_petugas.php?kategori=' + encodeURIComponent(data.nama_kategori))
        .then(response => response.json())
        .then(petugasList => {
            selectPetugas.innerHTML = '<option value="">-- Pilih Petugas ' + data.nama_kategori + ' --</option>';
            petugasList.forEach(petugas => {
                const option = document.createElement('option');
                option.value = petugas.id_petugas;
                option.text = petugas.nama;
                if(data.id_petugas && data.id_petugas == petugas.id_petugas) {
                    option.selected = true;
                }
                selectPetugas.appendChild(option);
            });
        })
        .catch(err => {
            selectPetugas.innerHTML = '<option value="">Gagal memuat daftar petugas</option>';
        });

    document.getElementById('adminDetailModal').style.display = "block";
}

function closeModalDetail() {
    document.getElementById('adminDetailModal').style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById('adminDetailModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

function confirmLogout() {
    if (confirm("Yakin mau keluar dari panel administrator?")) {
        window.location.href = "../logout.php";
    }
}
</script>
</body>
</html>