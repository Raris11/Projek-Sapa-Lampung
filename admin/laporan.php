<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php'; 
$nama = $_SESSION['nama'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_laporan'])) {
    $id_lap_update = mysqli_real_escape_string($conn, $_POST['id_laporan']);
    $status_baru   = mysqli_real_escape_string($conn, $_POST['status']);
    $id_petugas    = mysqli_real_escape_string($conn, $_POST['id_petugas']);
    
    $val_petugas = empty($id_petugas) ? "NULL" : "'$id_petugas'";

    $query_update = "UPDATE laporan SET status = '$status_baru', id_petugas = $val_petugas WHERE id_laporan = '$id_lap_update'";
    
    if (mysqli_query($conn, $query_update)) {
        echo "<script>alert('Laporan $id_lap_update berhasil diperbarui!'); window.location.href='laporan.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data laporan.');</script>";
    }
}

$cp = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Pending'"));
$cv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Verifikasi'"));
$cd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status='Diproses'"));


$search        = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$filter_kat    = isset($_GET['kategori']) ? mysqli_real_escape_string($conn, $_GET['kategori']) : '';
$filter_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

$sql = "SELECT laporan.*, kategori.nama_kategori, IFNULL(users.nama, 'Masyarakat Umum (Anonim)') as nama_pelapor 
        FROM laporan 
        JOIN kategori ON laporan.id_kategori = kategori.id_kategori 
        LEFT JOIN users ON laporan.id_pelapor = users.id_user
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND (laporan.id_laporan LIKE '%$search%' OR laporan.judul_laporan LIKE '%$search%')";
}
if (!empty($filter_kat)) {
    $sql .= " AND kategori.nama_kategori = '$filter_kat'";
}
if (!empty($filter_status)) {
    $sql .= " AND laporan.status = '$filter_status'";
}

$sql .= " ORDER BY laporan.tanggal_lapor DESC";
$query_all_lap = mysqli_query($conn, $sql);
$total_tampil  = mysqli_num_rows($query_all_lap);

