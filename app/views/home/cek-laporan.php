<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Laporan - SAPA Lampung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        body { background: #f7f8fb; }
        .check-page { min-height: 100vh; padding: 120px 5% 60px; }
        .check-wrap { max-width: 980px; margin: 0 auto; }
        .check-header { margin-bottom: 28px; }
        .check-header h1 { font-family: 'Lora', serif; font-size: 42px; margin: 0 0 10px; }
        .check-header p { color: #64748b; max-width: 620px; line-height: 1.7; }
        .check-form { display: flex; gap: 12px; background: #fff; border: 1px solid #e5e7eb; padding: 14px; border-radius: 14px; box-shadow: 0 10px 30px rgba(15,23,42,.06); margin-bottom: 24px; }
        .check-input { flex: 1; border: 1px solid #d8dee6; border-radius: 10px; padding: 13px 14px; font: inherit; }
        .check-result { background: #fff; border: 1px solid #e5e7eb; border-radius: 14px; padding: 24px; box-shadow: 0 10px 30px rgba(15,23,42,.06); }
        .result-top { display: flex; justify-content: space-between; gap: 16px; border-bottom: 1px solid #eef2f7; padding-bottom: 18px; margin-bottom: 18px; }
        .result-id { color: #c62828; font-weight: 800; font-size: 13px; letter-spacing: .5px; }
        .result-title { font-size: 24px; font-weight: 800; margin-top: 6px; }
        .status-pill { align-self: flex-start; border-radius: 999px; padding: 8px 12px; font-weight: 800; font-size: 12px; color: #0f5132; background: #dcfce7; white-space: nowrap; }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 24px; }
        .info-box { border: 1px solid #eef2f7; border-radius: 10px; padding: 14px; }
        .info-label { color: #64748b; font-size: 12px; font-weight: 700; margin-bottom: 5px; }
        .info-value { font-weight: 800; color: #111827; }
        .timeline { display: grid; gap: 14px; }
        .timeline-item { border-left: 3px solid #c62828; padding-left: 14px; }
        .timeline-item strong { display: block; color: #111827; }
        .timeline-item span { color: #64748b; font-size: 12px; }
        .timeline-item p { margin: 4px 0 0; color: #475569; line-height: 1.6; }
        .empty-state { text-align: center; color: #64748b; }
        @media (max-width: 720px) {
            .check-form, .result-top { flex-direction: column; }
            .info-grid { grid-template-columns: 1fr; }
            .check-header h1 { font-size: 34px; }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <div class="nav-logo" style="background: transparent; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                <img src="assets/img/logo-sapa.jpeg" alt="Logo SAPA" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
            </div>
            <div class="nav-brand-text"><span>SAPA</span> Lampung</div>
        </a>
        <div class="nav-links">
            <a href="index.php#tentang">Tentang</a>
            <a href="index.php#layanan">Layanan</a>
            <a href="cek-laporan.php">Cek Laporan</a>
        </div>
        <div class="nav-actions">
            <a href="buat-laporan.php" class="btn btn-red">Buat Laporan</a>
        </div>
    </nav>

    <main class="check-page">
        <div class="check-wrap">
            <div class="check-header">
                <span class="section-tag">Pelacakan Publik</span>
                <h1>Cek Status Laporan</h1>
                <p>Masukkan nomor tiket untuk melihat status terkini dan riwayat proses tanpa perlu login.</p>
            </div>

            <form class="check-form" method="GET" action="cek-laporan.php">
                <input class="check-input" type="text" name="ticket" value="<?= htmlspecialchars($ticket); ?>" placeholder="Contoh: SAPA-<?= date('Y'); ?>-0001" required>
                <button class="btn btn-red" type="submit">Cek Laporan</button>
            </form>

            <?php if ($ticket !== '' && !$laporan): ?>
                <div class="check-result empty-state">
                    Nomor tiket <strong><?= htmlspecialchars($ticket); ?></strong> tidak ditemukan. Pastikan nomor tiket sesuai dengan bukti pengiriman laporan.
                </div>
            <?php elseif ($laporan): ?>
                <section class="check-result">
                    <div class="result-top">
                        <div>
                            <div class="result-id"><?= htmlspecialchars($laporan['kode_tiket'] ?: $laporan['id_laporan']); ?></div>
                            <div class="result-title"><?= htmlspecialchars($laporan['judul_laporan']); ?></div>
                        </div>
                        <span class="status-pill"><?= htmlspecialchars($laporan['status']); ?></span>
                    </div>

                    <div class="info-grid">
                        <div class="info-box">
                            <div class="info-label">Kategori</div>
                            <div class="info-value"><?= htmlspecialchars($laporan['nama_kategori']); ?></div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Tanggal Laporan</div>
                            <div class="info-value"><?= date('d M Y H:i', strtotime($laporan['tanggal_lapor'])); ?></div>
                        </div>
                        <div class="info-box">
                            <div class="info-label">Status Saat Ini</div>
                            <div class="info-value"><?= htmlspecialchars($laporan['status']); ?></div>
                        </div>
                    </div>

                    <h3 style="margin:0 0 14px; font-size:18px;">Riwayat Proses</h3>
                    <div class="timeline">
                        <?php if (count($riwayat) > 0): ?>
                            <?php foreach ($riwayat as $item): ?>
                                <div class="timeline-item">
                                    <strong><?= htmlspecialchars($item['status']); ?></strong>
                                    <span><?= date('d M Y H:i', strtotime($item['created_at'])); ?></span>
                                    <p><?= htmlspecialchars($item['catatan']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="timeline-item">
                                <strong><?= htmlspecialchars($laporan['status']); ?></strong>
                                <span><?= date('d M Y H:i', strtotime($laporan['tanggal_lapor'])); ?></span>
                                <p>Laporan sudah tercatat di sistem SAPA Lampung.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
