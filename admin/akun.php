<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

include '../config/koneksi.php';
$nama_admin = $_SESSION['nama'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proses_akun'])) {
    $action_type = $_POST['action_type'];
    $id_user     = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama        = mysqli_real_escape_string($conn, $_POST['nama']);
    $email       = mysqli_real_escape_string($conn, $_POST['email']);
    $password    = mysqli_real_escape_string($conn, $_POST['password']);
    $telepon     = mysqli_real_escape_string($conn, $_POST['telepon']);
    $divisi      = mysqli_real_escape_string($conn, $_POST['divisi']);
    $status      = mysqli_real_escape_string($conn, $_POST['status']);

    if ($action_type == 'tambah') {
        $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email='$email'");
        if (mysqli_num_rows($cek_email) > 0) {
            echo "<script>alert('Gagal! Email sudah terdaftar.'); window.location.href='akun.php';</script>";
            exit;
        }

        $query_user = "INSERT INTO users (nama, email, password, telepon, role, status) VALUES ('$nama', '$email', '$password', '$telepon', 'petugas', '$status')";
        if (mysqli_query($conn, $query_user)) {
            $id_user_baru = mysqli_insert_id($conn);

            $query_kode = mysqli_query($conn, "SELECT kode_petugas FROM petugas ORDER BY id_petugas DESC LIMIT 1");
            $data_kode  = mysqli_fetch_assoc($query_kode);
            if ($data_kode) {
                $no_urut = substr($data_kode['kode_petugas'], -3);
                $no_urut = (int)$no_urut + 1;
                $kode_baru = "PTG-" . str_pad($no_urut, 3, "0", STR_PAD_LEFT);
            } else {
                $kode_baru = "PTG-001";
            }

            $query_petugas = "INSERT INTO petugas (id_user, kode_petugas, divisi, wilayah, rating) VALUES ('$id_user_baru', '$kode_baru', '$divisi', 'Bandar Lampung', 0.0)";
            if (mysqli_query($conn, $query_petugas)) {
                echo "<script>alert('Akun petugas berhasil dibuat!'); window.location.href='akun.php';</script>";
                exit;
            }
        }
    } elseif ($action_type == 'edit') {
        $query_update = "UPDATE users SET nama='$nama', email='$email', password='$password', telepon='$telepon', status='$status' WHERE id_user='$id_user'";
        if (mysqli_query($conn, $query_update)) {
            mysqli_query($conn, "UPDATE petugas SET divisi='$divisi' WHERE id_user='$id_user'");
            echo "<script>alert('Akun petugas berhasil diperbarui!'); window.location.href='akun.php';</script>";
            exit;
        }
    }
}

if (isset($_GET['hapus'])) {
    $id_user_hapus = mysqli_real_escape_string($conn, $_GET['hapus']);

    $data_ptg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT id_petugas FROM petugas WHERE id_user='$id_user_hapus'"));
    $id_ptg_chk = $data_ptg['id_petugas'] ?? 0;

    $cek_tugas = mysqli_query($conn, "SELECT id_laporan FROM laporan WHERE id_petugas='$id_ptg_chk' AND status != 'Selesai'");
    if (mysqli_num_rows($cek_tugas) > 0) {
        echo "<script>alert('Gagal! Petugas ini masih memiliki tugas aktif di lapangan.'); window.location.href='akun.php';</script>";
        exit;
    }

    if (mysqli_query($conn, "DELETE FROM users WHERE id_user='$id_user_hapus'")) {
        echo "<script>alert('Akun petugas berhasil dihapus!'); window.location.href='akun.php';</script>";
        exit;
    }
}

