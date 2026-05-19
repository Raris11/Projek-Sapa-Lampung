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

$arr_nama = explode(' ', $nama);
$inisial = strtoupper(substr($arr_nama[0], 0, 1) . (isset($arr_nama[1]) ? substr($arr_nama[1], 0, 1) : ''));

if (!isset($_GET['id'])) {
    header("Location: petugas.php");
    exit;
}
$id_laporan = mysqli_real_escape_string($conn, $_GET['id']);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_update'])) {
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

    if (empty($_FILES['foto_progres']['name'])) {
        echo "<script>alert('Gagal! Foto bukti pekerjaan wajib diunggah untuk verifikasi ke Admin.'); window.history.back();</script>";
        exit;
    }

    $filename    = $_FILES['foto_progres']['name'];
    $filetmp     = $_FILES['foto_progres']['tmp_name'];
    $ext         = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $allowed_ext = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed_ext)) {
        echo "<script>alert('Gagal! Format file salah. Hanya diperbolehkan file gambar (JPG, JPEG, PNG).'); window.history.back();</script>";
        exit;
    }

    $target_dir   = "../assets/img/laporan/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $new_filename = "PRG-" . time() . "." . $ext;

    if (move_uploaded_file($filetmp, $target_dir . $new_filename)) {
        $update_lap = mysqli_query($conn, "UPDATE laporan SET status='$status_baru' WHERE id_laporan='$id_laporan'");
        
        if ($update_lap) {
            $insert_track = mysqli_query($conn, "INSERT INTO tracking_progress (id_laporan, status_progres, keterangan, foto_progres, persentase) 
                                                 VALUES ('$id_laporan', '$status_tracking', '$keterangan', '$new_filename', '$persentase')");
            
            if ($insert_track) {
                echo "<script>alert('Progres laporan $id_laporan berhasil diperbarui!'); window.location.href='petugas.php';</script>";
                exit;
            }
        }
    }
    echo "<script>alert('Terjadi kesalahan saat memproses data lapangan.');</script>";
}

$query_detail = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori 
                                      FROM laporan 
                                      JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
                                      WHERE laporan.id_laporan = '$id_laporan' AND laporan.id_petugas = '$id_ptg_log'");
$data = mysqli_fetch_assoc($query_detail);

if (!$data) {
    header("Location: petugas.php");
    exit;
}

$query_timeline = mysqli_query($conn, "SELECT * FROM tracking_progress WHERE id_laporan='$id_laporan' ORDER BY waktu_update ASC");

