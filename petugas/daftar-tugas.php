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
                    echo "<script>alert('Progres laporan $id_laporan berhasil diperbarui!'); window.location.href='daftar-tugas.php';</script>";
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

$query_tabel = mysqli_query($conn, "SELECT laporan.*, kategori.nama_kategori 
                                    FROM laporan 
                                    JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
                                    WHERE laporan.id_petugas = '$id_ptg_log' AND laporan.status != 'Selesai'
                                    ORDER BY laporan.tanggal_lapor DESC");
$total_aktif = mysqli_num_rows($query_tabel);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Tugas - SAPA Lampung</title>
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
                <a href="daftar-tugas.php" class="sidebar-item active">Daftar Tugas</a>
                <a href="riwayat.php" class="sidebar-item">Riwayat Tugas</a>
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
                <h1 class="page-title">Daftar Tugas Saya</h1>
                <p class="page-subtitle">Semua tugas aktif yang diberikan admin</p>
            </div>
        </div>

        <div class="content-body">

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Tugas Aktif</div>
                        <div class="card-subtitle">Total <?= $total_aktif; ?> tugas aktif</div>
                    </div>
                </div>

                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Deadline</th>
                                <th>Urgensi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($total_aktif > 0) : ?>
                                <?php while($row = mysqli_fetch_assoc($query_tabel)) : 
                                    $pillClass = 'pending';
                                    if ($row['status'] == 'Verifikasi') $pillClass = 'verifikasi';
                                    elseif ($row['status'] == 'Diproses' || $row['status'] == 'Dalam Perjalanan') $pillClass = 'diproses';
                                ?>
                                <tr>
                                    <td><span class="id-badge"><?= $row['id_laporan'] ?></span></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['judul_laporan']) ?></div>
                                        <div class="text-small text-muted"><?= htmlspecialchars($row['alamat_kejadian']) ?></div>
                                    </td>
                                    <td class="deadline-orange"><?= date('d M Y', strtotime($row['deadline'] ?? $row['tanggal_lapor'])); ?></td>
                                    <td><span class="urgensi <?= strtolower($row['urgensi']); ?>"><?= $row['urgensi'] ?></span></td>
                                    <td><span class="pill pill-<?= $pillClass ?>"><?= $row['status'] ?></span></td>
                                    <td>
                                        <button class="action-btn" onclick='openUpdateModal(<?php echo json_encode($row); ?>)'>Update</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="6" style="text-align:center; padding:20px; color:#64748b;">Tidak ada data tugas aktif lapangan.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
</div>

<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 style="font-size: 16px; font-weight: 800; border-bottom: 2px solid #f0f0f5; padding-bottom: 8px; margin-bottom: 15px; color:#1a1a2e;">Update Progres Lapangan</h3>
        
        <form action="daftar-tugas.php" method="POST" enctype="multipart/form-data">
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
                <textarea name="keterangan" class="form-textarea" placeholder="Tuliskan tindakan teknis lapangan..." required></textarea>
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
    if (confirm("Yakin ingin keluar?")) { window.location.href = "../logout.php"; }
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