$query_tabel = mysqli_query($conn, "SELECT users.*, petugas.kode_petugas, petugas.divisi FROM users 
                                    JOIN petugas ON users.id_user = petugas.id_user 
                                    WHERE users.role = 'petugas' 
                                    ORDER BY users.id_user DESC");
$total_baris = mysqli_num_rows($query_tabel);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Akun Petugas — SAPA Admin</title>
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
                <a href="statistik.php" class="sidebar-item">Statistik</a>
                <a href="petugas.php" class="sidebar-item">Manajemen Petugas</a>
                <a href="akun.php" class="sidebar-item active">Kelola Akun</a>
            </nav>
        </div>
        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="sidebar-avatar"><?= strtoupper(substr($nama_admin, 0, 1)) ?></div>
                <div>
                    <div class="sidebar-user-name"><?= htmlspecialchars($nama_admin) ?></div>
                    <div class="sidebar-user-role">Administrator</div>
                </div>
            </div>
            <a href="#" class="sidebar-logout" onclick="confirmLogout()">Keluar</a>
        </div>
    </aside>

    <main class="main-content">

        <div class="topbar">
            <div class="topbar-title">
                <h2>Kelola Akun Petugas</h2>
                <p>Manajemen hak akses masuk sistem untuk personel lapangan</p>
            </div>
            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($nama_admin, 0, 1)) ?></div>
                <span class="topbar-username"><?= htmlspecialchars($nama_admin) ?></span>
            </div>
        </div>

        <div class="content-body">
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Daftar Akun Petugas</div>
                        <div class="card-subtitle">Total: <?= $total_baris; ?> akun terdaftar</div>
                    </div>
                    <button class="table-btn" onclick="openTambahModal()" style="background:#1a56e8; color:white; font-weight:bold; padding: 10px 20px; font-size:13px;">＋ Buat Akun Petugas</button>
                </div>

                <div class="ptg-filter-bar">
                    <input type="text" id="tabelSearch" class="ptg-input ptg-search" placeholder="Cari nama, kode, atau email..." onkeyup="filterTabelSecaraInstan()">
                </div>

                <div class="table-wrap">
                    <table id="tabelAkun">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Password</th>
                                <th>Telepon</th>
                                <th>Divisi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if($total_baris > 0) : ?>
                                <?php while($row = mysqli_fetch_assoc($query_tabel)) : 
                                    $is_aktif = ($row['status'] == 'aktif');
                                    $rowClass = $is_aktif ? '' : 'ptg-nonaktif';
                                    $pillClass = $is_aktif ? 'pill-aktif' : 'pill-nonaktif';
                                    $pillLabel = $is_aktif ? 'Aktif' : 'Nonaktif';
                                ?>
                                <tr class="<?= $rowClass ?>">
                                    <td><span class="id-badge"><?= $row['kode_petugas']; ?></span></td>
                                    <td><strong><?= htmlspecialchars($row['nama']); ?></strong></td>
                                    <td><?= htmlspecialchars($row['email']); ?></td>
                                    <td><code><?= htmlspecialchars($row['password']); ?></code></td>
                                    <td><?= htmlspecialchars($row['telepon']); ?></td>
                                    <td><span class="cat-tag"><?= $row['divisi']; ?></span></td>
                                    <td><span class="pill <?= $pillClass; ?>"><?= $pillLabel; ?></span></td>
                                    <td>
                                        <div class="ptg-act-wrap">
                                            <button class="ptg-act-btn ptg-act-edit" onclick='openEditModal(<?= json_encode($row); ?>)'>Edit</button>
                                            <a href="akun.php?hapus=<?= $row['id_user']; ?>" class="ptg-act-btn ptg-act-del" style="text-decoration:none;" onclick="return confirm('Yakin ingin menghapus akun petugas ini?')">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else : ?>
                                <tr><td colspan="8" style="text-align:center; padding:20px; color:#64748b;">Tidak ada data akun petugas.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="akunModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3 id="modal-title" style="font-size: 18px; font-weight: 800; border-bottom: 2px solid #f0f0f5; padding-bottom: 8px; margin-bottom: 15px; color:#1a1a2e;">
            Form Akun Petugas
        </h3>
        
        <form action="akun.php" method="POST">
            <input type="hidden" name="action_type" id="form-action-type" value="tambah">
            <input type="hidden" name="id_user" id="form-id-user" value="">

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nama Lengkap *</label>
                <input type="text" name="nama" id="in-nama" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" required>
            </div>

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Email Login *</label>
                <input type="email" name="email" id="in-email" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" required>
            </div>

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Password Sistem *</label>
                <input type="text" name="password" id="in-password" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" required>
            </div>

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Nomor Telepon *</label>
                <input type="text" name="telepon" id="in-telepon" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" required>
            </div>

            <div class="ptg-fg" style="margin-bottom:12px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Divisi Lapangan *</label>
                <select name="divisi" id="in-divisi" class="ptg-input" style="width:100%; padding:10px; border-radius:6px; border:1px solid #cbd5e1;" required>
                    <option value="">Pilih Divisi</option>
                    <option value="Infrastruktur">Infrastruktur</option>
                    <option value="Fasilitas Umum">Fasilitas Umum</option>
                    <option value="Keamanan">Keamanan</option>
                    <option value="Kebersihan">Kebersihan</option>
                    <option value="Darurat">Darurat</option>
                    <option value="Umum">Umum</option>
                </select>
            </div>

            <div class="ptg-fg" style="margin-bottom:20px;">
                <label style="display:block; font-size:12px; font-weight:bold; margin-bottom:5px;">Status Akun</label>
                <div style="display:flex; gap:20px; margin-top:5px;">
                    <label><input type="radio" name="status" id="st-aktif" value="aktif" checked> <span>Aktif</span></label>
                    <label><input type="radio" name="status" id="st-nonaktif" value="nonaktif"> <span>Nonaktif</span></label>
                </div>
            </div>

            <button type="submit" name="proses_akun" id="form-submit-btn" style="width:100%; padding:12px; border-radius:6px; font-weight:bold; cursor:pointer; background:#1a56e8; color:white; border:none;"></button>
        </form>
    </div>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin mau keluar dari akun?")) { window.location.href = "../logout.php"; }
}

