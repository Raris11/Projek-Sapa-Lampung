<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once BASE_PATH . '/config/koneksi.php';
$nama = $_SESSION['nama'];

// ── Ambil kategori petugas untuk dropdown filter ───────────────────────────
// Mengambil kategori_petugas, jika kosong maka menggunakan divisi.
$q_kategori_petugas_list = mysqli_query($conn, "
    SELECT DISTINCT COALESCE(NULLIF(kategori_petugas, ''), divisi) AS kategori_petugas
    FROM petugas
    WHERE COALESCE(NULLIF(kategori_petugas, ''), divisi) IS NOT NULL
      AND COALESCE(NULLIF(kategori_petugas, ''), divisi) <> ''
    ORDER BY kategori_petugas ASC
");

// ── Statistik kartu ────────────────────────────────────────────────────────
$today = date('Y-m-d');

$c_baru = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM laporan WHERE DATE(tanggal_lapor) = '$today'"
))['total'];

$c_diproses = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total FROM laporan WHERE status = 'Diproses'"
))['total'];

$c_selesai = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT id_laporan) as total 
     FROM riwayat_laporan 
     WHERE status = 'Selesai' 
       AND DATE(created_at) = '$today'"
))['total'];

$c_selesai_kemarin = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(DISTINCT id_laporan) as total 
     FROM riwayat_laporan 
     WHERE status = 'Selesai' 
       AND DATE(created_at) = DATE_SUB('$today', INTERVAL 1 DAY)"
))['total'];

$selisih_selesai = $c_selesai - $c_selesai_kemarin;

// Laporan terlambat: sudah > 7 hari sejak tanggal lapor, belum selesai
$c_terlambat = mysqli_fetch_assoc(mysqli_query(
    $conn,
    "SELECT COUNT(*) as total 
     FROM laporan 
     WHERE status NOT IN ('Selesai','Ditolak') 
       AND DATEDIFF(NOW(), tanggal_lapor) > 7"
))['total'];

// ── Build query tabel dengan filter ───────────────────────────────────────
$filter_search = isset($_GET['search'])
    ? mysqli_real_escape_string($conn, trim($_GET['search']))
    : '';

$filter_status = isset($_GET['status'])
    ? mysqli_real_escape_string($conn, trim($_GET['status']))
    : '';

$filter_kategori_petugas = isset($_GET['kategori_petugas'])
    ? mysqli_real_escape_string($conn, trim($_GET['kategori_petugas']))
    : '';

$where_parts = ["1=1"];

if ($filter_search !== '') {
    $where_parts[] = "(
        l.kode_tiket LIKE '%$filter_search%' 
        OR l.judul_laporan LIKE '%$filter_search%' 
        OR u.nama LIKE '%$filter_search%'
    )";
}

if ($filter_status !== '') {
    $where_parts[] = "l.status = '$filter_status'";
}

if ($filter_kategori_petugas !== '') {
    $where_parts[] = "COALESCE(NULLIF(p.kategori_petugas, ''), p.divisi) = '$filter_kategori_petugas'";
}

$where_sql = implode(' AND ', $where_parts);

