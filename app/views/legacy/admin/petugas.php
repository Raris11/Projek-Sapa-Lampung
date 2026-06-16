<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once BASE_PATH . '/config/koneksi.php';
$nama = $_SESSION['nama'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_petugas'])) {
    $action_type = $_POST['action_type'];
    $id_user     = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama_input  = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $telepon     = mysqli_real_escape_string($conn, $_POST['telepon']);
    $nip         = mysqli_real_escape_string($conn, $_POST['nip']);
    $divisi      = mysqli_real_escape_string($conn, $_POST['divisi']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    if ($action_type == 'tambah') {
        $cek_mail = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek_mail) > 0) {
            echo "<script>alert('Gagal! Email tersebut sudah terdaftar di sistem.'); window.location.href='petugas.php';</script>";
            exit;
        }

        $pass_default = explode('@', $email)[0];
        $pass_hash = password_hash($pass_default, PASSWORD_DEFAULT);
        $q_user = "INSERT INTO users (nama, email, password, telepon, role, status) VALUES ('$nama_input', '$email', '$pass_hash', '$telepon', 'petugas', '$status')";
        
        if (mysqli_query($conn, $q_user)) {
            $id_user_baru = mysqli_insert_id($conn);

            $q_kode = mysqli_query($conn, "SELECT kode_petugas FROM petugas ORDER BY id_petugas DESC LIMIT 1");
            $d_kode = mysqli_fetch_assoc($q_kode);
            if ($d_kode) {
                $no_urut = substr($d_kode['kode_petugas'], -3);
                $no_urut = (int)$no_urut + 1;
                $kode_baru = "PTG-" . str_pad($no_urut, 3, "0", STR_PAD_LEFT);
            } else {
                $kode_baru = "PTG-001";
            }

            $val_nip = empty($nip) ? "NULL" : "'$nip'";
            $q_ptg = "INSERT INTO petugas (id_user, kode_petugas, nip, divisi, kategori_petugas, wilayah, rating) VALUES ('$id_user_baru', '$kode_baru', $val_nip, '$divisi', '$divisi', 'Bandar Lampung', 0.0)";
            
            if (mysqli_query($conn, $q_ptg)) {
                echo "<script>alert('Petugas $kode_baru berhasil ditambahkan!\\nPassword default: $pass_default'); window.location.href='petugas.php';</script>";
                exit;
            }
        }
    } elseif ($action_type == 'edit') {
        $up_user = "UPDATE users SET nama='$nama_input', email='$email', telepon='$telepon', status='$status' WHERE id_user='$id_user'";
        if (mysqli_query($conn, $up_user)) {
            $val_nip = empty($nip) ? "NULL" : "'$nip'";
            $up_ptg = "UPDATE petugas SET nip=$val_nip, divisi='$divisi', kategori_petugas='$divisi' WHERE id_user='$id_user'";
            
            if (mysqli_query($conn, $up_ptg)) {
                echo "<script>alert('Data personel petugas sukses diperbarui!'); window.location.href='petugas.php';</script>";
                exit;
            }
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_user_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);
    
    $d_ptg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_petugas FROM petugas WHERE id_user='$id_user_hapus'"));
    $id_ptg_chk = $d_ptg['id_petugas'] ?? 0;

    $cek_tugas = mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_petugas='$id_ptg_chk' AND status != 'Selesai'");
    if (mysqli_num_rows($cek_tugas) > 0) {
        echo "<script>alert('Gagal Menghapus! Personel ini masih mengemban tugas dinas aktif lapangan.'); window.location.href='petugas.php';</script>";
        exit;
    }

    if (mysqli_query($conn, "DELETE FROM users WHERE id_user='$id_user_hapus'")) {
        echo "<script>alert('Akun petugas berhasil dihapus dari sistem.'); window.location.href='petugas.php';</script>";
        exit;
    }
}

