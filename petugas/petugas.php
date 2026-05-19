<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'petugas') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';

$nama = $_SESSION['nama'] ?? 'Petugas';
$id_ptg_log = $_SESSION['id_petugas'] ?? 0;
$kode_petugas = $_SESSION['kode_petugas'] ?? 'PTG-000';
$divisi_petugas = $_SESSION['divisi'] ?? 'Umum';
$wilayah = $_SESSION['wilayah'] ?? 'Bandar Lampung';
$rating = $_SESSION['rating'] ?? 0.0;

$arr_nama = explode(' ', $nama);
$inisial = strtoupper(substr($arr_nama[0], 0, 1) . (isset($arr_nama[1]) ? substr($arr_nama[1], 0, 1) : ''));

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_update'])) {
    $id_laporan = mysqli_real_escape_string($conn, $_POST['id_laporan']);
    $status_baru = mysqli_real_escape_string($conn, $_POST['status']);
    $keterangan  = mysqli_real_escape_string($conn, $_POST['keterangan']);
    
    $persentase  = 25;
    $status_tracking = 'Tugas Diterima';

    if ($status_baru == 'Dalam Perjalanan') {
        $persentase = 50;
        $status_tracking = 'Dalam Perjalanan';
    } elseif ($status_baru == 'Diproses') {
        $persentase = 75;
        $status_tracking = 'Pengerjaan Lapangan';
    } elseif ($status_baru == 'Selesai') {
        $persentase = 100;
        $status_tracking = 'Selesai';
    }

    if (!empty($_FILES['foto_progres']['name'])) {
        $filename    = $_FILES['foto_progres']['name'];
        $filetmp     = $_FILES['foto_progres']['tmp_name'];
        $ext         = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $allowed_ext)) {
            $target_dir   = "../assets/img/laporan/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }

            $new_filename = "PRG-" . time() . "." . $ext;

            if (move_uploaded_file($filetmp, $target_dir . $new_filename)) {
                $update_lap = mysqli_query($conn, "UPDATE laporan SET status='$status_baru' WHERE id_laporan='$id_laporan'");
                if ($update_lap) {
                    mysqli_query($conn, "INSERT INTO tracking_progress (id_laporan, status_progres, keterangan, foto_progres, persentase) VALUES ('$id_laporan', '$status_tracking', '$keterangan', '$new_filename', '$persentase')");
                    echo "<script>alert('Progres laporan $id_laporan berhasil diperbarui!'); window.location.href='petugas.php';</script>";
                    exit;
                }
            }
        } else {
            echo "<script>alert('Gagal! Format berkas harus gambar (JPG/JPEG/PNG).');</script>";
        }
    } else {
        echo "<script>alert('Gagal! Foto bukti pekerjaan wajib diunggah.');</script>";
    }
}

$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE id_petugas='$id_ptg_log'");
$total_tugas = mysqli_fetch_assoc($q_total)['total'] ?? 0;

$q_proses = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE id_petugas='$id_ptg_log' AND (status='Diproses' OR status='Dalam Perjalanan')");
$total_proses = mysqli_fetch_assoc($q_proses)['total'] ?? 0;

$q_selesai = mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE id_petugas='$id_ptg_log' AND status='Selesai'");
$total_selesai = mysqli_fetch_assoc($q_selesai)['total'] ?? 0;

$query_tabel = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori 
                                    FROM laporan 
                                    JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
                                    WHERE laporan.id_petugas = '$id_ptg_log' AND laporan.status != 'Selesai'
                                    ORDER BY laporan.tanggal_lapor DESC");