$list_petugas = [];
$query_ptg = mysqli_query($conn, "SELECT petugas.id_petugas, petugas.divisi, users.nama FROM petugas JOIN users ON petugas.id_user = users.id_user");
while($ptg = mysqli_fetch_assoc($query_ptg)) {
    $list_petugas[] = $ptg;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Masuk — SAPA Admin</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .modal {
            display: none; 
            position: fixed; 
            z-index: 9999; 
            left: 0; top: 0;
            width: 100%; height: 100%;
            overflow: auto; 
            background-color: rgba(0,0,0,0.5);
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto; 
            padding: 24px;
            border-radius: 16px;
            width: 90%;
            max-width: 700px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.2);
            position: relative;
            animation: animatetop 0.3s ease;
        }
        @keyframes animatetop {
            from {top: -100px; opacity: 0}
            to {top: 0; opacity: 1}
        }
        .close-btn {
            position: absolute;
            right: 20px; top: 15px;
            color: #aaa;
            font-size: 28px; font-weight: bold;
            cursor: pointer;
        }
        .close-btn:hover { color: #c62828; }
        
        .modal-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        .detail-group {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .detail-label {
            font-size: 11px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: bold;
        }
        .detail-value {
            font-size: 14px;
            color: #0f172a;
            font-weight: 600;
        }
        .modal-img {
            max-width: 100%;
            max-height: 160px;
            object-fit: contain;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
            margin-top: 5px;
        }
    </style>
</head>

<body>

<div class="dashboard-layout">

    <aside class="sidebar">
        <div>
            <div class="sidebar-brand">
                <div class="sidebar-logo">SA</div>
                <div>
                    <div class="sidebar-brand-name">SAPA Admin</div>
                    <div class="sidebar-brand-sub">Panel Administrator</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="sidebar-item">Dashboard</a>
                <a href="laporan.php" class="sidebar-item active">Laporan Masuk</a>
                <a href="tracking.php" class="sidebar-item">Tracking</a>
                <a href="statistik.php" class="sidebar-item">Statistik</a>
                <a href="petugas.php" class="sidebar-item">Manajemen Petugas</a>
                <a href="akun.php" class="sidebar-item">Kelola Akun</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($nama, 0, 1)) ?></div>
                <div>
                    <div class="sidebar-user-name"><?= htmlspecialchars($nama) ?></div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </div>
            <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Laporan Masuk</h1>
                <p class="page-subtitle">Verifikasi dan tindak lanjuti laporan masyarakat</p>
            </div>
        </div>

        <div class="content-body">
            <div class="stats-row stats-row-3">
                <div class="stat-box">
                    <div class="stat-box-label">Pending</div>
                    <div class="stat-box-num"><?= $cp['total']; ?></div>
                    <div class="stat-box-change">Belum diverifikasi</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-label">Diverifikasi</div>
                    <div class="stat-box-num"><?= $cv['total']; ?></div>
                    <div class="stat-box-change">Menunggu penugasan</div>
                </div>
                <div class="stat-box">
                    <div class="stat-box-label">Diproses</div>
                    <div class="stat-box-num"><?= $cd['total']; ?></div>
                    <div class="stat-box-change">Sedang ditangani</div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Daftar Laporan</div>
                        <div class="card-subtitle">Total data tampil: <?= $total_tampil; ?></div>
                    </div>
                </div>

                <form method="GET" action="laporan.php">
                    <div class="filter-row">
                        <input type="text" name="search" class="form-input" placeholder="Cari ID laporan atau judul..." value="<?= htmlspecialchars($search); ?>">
                        <select name="kategori" onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            <option value="Infrastruktur" <?= ($filter_kat == 'Infrastruktur') ? 'selected' : ''; ?>>Infrastruktur</option>
                            <option value="Kebersihan" <?= ($filter_kat == 'Kebersihan') ? 'selected' : ''; ?>>Kebersihan</option>
                            <option value="Keamanan" <?= ($filter_kat == 'Keamanan') ? 'selected' : ''; ?>>Keamanan</option>
                            <option value="Fasilitas Umum" <?= ($filter_kat == 'Fasilitas Umum') ? 'selected' : ''; ?>>Fasilitas Umum</option>
                            <option value="Darurat" <?= ($filter_kat == 'Darurat') ? 'selected' : ''; ?>>Darurat</option>
                        </select>
                        <select name="status" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Pending" <?= ($filter_status == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                            <option value="Verifikasi" <?= ($filter_status == 'Verifikasi') ? 'selected' : ''; ?>>Verifikasi</option>
                            <option value="Diproses" <?= ($filter_status == 'Diproses') ? 'selected' : ''; ?>>Diproses</option>
                            <option value="Selesai" <?= ($filter_status == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                </form>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Pelapor</th>
                                <th>Judul Laporan</th>
                                <th>Kategori</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($total_tampil > 0) : ?>
                                <?php while($row = mysqli_fetch_assoc($query_all_lap)) : ?>
                                <tr>
                                    <td><span class="id-badge"><?= $row['id_laporan']; ?></span></td>
                                    <td><?= htmlspecialchars($row['nama_pelapor']); ?></td>
                                    <td><?= htmlspecialchars($row['judul_laporan']); ?></td>
                                    <td><span class="cat-tag"><?= $row['nama_kategori']; ?></span></td>
                                    <td><span class="pill pill-<?= strtolower($row['status']); ?>"><?= $row['status']; ?></span></td>
                                    <td>
                                        <button class="table-btn" onclick='openAdminModal(<?= json_encode($row, JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>Detail</button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="6" style="text-align:center; padding:20px;">Data kosong.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="adminModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeAdminModal()">&times;</span>
        <h3 style="font-size: 18px; font-weight: 800; border-bottom: 2px solid #f0f0f5; padding-bottom: 8px; color:#1a1a2e;">
            Detail & Penanganan Pengaduan
        </h3>
        
        <div class="modal-grid">
            <div>
                <div class="detail-group">
                    <div class="detail-label">ID Laporan</div>
                    <div class="detail-value" id="det-id"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Pelapor</div>
                    <div class="detail-value" id="det-pelapor"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Judul Masalah</div>
                    <div class="detail-value" id="det-judul"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Alamat Lokasi</div>
                    <div class="detail-value" id="det-alamat" style="font-weight: normal; font-size: 13px;"></div>
                </div>
                <div class="detail-group">
                    <div class="detail-label">Deskripsi Keluhan</div>
                    <p id="det-deskripsi" style="font-size:13px; color:#475569; margin-top:3px; max-height:80px; overflow-y:auto;"></p>
                </div>
                <div class="detail-group" style="border:none;">
                    <div class="detail-label">Foto Bukti Lampiran</div>
                    <img id="det-img" src="" class="modal-img" alt="Bukti Fisik">
                </div>
            </div>

            <div style="background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; height: fit-content;">
                <h4 style="margin-bottom: 12px; font-size: 14px; color:#0f172a;">Aksi Administrator</h4>
                
                <form action="laporan.php" method="POST">
                    <input type="hidden" name="id_laporan" id="form-id-laporan">

                    <div style="margin-bottom: 15px;">
                        <label style="font-size:12px; font-weight:bold; display:block; margin-bottom:5px;">Set Status Progres</label>
                        <select name="status" id="form-status" class="form-input" style="width:100%; padding:8px; border-radius:6px;" required>
                            <option value="Pending">Pending (Baru)</option>
                            <option value="Verifikasi">Verifikasi (Disetujui)</option>
                            <option value="Diproses">Diproses (Lapangan)</option>
                            <option value="Selesai">Selesai (Tuntas)</option>
                        </select>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <label style="font-size:12px; font-weight:bold; display:block; margin-bottom:5px;">Tunjuk Petugas Lapangan</label>
                        <select name="id_petugas" id="form-petugas" class="form-input" style="width:100%; padding:8px; border-radius:6px;">
                            <option value="">-- Cari/Pilih Petugas --</option>
                            <?php foreach($list_petugas as $p) : ?>
                                <option value="<?= $p['id_petugas']; ?>">
                                    <?= htmlspecialchars($p['nama']); ?> (<?= $p['divisi']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="update_laporan" class="btn-primary" style="width:100%; padding:10px; font-weight:bold;">
                        Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin ingin keluar?")) { window.location.href = "../logout.php"; }
}

function openAdminModal(data) {
    document.getElementById('det-id').innerText = data.id_laporan;
    document.getElementById('det-pelapor').innerText = data.nama_pelapor;
    document.getElementById('det-judul').innerText = data.judul_laporan;
    document.getElementById('det-alamat').innerText = data.alamat_kejadian;
    document.getElementById('det-deskripsi').innerText = data.deskripsi;
    
    document.getElementById('form-id-laporan').value = data.id_laporan;
    document.getElementById('form-status').value = data.status;
    document.getElementById('form-petugas').value = data.id_petugas ? data.id_petugas : "";

    if(data.foto_bukti) {
        document.getElementById('det-img').src = "../assets/img/laporan/" + data.foto_bukti;
        document.getElementById('det-img').style.display = "block";
    } else {
        document.getElementById('det-img').style.display = "none";
    }

    document.getElementById('adminModal').style.display = "block";
}

function closeAdminModal() {
    document.getElementById('adminModal').style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById('adminModal');
    if (event.target == modal) { modal.style.display = "none"; }
}
</script>
</body>
</html>