$c_total  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas"))['total'];
$c_aktif  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='petugas' AND status='aktif'"))['total'];
$c_non    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE role='petugas' AND status='nonaktif'"))['total'];
$c_tugas  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM laporan WHERE status != 'Selesai' AND id_petugas IS NOT NULL"))['total'];
$rate_aktif = ($c_total > 0) ? round(($c_aktif / $c_total) * 100) : 0;

$div_kebersihan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Kebersihan'"))['total'];
$div_infra      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Infrastruktur'"))['total'];
$div_fasum      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Fasilitas Umum'"))['total'];
$div_keamanan   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Keamanan'"))['total'];
$div_darurat    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Darurat'"))['total'];
$div_umum       = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Umum'"))['total'];
$div_sosial     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Sosial'"))['total'];
$div_pendidikan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Pendidikan'"))['total'];
$div_kesehatan  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM petugas WHERE divisi='Kesehatan'"))['total'];

$max_div = max($div_kebersihan, $div_infra, $div_fasum, $div_keamanan, $div_darurat, $div_umum, $div_sosial, $div_pendidikan, $div_kesehatan, 1);

$query_tabel = mysqli_query($conn, "SELECT users.*, petugas.id_petugas, petugas.kode_petugas, petugas.nip, petugas.divisi, petugas.rating,
                                    (SELECT COUNT(*) FROM laporan WHERE laporan.id_petugas = petugas.id_petugas AND laporan.status != 'Selesai') as jumlah_tugas
                                    FROM petugas 
                                    JOIN users ON petugas.id_user = users.id_user 
                                    ORDER BY petugas.id_petugas DESC");
$total_baris = mysqli_num_rows($query_tabel);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Petugas — SAPA Admin</title>
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
            max-width: 500px;
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
                <a href="laporan.php" class="sidebar-item">Laporan Masuk</a>
                <a href="tracking.php" class="sidebar-item">Tracking</a>
                <a href="petugas.php" class="sidebar-item active">Manajemen Petugas</a>
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
            <div class="topbar-title">
                <h2>Manajemen Petugas</h2>
                <p>Kelola data petugas lapangan SAPA Lampung</p>
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($nama, 0, 1)) ?></div>
                <span class="topbar-username"><?= htmlspecialchars($nama) ?></span>
            </div>
        </div>

        <div class="content-body">

            <div class="ptg-stats mb-20">
                <div class="ptg-stat">
                    <div class="ptg-stat-left">
                        <div class="ptg-stat-icon" style="background:#f1f5f9;color:#64748b">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                        <div>
                            <div class="ptg-stat-val"><?= $c_total; ?></div>
                            <div class="ptg-stat-lbl">Total Petugas</div>
                        </div>
                    </div>
                </div>

                <div class="ptg-stat">
                    <div class="ptg-stat-left">
                        <div class="ptg-stat-icon" style="background:#dcfce7;color:#16a34a">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                        </div>
                        <div>
                            <div class="ptg-stat-val" style="color:#16a34a"><?= $c_aktif; ?></div>
                            <div class="ptg-stat-lbl">Aktif</div>
                        </div>
                    </div>
                    <span class="ptg-stat-badge" style="background:#dcfce7;color:#16a34a"><?= $rate_aktif; ?>%</span>
                </div>

                <div class="ptg-stat">
                    <div class="ptg-stat-left">
                        <div class="ptg-stat-icon" style="background:#fee2e2;color:#dc2626">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        </div>
                        <div>
                            <div class="ptg-stat-val" style="color:#dc2626"><?= $c_non; ?></div>
                            <div class="ptg-stat-lbl">Nonaktif</div>
                        </div>
                    </div>
                </div>

                <div class="ptg-stat">
                    <div class="ptg-stat-left">
                        <div class="ptg-stat-icon" style="background:#fef3c7;color:#d97706">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        </div>
                        <div>
                            <div class="ptg-stat-val" style="color:#d97706"><?= $c_tugas; ?></div>
                            <div class="ptg-stat-lbl">Tugas Aktif</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-20">
                <div class="card-header">
                    <div>
                        <div class="card-title">Sebaran per Divisi</div>
                        <div class="card-subtitle"><?= $c_total; ?> petugas terdaftar</div>
                    </div>
                    <button class="table-btn" onclick="openTambahModal()" style="background:#1a56e8; color:white; font-weight:bold; padding: 10px 20px; font-size:13px;">＋ Tambah Petugas Baru</button>
                </div>
                <div class="card-body">
                    <div class="ptg-divisi" style="display:grid; grid-template-columns: 1fr 1fr; gap:15px 30px;">
                        <?php
                        $list_divisi = [
                            ['Kebersihan',   $div_kebersihan, round(($div_kebersihan/$max_div)*100), '#16a34a'],
                            ['Infrastruktur', $div_infra,      round(($div_infra/$max_div)*100),      '#1a56e8'],
                            ['Fasilitas Umum', $div_fasum,      round(($div_fasum/$max_div)*100),      '#7c3aed'],
                            ['Keamanan',       $div_keamanan,   round(($div_keamanan/$max_div)*100),   '#0e7490'],
                            ['Darurat',        $div_darurat,    round(($div_darurat/$max_div)*100),    '#dc2626'],
                            ['Sosial',         $div_sosial,     round(($div_sosial/$max_div)*100),     '#9333ea'],
                            ['Pendidikan',     $div_pendidikan, round(($div_pendidikan/$max_div)*100), '#0f766e'],
                            ['Kesehatan',      $div_kesehatan,  round(($div_kesehatan/$max_div)*100),  '#16a34a'],
                            ['Umum',           $div_umum,       round(($div_umum/$max_div)*100),       '#9ca3af'],
                        ];
                        foreach ($list_divisi as $d): ?>
                        <div class="ptg-divisi-row" style="margin:0;">
                            <div class="ptg-divisi-meta">
                                <span class="ptg-divisi-name"><?= $d[0] ?></span>
                                <span class="ptg-divisi-num"><?= $d[1] ?> petugas</span>
                            </div>
                            <div class="ptg-divisi-track">
                                <div class="ptg-divisi-fill" style="width:<?= $d[2] ?>%;background:<?= $d[3] ?>"></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Daftar Seluruh Petugas</div>
                        <div class="card-subtitle"><?= $total_baris; ?> petugas terdaftar</div>
                    </div>
                </div>

                <div class="ptg-filter-bar">
                    <input type="text" id="tabelSearch" class="ptg-input ptg-search" placeholder="Cari nama atau email..." onkeyup="filterTabelSecaraInstan()">
                    <select id="tabelFilterDivisi" class="ptg-input ptg-sel" onchange="filterTabelSecaraInstan()">
                        <option value="">Semua Divisi</option>
                        <option value="Infrastruktur">Infrastruktur</option>
                        <option value="Fasilitas Umum">Fasilitas Umum</option>
                        <option value="Keamanan">Keamanan</option>
                        <option value="Kebersihan">Kebersihan</option>
                        <option value="Darurat">Darurat</option>
                        <option value="Sosial">Sosial</option>
                        <option value="Pendidikan">Pendidikan</option>
                        <option value="Kesehatan">Kesehatan</option>
                        <option value="Umum">Umum</option>
                    </select>
                </div>

                <div class="table-wrap">
                    <table id="tabelPetugas">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Petugas</th>
                                <th>Kontak</th>
                                <th>Divisi</th>
                                <th>Status</th>
                                <th>Tugas</th>
                                <th>Rating</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($total_baris > 0) : ?>
                                <?php while($p = mysqli_fetch_assoc($query_tabel)) : 
                                    $arr_nama = explode(' ', $p['nama']);
                                    $inisial = strtoupper(substr($arr_nama[0], 0, 1) . (isset($arr_nama[1]) ? substr($arr_nama[1], 0, 1) : ''));
                                    
                                    $slug_div = 'umum';
                                    if($p['divisi'] == 'Kebersihan') $slug_div = 'kebersihan';
                                    elseif($p['divisi'] == 'Infrastruktur') $slug_div = 'infrastruktur';
                                    elseif($p['divisi'] == 'Fasilitas Umum') $slug_div = 'fasilitas';
                                    elseif($p['divisi'] == 'Keamanan') $slug_div = 'keamanan';
                                    elseif($p['divisi'] == 'Darurat') $slug_div = 'darurat';
                                    elseif($p['divisi'] == 'Sosial') $slug_div = 'sosial';
                                    elseif($p['divisi'] == 'Pendidikan') $slug_div = 'pendidikan';
                                    elseif($p['divisi'] == 'Kesehatan') $slug_div = 'kesehatan';

                                    $is_aktif = ($p['status'] == 'aktif');
                                    $rowClass = $is_aktif ? '' : 'ptg-nonaktif';
                                    $pillClass = $is_aktif ? 'pill-aktif' : 'pill-nonaktif';
                                    $pillLabel = $is_aktif ? 'Aktif' : 'Nonaktif';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td><span class="id-badge"><?= $p['kode_petugas'] ?></span></td>
                                    <td>
                                        <div class="ptg-user">
                                            <div class="ptg-av" style="background:#ede9fe; color:#6d28d9; font-weight:bold;"><?= $inisial ?></div>
                                            <span class="ptg-uname"><?= htmlspecialchars($p['nama']) ?></span>
                                        </div>
                                    </td>
                                    <td class="ptg-contact">
                                        <span><?= htmlspecialchars($p['email']) ?></span>
                                        <span><?= htmlspecialchars($p['telepon'] ?? '-') ?></span>
                                    </td>
                                    <td><span class="cat-tag cat-<?= $slug_div ?>"><?= $p['divisi'] ?></span></td>
                                    <td><span class="pill <?= $pillClass ?>"><?= $pillLabel ?></span></td>
                                    <td><span class="ptg-count <?= $p['jumlah_tugas'] == 0 ? 'ptg-count-zero' : '' ?>"><?= $p['jumlah_tugas'] ?></span></td>
                                    <td><span class="ptg-rate"><?= ($p['rating'] > 0) ? '&#9733; ' . number_format($p['rating'], 1) : '—'; ?></span></td>
                                    <td>
                                        <div class="ptg-act-wrap">
                                            <button class="ptg-act-btn ptg-act-edit" onclick='openEditModal(<?= json_encode($p); ?>)'>Edit</button>
                                            <a href="petugas.php?hapus=<?= $p['id_user']; ?>" class="ptg-act-btn ptg-act-del" style="text-decoration:none;" onclick="return confirm('Yakin ingin menghapus petugas ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="8" style="text-align:center; padding:20px; color:#64748b;">Belum ada data personel petugas lapangan di database.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="petugasModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="form-card-title" style="font-size: 18px; font-weight: 800; border-bottom: 2px solid #f0f0f5; padding-bottom: 8px; margin-bottom: 15px; color:#1a1a2e;">
            Form Manajemen Petugas
        </h3>
        
        <form action="petugas.php" method="POST">
            <input type="hidden" name="action_type" id="form-action-type" value="tambah">
            <input type="hidden" name="id_user" id="form-id-user" value="">

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label class="ptg-lbl" style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nama Lengkap *</label>
                <input type="text" name="nama" id="in-nama" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" placeholder="Nama sesuai KTP" required>
            </div>

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label class="ptg-lbl" style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Email Resmi *</label>
                <input type="email" name="email" id="in-email" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" placeholder="nama@sapa.go.id" required>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-bottom:12px;">
                <div class="ptg-fg">
                    <label class="ptg-lbl" style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nomor HP/WA *</label>
                    <input type="text" name="telepon" id="in-telepon" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" placeholder="0812-xxxx" required>
                </div>
                <div class="ptg-fg">
                    <label class="ptg-lbl" style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">NIP</label>
                    <input type="text" name="nip" id="in-nip" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" placeholder="Opsional">
                </div>
            </div>

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label class="ptg-lbl" style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Divisi Penugasan *</label>
                <select name="divisi" id="in-divisi" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" required>
                    <option value="">Pilih Divisi</option>
                    <option value="Infrastruktur">Infrastruktur</option>
                    <option value="Fasilitas Umum">Fasilitas Umum</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="Darurat">Darurat</option>
                    <option value="Sosial">Sosial</option>
                    <option value="Pendidikan">Pendidikan</option>
                    <option value="Kesehatan">Kesehatan</option>
                    <option value="Umum">Umum</option>
                </select>
            </div>

            <div class="ptg-fg" style="margin-bottom:20px;">
                <label class="ptg-lbl" style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Status Akun</label>
                <div class="ptg-radio-wrap" style="display:flex; gap:20px; margin-top:5px;">
                    <label class="ptg-radio-lbl">
                        <input type="radio" name="status" id="st-aktif" value="aktif" checked> <span>Aktif</span>
                    </label>
                    <label class="ptg-radio-lbl">
                        <input type="radio" name="status" id="st-nonaktif" value="nonaktif"> <span>Nonaktif</span>
                    </label>
                </div>
            </div>

            <button type="submit" name="proses_petugas" id="form-submit-btn" class="ptg-submit" style="width:100%; padding:12px; border-radius:6px; font-weight:bold; cursor:pointer; background:#1a56e8; color:white; border:none;"></button>
        </form>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin mau keluar dari akun?")) { window.location.href = "../logout.php"; }
}