$query_notif = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori 
                                    FROM laporan 
                                    JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
                                    WHERE laporan.id_petugas = '$id_ptg_log' AND laporan.status = 'Verifikasi'
                                    ORDER BY laporan.tanggal_lapor DESC LIMIT 1");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Petugas - SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/petugas.css">
    <style>
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); }
        .modal-content { background-color: #fff; margin: 5% auto; padding: 24px; border-radius: 16px; width: 90%; max-width: 500px; box-shadow: 0 4px 24px rgba(0,0,0,0.2); position: relative; animation: animatetop 0.3s ease; }
        @keyframes animatetop { from {top: -100px; opacity: 0} to {top: 0; opacity: 1} }
        .close-btn { position: absolute; right: 20px; top: 15px; color: #aaa; font-size: 28px; font-weight: bold; cursor: pointer; }
        .close-btn:hover { color: #c62828; }
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; margin-bottom: 6px; font-size: 13px; font-weight: 700; color: #111827; }
        .form-select, .form-textarea { width: 100%; padding: 10px 12px; border: 1px solid #d8dee6; border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box; }
        .form-textarea { height: 80px; resize: vertical; }
        .upload-box { border: 2px dashed #cfd6dd; border-radius: 12px; padding: 20px; text-align: center; background: #fafcfa; cursor: pointer; }
        .upload-icon { font-size: 32px; margin-bottom: 6px; }
        .upload-title { font-size: 13px; font-weight: 700; color: #111827; }
        
        .topbar-info-block { display: flex; flex-direction: column; align-items: flex-end; justify-content: center; text-align: right; }
        .topbar-meta-text { font-size: 11px; color: #6b7280; font-weight: 500; margin-top: 2px; }
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
                <a href="petugas.php" class="sidebar-item active">Dashboard</a>
                <a href="daftar-tugas.php" class="sidebar-item">Daftar Tugas</a>
                <a href="riwayat.php" class="sidebar-item">Riwayat Tugas</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
        </div>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="topbar-title">
                <h2>Dashboard Kerja</h2>
                <p>Sistem Monitoring Penugasan Operasional Lapangan</p>
            </div>
            <div class="topbar-user">
                <div class="topbar-info-block">
                    <span class="topbar-user-name"><?= htmlspecialchars($nama); ?></span>
                    <span class="topbar-meta-text"><strong><?= htmlspecialchars($kode_petugas); ?></strong> • <?= htmlspecialchars($divisi_petugas); ?> • <?= htmlspecialchars($wilayah); ?></span>
                </div>
                <div class="topbar-avatar"><?= $inisial; ?></div>
            </div>
        </div>

        <div class="content-body">

            <div class="petugas-hero" style="border-radius:16px; margin-bottom:20px; border:1px solid #e5e7eb; box-shadow:0 1px 4px rgba(0,0,0,.05);">
                <div>
                    <h1 class="page-title">Selamat Bekerja, <?= htmlspecialchars($nama); ?> 👋</h1>
                    <p class="page-subtitle">Gunakan panel ini untuk memperbarui kendala infrastruktur daerah secara berkala.</p>
                </div>
                <div class="hero-status">⭐ Rating Penilaian: <?= number_format($rating, 1); ?>/5</div>
            </div>

            <?php if(mysqli_num_rows($query_notif) > 0) : ?>
                <?php $notif = mysqli_fetch_assoc($query_notif); ?>
                <div class="alert-box" style="margin-bottom:20px; display:block;">
                    <strong>🔔 Tugas Baru Masuk:</strong> Anda ditunjuk untuk menangani laporan <strong><?= htmlspecialchars($notif['id_laporan']); ?></strong>: "<?= htmlspecialchars($notif['judul_laporan']); ?>". Lokasi penugasan berada di <u><?= htmlspecialchars($notif['alamat_kejadian']); ?></u>. Harap segera lakukan update progres perjalanan ke lokasi.
                </div>
            <?php endif; ?>

            <div class="stats-row">
                <div class="stat-box stat-orange">
                    <div class="stat-box-label">Total Penugasan</div>
                    <div class="stat-box-num"><?= $total_tugas; ?></div>
                    <div class="stat-box-change">Semua agenda</div>
                </div>
                <div class="stat-box stat-blue">
                    <div class="stat-box-label">Sedang Diproses</div>
                    <div class="stat-box-num"><?= $total_proses; ?></div>
                    <div class="stat-box-change">Tugas aktif</div>
                </div>
                <div class="stat-box stat-green">
                    <div class="stat-box-label">Selesai Ditangani</div>
                    <div class="stat-box-num"><?= $total_selesai; ?></div>
                    <div class="stat-box-change up">Pekerjaan tuntas</div>
                </div>
                <div class="stat-box stat-yellow">
                    <div class="stat-box-label">Performa Perangkat</div>
                    <div class="stat-box-num"><?= number_format($rating, 1); ?></div>
                    <div class="stat-box-change">Bintang ulasan</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Agenda Tugas Aktif Anda</div>
                        <div class="card-subtitle">Daftar keluhan masyarakat yang harus segera diselesaikan</div>
                    </div>
                    <a href="daftar-tugas.php" class="btn btn-outline btn-sm">Lihat Semua Tugas →</a>
                </div>
                
                <div class="tugas-list" style="padding:20px; display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <?php if(mysqli_num_rows($query_tabel) > 0) : ?>
                        <?php while($row = mysqli_fetch_assoc($query_tabel)) : 
                            $pillClass = 'pending';
                            $itemClass = 'urgent';
                            if ($row['status'] == 'Verifikasi') { $pillClass = 'verifikasi'; $itemClass = 'urgent'; }
                            elseif ($row['status'] == 'Diproses' || $row['status'] == 'Dalam Perjalanan') { $pillClass = 'diproses'; $itemClass = 'warning'; }
                        ?>
                        <div class="tugas-item <?= $itemClass; ?>" style="margin:0;">
                            <div class="tugas-top">
                                <div>
                                    <div class="tugas-title"><?= htmlspecialchars($row['judul_laporan']); ?></div>
                                    <div class="tugas-id"><?= $row['id_laporan']; ?></div>
                                </div>
                                <span class="pill pill-<?= $pillClass; ?>"><?= $row['status']; ?></span>
                            </div>
                            <div class="tugas-lokasi">📍 <?= htmlspecialchars($row['alamat_kejadian']); ?></div>
                            <div class="tugas-footer">
                                <span class="cat-tag cat-infrastruktur">🏗️ <?= $row['nama_kategori']; ?></span>
                                <button class="action-btn" onclick='openUpdateModal(<?php echo json_encode($row); ?>)'>Update</button>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <div style="grid-column: span 2; text-align:center; padding:30px; color:#64748b;">Tidak ada agenda tugas aktif lapangan untuk Anda saat ini.</div>
                    <?php endif; ?>
                </div>
            </div>

        </div>

    </main>
</div>

<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 style="font-size: 16px; font-weight: 800; border-bottom: 2px solid #f0f0f5; padding-bottom: 8px; margin-bottom: 15px; color:#1a1a2e;">Update Progres Lapangan</h3>
        
        <form action="petugas.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_laporan" id="modal-id-laporan">

            <div class="form-group">
                <label class="form-label">Status Progres Baru *</label>
                <select name="status" id="modal-status" class="form-select" required>
                    <option value="Verifikasi">Diterima (Tugas Siap Dikerjakan)</option>
                    <option value="Dalam Perjalanan">Dalam Perjalanan (Menuju Lokasi)</option>
                    <option value="Diproses">Pengerjaan (Sedang Perbaikan)</option>
                    <option value="Selesai">Selesai (Tugas Rampung Total)</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Keterangan / Tindakan Lapangan *</label>
                <textarea name="keterangan" class="form-textarea" placeholder="Tuliskan tindakan teknik lapangan..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Upload Gambar Bukti *</label>
                <div class="upload-box" onclick="document.getElementById('foto_progres').click();">
                    <div class="upload-icon">📷</div>
                    <div class="upload-title" id="file-label">Klik Pilih Gambar Progres</div>
                </div>
                <input type="file" id="foto_progres" name="foto_progres" style="display:none;" accept="image/*" required onchange="document.getElementById('file-label').innerText = this.files[0].name;">
            </div>

            <button type="submit" name="proses_update" class="btn-primary" style="width:100%; padding:10px; border-radius:8px; font-weight:bold; background:#1a56e8; color:white; border:none; cursor:pointer;">Kirim Update</button>
        </form>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin ingin keluar dari akun petugas?")) { window.location.href = "../logout.php"; }
}

const modal = document.getElementById('updateModal');

function openUpdateModal(data) {
    document.getElementById('modal-id-laporan').value = data.id_laporan;
    document.getElementById('modal-status').value = data.status;
    modal.style.display = "block";
}

function closeModal() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) { modal.style.display = "none"; }
}
</script>

</body>
</html>