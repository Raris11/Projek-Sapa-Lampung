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

$query_kat = mysqli_query($conn, "SELECT * FROM kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/masyarakat.css">
    <style>
        .text-danger { color: #dc2626; font-weight: bold; }
        .file-input-hidden { display: none; }
        .sidebar-public { background: linear-gradient(160deg, #c62828 0%, #7b0000 100%) !important; }
        .upload-box { cursor: pointer; transition: background 0.2s ease; }
        .upload-box:hover { background-color: #f8fafc; border-color: #c62828; }
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
                <a href="dashboard.php" class="sidebar-item">&ensp;Dashboard</a>
                <a href="buat-laporan.php" class="sidebar-item active">&ensp;Buat Laporan</a>
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
                <h1 class="page-title">Buat Laporan Publik</h1>
                <p class="page-subtitle">Laporkan keluhan Anda secara langsung <?php echo !$is_logged_in ? 'tanpa perlu masuk akun' : ''; ?></p>
            </div>
            <div class="topbar-right">
                <?php if (!$is_logged_in): ?>
                    <a href="../login.php" class="btn-primary" style="padding: 8px 16px; font-size: 13px; text-decoration: none;">Masuk Sistem</a>
                <?php else: ?>
                    <div class="topbar-user">
                        <div class="topbar-avatar" style="display: flex !important; align-items: center !important; justify-content: center !important; background: #e2e8f0 !important; color: #64748b !important; border-radius: 50% !important; padding: 0 !important; margin: 0 !important; width: 40px !important; height: 40px !important; box-sizing: border-box !important;">
                            <svg class="icon-svg" viewBox="0 0 24 24" style="width: 24px; height: 24px;"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/></svg>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="content-body">
            <div class="form-card">
                <div class="form-header">
                    <h2>Form Laporan Pengaduan</h2>
                    <p>Isi data laporan dengan benar agar mudah diverifikasi oleh Admin.</p>
                    <div class="card-subtitle"><span class="text-danger">*</span> Keterangan: Kolom bertanda merah wajib diisi</div>
                </div>

                <form action="proses-lapor.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Judul Laporan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_laporan" class="form-input" placeholder="Contoh: Jalan berlubang parah di daerah Sukarame" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Laporan <span class="text-danger">*</span></label>
                        <select name="id_kategori" class="form-input" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while($kat = mysqli_fetch_assoc($query_kat)) : ?>
                                <option value="<?php echo $kat['id_kategori']; ?>"><?php echo $kat['nama_kategori']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Deskripsi Masalah <span class="text-danger">*</span></label>
                        <textarea name="deskripsi" class="form-textarea" placeholder="Jelaskan kronologi atau detail masalah secara rinci..." required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Alamat Kejadian <span class="text-danger">*</span></label>
                        <input type="text" name="alamat_kejadian" class="form-input" placeholder="Contoh: Jl. Ryacudu No. 12, Sukarame, Bandar Lampung" required>
                    </div>
                    <div class="form-group">
                        <label>Upload Foto Bukti <span class="text-danger">*</span></label>
                        <div class="upload-box" onclick="document.getElementById('foto_bukti').click();" style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 30px; border: 2px dashed #cbd5e1; border-radius: 8px;">
                            <div class="upload-icon" style="color: #64748b; margin-bottom: 10px; display: flex; align-items: center; justify-content: center;">
                                <svg viewBox="0 0 24 24" style="width: 32px; height: 32px; fill: currentColor;"><path d="M4 4h3l2-3h6l2 3h3c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2zm8 3c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 2c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3z"/></svg>
                            </div>
                            <p id="file-label" style="margin: 0 0 5px 0; font-size: 14px; font-weight: 600; color: #334155;">Klik untuk memilih foto dari perangkat</p>
                            <span style="font-size: 12px; color: #64748b;">Format: JPG / JPEG / PNG (Maksimal 5MB)</span>
                        </div>
                        <input type="file" id="foto_bukti" name="foto_bukti" class="file-input-hidden" accept="image/*" required onchange="updateFileName(this)">
                    </div>
                    <div class="form-group">
                        <label>Tingkat Urgensi <span class="text-danger">*</span></label>
                        <div class="urgensi-group">
                            <label class="urgensi-item"><input type="radio" name="urgensi" value="Rendah" required> Rendah</label>
                            <label class="urgensi-item"><input type="radio" name="urgensi" value="Sedang" checked required> Sedang</label>
                            <label class="urgensi-item"><input type="radio" name="urgensi" value="Tinggi" required> Tinggi</label>
                        </div>
                    </div>
                    <div class="form-action">
                        <a href="dashboard.php" class="btn-secondary" style="text-decoration: none;">Batal</a>
                        <button type="submit" class="btn-primary">Kirim Laporan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</div>

<script>
function updateFileName(input) {
    const label = document.getElementById('file-label');
    if (input.files && input.files[0]) { label.innerText = "Gambar siap diupload: " + input.files[0].name; }
    else { label.innerText = "Klik untuk memilih foto dari perangkat"; }
}
function confirmLogout() {
    if (confirm("Yakin mau keluar dari akun?")) { window.location.href = "../logout.php"; }
}
</script>
</body>
</html>