const modal = document.getElementById('akunModal');

function openTambahModal() {
    document.getElementById('modal-title').innerText = "Buat Akun Petugas Baru";
    document.getElementById('form-submit-btn').innerText = "Buat Akun";
    document.getElementById('form-action-type').value = "tambah";
    document.getElementById('form-id-user').value = "";
    
    document.getElementById('in-nama').value = "";
    document.getElementById('in-email').value = "";
    document.getElementById('in-password').value = "";
    document.getElementById('in-telepon').value = "";
    document.getElementById('in-divisi').value = "";
    document.getElementById('st-aktif').checked = true;

    modal.style.display = "block";
}

function openEditModal(data) {
    document.getElementById('modal-title').innerText = "Edit Akun Petugas: " + data.kode_petugas;
    document.getElementById('form-submit-btn').innerText = "Simpan Perubahan";
    document.getElementById('form-action-type').value = "edit";
    document.getElementById('form-id-user').value = data.id_user;
    
    document.getElementById('in-nama').value = data.nama;
    document.getElementById('in-email').value = data.email;
    document.getElementById('in-password').value = data.password;
    document.getElementById('in-telepon').value = data.telepon;
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
    const table     = document.getElementById('tabelAkun');
    const tr        = table.getElementsByTagName('tr');

    for (let i = 1; i < tr.length; i++) {
        let tdKode   = tr[i].getElementsByTagName('td')[0];
        let tdNama   = tr[i].getElementsByTagName('td')[1];
        let tdEmail  = tr[i].getElementsByTagName('td')[2];
        
        if (tdKode && tdNama && tdEmail) {
            let txtKode  = tdKode.textContent || tdKode.innerText;
            let txtNama  = tdNama.textContent || tdNama.innerText;
            let txtEmail = tdEmail.textContent || tdEmail.innerText;
            
            if (txtKode.toLowerCase().indexOf(searchVal) > -1 || txtNama.toLowerCase().indexOf(searchVal) > -1 || txtEmail.toLowerCase().indexOf(searchVal) > -1) {
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