$current_percent = 0;
$query_latest_track = mysqli_query($conn, "SELECT persentase FROM tracking_progress WHERE id_laporan='$id_laporan' ORDER BY waktu_update DESC LIMIT 1");
$latest_track = mysqli_fetch_assoc($query_latest_track);
if ($latest_track) {
    $current_percent = $latest_track['persentase'];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Progress - SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/petugas.css">
    <style>
        .form-group { margin-bottom: 22px; }
        .form-label { display: block; margin-bottom: 10px; font-size: 14px; font-weight: 700; color: #111827; }
        .form-select-status, .form-textarea { width: 100%; padding: 14px 16px; border: 1px solid #d8dee6; border-radius: 14px; background: white; font-size: 14px; outline: none; box-sizing: border-box; }
        .form-textarea { height: 120px; resize: vertical; }
        .text-danger { color: #dc2626; font-weight: bold; }
        .upload-box { border: 2px dashed #cfd6dd; border-radius: 20px; padding: 38px 24px; text-align: center; background: #fafcfa; cursor: pointer; transition: .25s ease; }
        .upload-box:hover { border-color: #2e7d32; background: #f3fbf4; }
        .upload-icon { font-size: 48px; margin-bottom: 12px; }
        .upload-title { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 6px; }
        .upload-sub { font-size: 13px; color: #6b7280; }
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
                    <div class="sidebar-brand-sub">Panel Petugas</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="petugas.php" class="sidebar-item">Dashboard</a>
                <a href="daftar-tugas.php" class="sidebar-item">Daftar Tugas</a>
                <a href="petugas.php" class="sidebar-item active">Update Progress</a>
                <a href="riwayat.php" class="sidebar-item">Riwayat Tugas</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= $inisial; ?></div>
                <div>
                    <div class="sidebar-user-name"><?= htmlspecialchars($nama); ?></div>
                    <div class="sidebar-user-role">Petugas Lapangan</div>
                </div>
            </div>
            <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
        </div>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div>
                <h1 class="page-title">Update Progress</h1>
                <p class="page-subtitle">Perbarui status dan dokumentasi tugas lapangan</p>
            </div>
        </div>

        <div class="content-body">

            <div class="card mb-24">
                <div class="card-header">
                    <div>
                        <div class="card-title">Informasi Laporan</div>
                        <div class="card-subtitle"><?= $data['id_laporan']; ?></div>
                    </div>
                    <span class="pill pill-diproses"><?= $data['status']; ?></span>
                </div>
                <div class="card-body">
                    <div class="detail-row"><strong>Judul:</strong> <?= htmlspecialchars($data['judul_laporan']); ?></div>
                    <div class="detail-row"><strong>Lokasi:</strong> <?= htmlspecialchars($data['alamat_kejadian']); ?></div>
                    <div class="detail-row"><strong>Deadline:</strong> <span class="text-danger"><?= date('d M Y', strtotime($data['deadline'] ?? $data['tanggal_lapor'])); ?></span></div>
                    <div class="progress-info">
                        <div class="progress-text">
                            <span>Progress Saat Ini</span>
                            <strong><?= $current_percent; ?>%</strong>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill" style="width:<?= $current_percent; ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-24">
                <div class="card-header"><div class="card-title">Timeline Progress</div></div>
                <div class="card-body">
                    <div class="timeline">
                        <?php if (mysqli_num_rows($query_timeline) > 0) : ?>
                            <?php while($tl = mysqli_fetch_assoc($query_timeline)) : ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div>
                                    <div class="timeline-title"><?= htmlspecialchars($tl['status_progres']); ?> (<?= $tl['persentase']; ?>%)</div>
                                    <div class="timeline-time"><?= date('d M Y · H:i', strtotime($tl['waktu_update'])); ?> WIB</div>
                                    <div class="timeline-desc"><?= htmlspecialchars($tl['keterangan']); ?></div>
                                    <?php if (!empty($tl['foto_progres'])) : ?>
                                        <a href="../assets/img/laporan/<?= $tl['foto_progres']; ?>" target="_blank" style="font-size:11px; color:#2e7d32; display:block; margin-top:5px; font-weight:bold;">📄 Lihat Foto Bukti</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <div class="timeline-item">
                                <div class="timeline-dot"></div>
                                <div><div class="timeline-title">Belum ada perbaruan progres lapangan.</div></div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <form action="" method="POST" enctype="multipart/form-data">
                <div class="card mb-24">
                    <div class="card-header"><div class="card-title">Form Update</div></div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <label class="form-label">Status Progres Baru <span class="text-danger">*</span></label>
                            <select name="status" class="form-select-status" required>
                                <option value="Verifikasi" <?= ($data['status'] == 'Verifikasi') ? 'selected' : ''; ?>>Diterima (Tugas Siap Dikerjakan)</option>
                                <option value="Dalam Perjalanan" <?= ($data['status'] == 'Dalam Perjalanan') ? 'selected' : ''; ?>>Dalam Perjalanan (Menuju Lokasi)</option>
                                <option value="Diproses" <?= ($data['status'] == 'Diproses') ? 'selected' : ''; ?>>Pengerjaan (Sedang Perbaikan)</option>
                                <option value="Selesai" <?= ($data['status'] == 'Selesai') ? 'selected' : ''; ?>>Selesai (Tugas Rampung Total)</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Keterangan / Tindakan Lapangan <span class="text-danger">*</span></label>
                            <textarea name="keterangan" class="form-textarea" placeholder="Tuliskan tindakan teknis yang Anda lakukan di lokasi..." required></textarea>
                        </div>

                    </div>
                </div>

                <div class="card mb-24">
                    <div class="card-header"><div class="card-title">Upload Dokumentasi</div></div>
                    <div class="card-body">
                        
                        <div class="form-group">
                            <div class="upload-box" onclick="document.getElementById('foto_progres').click();">
                                <div class="upload-icon">📷</div>
                                <div class="upload-title" id="file-label">Klik untuk Memilih Gambar Progres</div>
                                <div class="upload-sub">Hanya file gambar JPG / JPEG / PNG</div>
                            </div>
                            <input type="file" id="foto_progres" name="foto_progres" style="display:none;" accept="image/*" required onchange="document.getElementById('file-label').innerText = 'Foto dipilih: ' + this.files[0].name;">
                        </div>

                    </div>
                </div>

                <div class="card mb-24">
                    <div class="card-header"><div class="card-title">Checklist Keselamatan</div></div>
                    <div class="card-body">
                        <ul class="check-list">
                            <li><div class="check-icon">✓</div> APD lengkap digunakan</li>
                            <li><div class="check-icon">✓</div> Area kerja diberi tanda</li>
                            <li><div class="check-icon">✓</div> Sudah koordinasi dengan warga</li>
                        </ul>
                    </div>
                </div>

                <div class="action-row">
                    <a href="daftar-tugas.php" class="btn btn-outline" style="text-decoration:none; text-align:center; line-height:24px;">Kembali</a>
                    <button type="submit" name="proses_update" class="btn btn-primary">Kirim Update</button>
                </div>
            </form>

        </div>

    </main>

</div>

<script>
function confirmLogout() {
    const yakin = confirm("Yakin ingin keluar?");
    if (yakin) {
        window.location.href = "../logout.php";
    }
}
</script>

</body>
</html>