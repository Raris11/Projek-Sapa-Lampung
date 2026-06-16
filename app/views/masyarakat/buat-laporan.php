<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Laporan - SAPA Lampung</title>
    <link rel="stylesheet" href="<?= asset('css/global.css'); ?>">
    <link rel="stylesheet" href="<?= asset('css/masyarakat.css'); ?>">
    <style>
        .text-danger { color: #dc2626; font-weight: bold; }
        .file-input-hidden { display: none; }
        .sidebar-public { background: linear-gradient(160deg, #c62828 0%, #7b0000 100%) !important; }
        .upload-box { cursor: pointer; transition: background 0.2s ease; }
        .upload-box:hover { background-color: #f8fafc; border-color: #c62828; }
    </style>
</head>
<body>
<div class="dashboard-layout">
    <aside class="sidebar <?= (!$is_logged_in) ? 'sidebar-public' : ''; ?>">
        <div>
            <div class="sidebar-brand">
                <div class="sidebar-logo">S</div>
                <div>
                    <div class="sidebar-brand-name">SAPA</div>
                    <div class="sidebar-brand-sub">Portal Masyarakat</div>
                </div>
            </div>
            <nav class="sidebar-nav">
                <a href="<?= url('/masyarakat/dashboard'); ?>" class="sidebar-item">&ensp;Dashboard</a>
                <a href="<?= url('/masyarakat/buat-laporan'); ?>" class="sidebar-item active">&ensp;Buat Laporan</a>
                <a href="<?= url('/masyarakat/riwayat'); ?>" class="sidebar-item">&ensp;Riwayat Laporan</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($nama_user, 0, 1)); ?></div>
                <div>
                    <div class="sidebar-user-name"><?= htmlspecialchars($nama_user); ?></div>
                    <div class="sidebar-user-role">Masyarakat</div>
                </div>
            </div>
            <a href="<?= $is_logged_in ? url('/logout') : url('/'); ?>" class="sidebar-logout"><?= $is_logged_in ? 'Keluar' : 'Kembali ke Beranda'; ?></a>
        </div>
    </aside>

    <main class="main-content">
        <div class="topbar">
            <div>
                <h1 class="page-title">Buat Laporan Publik</h1>
                <p class="page-subtitle">Laporkan keluhan Anda secara langsung <?= !$is_logged_in ? 'tanpa perlu masuk akun' : ''; ?></p>
            </div>
        </div>

        <div class="content-body">
            <div class="form-card">
                <div class="form-header">
                    <h2>Form Laporan Pengaduan</h2>
                    <p>Isi data laporan dengan benar agar mudah diverifikasi oleh Admin.</p>
                    <div class="card-subtitle"><span class="text-danger">*</span> Kolom wajib diisi</div>
                </div>

                <form action="<?= url('/masyarakat/laporan'); ?>" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nama Pelapor <span style="color:#64748b; font-weight:600;">(opsional)</span></label>
                        <input type="text" name="nama_pelapor" class="form-input" placeholder="Boleh dikosongkan untuk laporan anonim">
                    </div>
                    <div class="form-group">
                        <label>Email <span style="color:#64748b; font-weight:600;">(opsional)</span></label>
                        <input type="email" name="email_pelapor" class="form-input" placeholder="Contoh: nama@email.com">
                    </div>
                    <div class="form-group">
                        <label>Nomor HP <span style="color:#64748b; font-weight:600;">(opsional)</span></label>
                        <input type="tel" name="telepon_pelapor" class="form-input" placeholder="Contoh: 08xxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label>Judul Laporan <span class="text-danger">*</span></label>
                        <input type="text" name="judul_laporan" class="form-input" placeholder="Contoh: Jalan berlubang parah di daerah Sukarame" required>
                    </div>
                    <div class="form-group">
                        <label>Kategori Laporan <span class="text-danger">*</span></label>
                        <select name="id_kategori" class="form-input" required>
                            <option value="">-- Pilih Kategori --</option>
                            <?php while ($query_kat && $kat = mysqli_fetch_assoc($query_kat)) : ?>
                                <option value="<?= htmlspecialchars($kat['id_kategori']); ?>"><?= htmlspecialchars($kat['nama_kategori']); ?></option>
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
                        <div class="upload-box" onclick="document.getElementById('foto_bukti').click();" style="padding: 30px; border: 2px dashed #cbd5e1; border-radius: 8px; text-align:center;">
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
                        <a href="<?= url('/masyarakat/dashboard'); ?>" class="btn-secondary" style="text-decoration: none;">Batal</a>
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
    label.innerText = input.files && input.files[0] ? 'Gambar siap diupload: ' + input.files[0].name : 'Klik untuk memilih foto dari perangkat';
}
</script>
</body>
</html>