$query_tabel = mysqli_query($conn, "
    SELECT 
        l.id_laporan,
        l.kode_tiket,
        l.judul_laporan AS judul,
        l.status,
        l.tanggal_lapor,
        u.nama AS nama_petugas,
        COALESCE(NULLIF(p.kategori_petugas, ''), p.divisi) AS kategori_petugas,
        CASE l.status
            WHEN 'Menunggu Verifikasi' THEN 10
            WHEN 'Diverifikasi'        THEN 30
            WHEN 'Ditugaskan'          THEN 50
            WHEN 'Diproses'            THEN 75
            WHEN 'Selesai'             THEN 100
            ELSE 0
        END AS progress_pct,
        CASE 
            WHEN l.status NOT IN ('Selesai','Ditolak') 
             AND DATEDIFF(NOW(), l.tanggal_lapor) > 7 
            THEN 1 
            ELSE 0 
        END AS terlambat
    FROM laporan l
    LEFT JOIN petugas p ON l.id_petugas = p.id_petugas
    LEFT JOIN users u ON p.id_user = u.id_user
    WHERE $where_sql
    ORDER BY l.id_laporan DESC
");

// ── Export CSV ─────────────────────────────────────────────────────────────
if (isset($_GET['export']) && $_GET['export'] == '1') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="tracking-laporan-' . date('Ymd-His') . '.csv"');

    $out = fopen('php://output', 'w');
    fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

    fputcsv($out, [
        'ID / Kode Tiket',
        'Judul Laporan',
        'Petugas',
        'Kategori Petugas',
        'Status',
        'Progress (%)',
        'Tanggal Lapor',
        'Terlambat'
    ]);

    $q_exp = mysqli_query($conn, "
        SELECT 
            l.id_laporan,
            l.kode_tiket,
            l.judul_laporan AS judul,
            l.status,
            l.tanggal_lapor,
            u.nama AS nama_petugas,
            COALESCE(NULLIF(p.kategori_petugas, ''), p.divisi) AS kategori_petugas,
            CASE l.status
                WHEN 'Menunggu Verifikasi' THEN 10
                WHEN 'Diverifikasi'        THEN 30
                WHEN 'Ditugaskan'          THEN 50
                WHEN 'Diproses'            THEN 75
                WHEN 'Selesai'             THEN 100
                ELSE 0
            END AS progress_pct,
            CASE 
                WHEN l.status NOT IN ('Selesai','Ditolak') 
                 AND DATEDIFF(NOW(), l.tanggal_lapor) > 7 
                THEN 1 
                ELSE 0 
            END AS terlambat
        FROM laporan l
        LEFT JOIN petugas p ON l.id_petugas = p.id_petugas
        LEFT JOIN users u ON p.id_user = u.id_user
        WHERE $where_sql
        ORDER BY l.id_laporan DESC
    ");

    while ($row = mysqli_fetch_assoc($q_exp)) {
        $kode = $row['kode_tiket'] ?: 'RPT-' . str_pad($row['id_laporan'], 4, '0', STR_PAD_LEFT);

        fputcsv($out, [
            $kode,
            $row['judul'],
            $row['nama_petugas'] ?? '-',
            $row['kategori_petugas'] ?? '-',
            $row['status'],
            $row['progress_pct'] . '%',
            $row['tanggal_lapor'],
            $row['terlambat'] ? 'Ya' : 'Tidak'
        ]);
    }

    fclose($out);
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Laporan — SAPA Lampung</title>
    <link rel="stylesheet" href="../assets/css/global.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
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
                <a href="tracking.php" class="sidebar-item active">Tracking</a>
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
            <div class="topbar-title">
                <h2>Tracking Laporan</h2>
                <p>Pantau seluruh progres laporan masyarakat</p>
            </div>

            <div class="topbar-user">
                <div class="topbar-avatar"><?= strtoupper(substr($nama, 0, 1)) ?></div>
                <span class="topbar-username"><?= htmlspecialchars($nama) ?></span>
            </div>
        </div>

        <div class="content-body">

            <!-- ═══ KARTU STATISTIK ═══ -->
            <div class="stats-row">
                <div class="stat-box">
                    <div class="stat-box-label">Laporan Baru</div>
                    <div class="stat-box-num"><?= $c_baru ?></div>
                    <div class="stat-box-change">Hari ini</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Sedang Diproses</div>
                    <div class="stat-box-num"><?= $c_diproses ?></div>
                    <div class="stat-box-change">Aktif di lapangan</div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Selesai Hari Ini</div>
                    <div class="stat-box-num"><?= $c_selesai ?></div>
                    <div class="stat-box-change <?= $selisih_selesai >= 0 ? 'up' : 'down' ?>">
                        <?= ($selisih_selesai >= 0 ? '+' : '') . $selisih_selesai ?> dari kemarin
                    </div>
                </div>

                <div class="stat-box">
                    <div class="stat-box-label">Terlambat</div>
                    <div class="stat-box-num"><?= $c_terlambat ?></div>
                    <div class="stat-box-change down">Perlu tindak lanjut</div>
                </div>
            </div>

            <!-- ═══ FILTER BAR ═══ -->
            <div class="card mb-20">
                <div class="filter-bar">

                    <input
                        type="text"
                        id="trackSearch"
                        class="form-input"
                        placeholder="Cari ID laporan, judul, atau petugas..."
                        value="<?= htmlspecialchars($filter_search) ?>"
                        onkeyup="filterTabelTracking()"
                    >

                    <select id="trackFilterStatus" class="form-select" onchange="filterTabelTracking()">
                        <option value="">Semua Status</option>
                        <option value="Menunggu Verifikasi" <?= $filter_status == 'Menunggu Verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                        <option value="Diverifikasi" <?= $filter_status == 'Diverifikasi' ? 'selected' : '' ?>>Diverifikasi</option>
                        <option value="Ditugaskan" <?= $filter_status == 'Ditugaskan' ? 'selected' : '' ?>>Ditugaskan</option>
                        <option value="Diproses" <?= $filter_status == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                        <option value="Selesai" <?= $filter_status == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="Ditolak" <?= $filter_status == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>

                    <select id="trackFilterKategoriPetugas" class="form-select" onchange="filterTabelTracking()">
                        <option value="">Semua Kategori Petugas</option>
                        <?php while ($kp = mysqli_fetch_assoc($q_kategori_petugas_list)): ?>
                            <option
                                value="<?= htmlspecialchars($kp['kategori_petugas']) ?>"
                                <?= $filter_kategori_petugas == $kp['kategori_petugas'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($kp['kategori_petugas']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>

                </div>
            </div>

            <!-- ═══ TABEL LAPORAN ═══ -->
            <div class="card">
                <div class="card-header">
                    <div>
                        <div class="card-title">Semua Laporan</div>
                        <div class="card-subtitle">Monitoring progres laporan masyarakat</div>
                    </div>

                    <a
                        href="tracking.php?export=1&search=<?= urlencode($filter_search) ?>&status=<?= urlencode($filter_status) ?>&kategori_petugas=<?= urlencode($filter_kategori_petugas) ?>"
                        id="btnExport"
                        class="btn btn-outline"
                        style="text-decoration:none;"
                    >
                        ⬇ Export CSV
                    </a>
                </div>

                <div class="table-wrap">
                    <table id="tabelTracking">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Judul</th>
                                <th>Petugas</th>
                                <th>Status</th>
                                <th>Progress</th>
                            </tr>
                        </thead>

                        <tbody>
                        <?php
                        $total_rows = mysqli_num_rows($query_tabel);

                        if ($total_rows > 0):
                            while ($row = mysqli_fetch_assoc($query_tabel)):
                                $kode = $row['kode_tiket'] ?: 'RPT-' . str_pad($row['id_laporan'], 4, '0', STR_PAD_LEFT);
                                $pct  = (int) $row['progress_pct'];
                                $terlambat = (bool) $row['terlambat'];

                                $pill_map = [
                                    'Menunggu Verifikasi' => 'pill-pending',
                                    'Diverifikasi'        => 'pill-verifikasi',
                                    'Ditugaskan'          => 'pill-verifikasi',
                                    'Diproses'            => 'pill-diproses',
                                    'Selesai'             => 'pill-selesai',
                                    'Ditolak'             => 'pill-pending',
                                ];

                                $pill_class = $pill_map[$row['status']] ?? 'pill-pending';
                                $bar_pct = $pct > 0 ? $pct : 5;
                                $text_extra = '<div class="progress-text">' . $pct . '%</div>';
                        ?>
                            <tr
                                data-id="<?= strtolower($kode) ?>"
                                data-judul="<?= strtolower(htmlspecialchars($row['judul'])) ?>"
                                data-petugas="<?= strtolower(htmlspecialchars($row['nama_petugas'] ?? '')) ?>"
                                data-kategori-petugas="<?= strtolower(htmlspecialchars($row['kategori_petugas'] ?? '')) ?>"
                                data-status="<?= htmlspecialchars($row['status']) ?>"
                            >
                                <td>
                                    <span class="id-badge"><?= htmlspecialchars($kode) ?></span>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['judul']) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($row['nama_petugas'] ?? '—') ?>
                                    <div style="font-size:12px; color:#64748b; margin-top:4px;">
                                        <?= htmlspecialchars($row['kategori_petugas'] ?? '—') ?>
                                    </div>
                                </td>

                                <td>
                                    <span class="pill <?= $pill_class ?>">
                                        <?= htmlspecialchars($row['status']) ?>
                                    </span>
                                </td>

                                <td>
                                    <div style="width:120px;height:8px;background:#e4e7ef;border-radius:999px;overflow:hidden;margin-bottom:5px;">
                                        <div style="height:100%;border-radius:999px;background:#dc2626;width:<?= $bar_pct ?>%;"></div>
                                    </div>
                                    <?= $text_extra ?>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        else:
                        ?>
                            <tr id="noResultRow">
                                <td colspan="5" style="text-align:center; padding:20px; color:#64748b;">
                                    Tidak ada laporan yang ditemukan.
                                </td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
    </main>
</div>

<script>
function confirmLogout() {
    if (confirm("Yakin mau keluar dari akun?")) {
        window.location.href = "../logout.php";
    }
}

function filterTabelTracking() {
    const searchVal = document.getElementById('trackSearch').value.toLowerCase().trim();
    const statusVal = document.getElementById('trackFilterStatus').value.toLowerCase();
    const kategoriPetugasVal = document.getElementById('trackFilterKategoriPetugas').value.toLowerCase();

    const table = document.getElementById('tabelTracking');
    const rows = table.getElementsByTagName('tr');

    let visibleCount = 0;

    const oldEmpty = document.getElementById('noResultRow');
    if (oldEmpty) oldEmpty.remove();

    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];

        const id = (row.dataset.id || '').toLowerCase();
        const judul = (row.dataset.judul || '').toLowerCase();
        const petugas = (row.dataset.petugas || '').toLowerCase();
        const kategoriPetugas = (row.dataset.kategoriPetugas || '').toLowerCase();
        const status = (row.dataset.status || '').toLowerCase();

        const matchSearch =
            searchVal === '' ||
            id.includes(searchVal) ||
            judul.includes(searchVal) ||
            petugas.includes(searchVal);

        const matchStatus =
            statusVal === '' ||
            status === statusVal;

        const matchKategoriPetugas =
            kategoriPetugasVal === '' ||
            kategoriPetugas === kategoriPetugasVal;

        if (matchSearch && matchStatus && matchKategoriPetugas) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }

    if (visibleCount === 0) {
        const tbody = table.getElementsByTagName('tbody')[0];
        const emptyRow = tbody.insertRow();
        emptyRow.id = 'noResultRow';
        emptyRow.innerHTML = '<td colspan="5" style="text-align:center; padding:20px; color:#64748b;">Tidak ada laporan yang cocok dengan filter.</td>';
    }

    const exportBtn = document.getElementById('btnExport');

    const params = new URLSearchParams({
        export: '1',
        search: document.getElementById('trackSearch').value,
        status: document.getElementById('trackFilterStatus').value,
        kategori_petugas: document.getElementById('trackFilterKategoriPetugas').value
    });

    exportBtn.href = 'tracking.php?' + params.toString();
}
</script>

</body>
</html>