const modal = document.getElementById('petugasModal');

// Form resets safely
function openTambahModal() {
    document.getElementById('form-card-title').innerText = "Tambah Personel Petugas Baru";
    document.getElementById('form-submit-btn').innerText = "Tambah Petugas";
    document.getElementById('form-action-type').value = "tambah";
    document.getElementById('form-id-user').value = "";
    
    document.getElementById('in-nama').value = "";
    document.getElementById('in-email').value = "";
    document.getElementById('in-telepon').value = "";
    document.getElementById('in-nip').value = "";
    document.getElementById('in-divisi').value = "";
    document.getElementById('st-aktif').checked = true;

    modal.style.display = "block";
}

function openEditModal(data) {
    document.getElementById('form-card-title').innerText = "Edit Data Petugas: " + data.kode_petugas;
    document.getElementById('form-submit-btn').innerText = "Simpan Perubahan";
    document.getElementById('form-action-type').value = "edit";
    document.getElementById('form-id-user').value = data.id_user;
    
    document.getElementById('in-nama').value = data.nama;
    document.getElementById('in-email').value = data.email;
    document.getElementById('in-telepon').value = data.telepon;
    document.getElementById('in-nip').value = data.nip ? data.nip : "";
    document.getElementById('in-divisi').value = data.divisi;
    
    if (data.status === 'aktif') {
        document.getElementById('st-aktif').checked = true;
    } else {
        document.getElementById('st-nonaktif').checked = true;
    }

    modal.style.display = "block";
}

function closeModal() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) { modal.style.display = "none"; }
}

function filterTabelSecaraInstan() {
    const searchVal = document.getElementById('tabelSearch').value.toLowerCase();
    const divisiVal = document.getElementById('tabelFilterDivisi').value.toLowerCase();
    const table     = document.getElementById('tabelPetugas');
    const tr        = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let tdNama    = tr[i].getElementsByTagName('td')[1];
        let tdKontak  = tr[i].getElementsByTagName('td')[2];
        let tdDivisi  = tr[i].getElementsByTagName('td')[3];
        
        if (tdNama && tdKontak && tdDivisi) {
            let txtNama   = tdNama.textContent || tdNama.innerText;
            let txtKontak = tdKontak.textContent || tdKontak.innerText;
            let txtDivisi = tdDivisi.textContent || tdDivisi.innerText;
            
            let matchSearch = (txtNama.toLowerCase().indexOf(searchVal) > -1 || txtKontak.toLowerCase().indexOf(searchVal) > -1);
            let matchDivisi = (divisiVal === "" || txtDivisi.toLowerCase().indexOf(divisiVal) > -1);

            if (matchSearch && matchDivisi) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}
</script>

</body>
</html>
