ALTER TABLE laporan
    ADD COLUMN kode_tiket VARCHAR(20) NULL UNIQUE AFTER id_laporan,
    ADD COLUMN nama_pelapor VARCHAR(100) NULL AFTER id_pelapor,
    ADD COLUMN email_pelapor VARCHAR(100) NULL AFTER nama_pelapor,
    ADD COLUMN telepon_pelapor VARCHAR(20) NULL AFTER email_pelapor,
    ADD COLUMN petugas_id INT NULL AFTER id_petugas;

ALTER TABLE petugas
    ADD COLUMN kategori_petugas VARCHAR(100) NULL AFTER divisi;

ALTER TABLE petugas
    MODIFY divisi ENUM(
        'Infrastruktur',
        'Fasilitas Umum',
        'Keamanan',
        'Kebersihan',
        'Darurat',
        'Umum',
        'Sosial',
        'Pendidikan',
        'Kesehatan'
    ) NOT NULL;

UPDATE petugas
SET kategori_petugas = divisi
WHERE kategori_petugas IS NULL OR kategori_petugas = '';

INSERT INTO kategori (nama_kategori, slug)
SELECT 'Sosial', 'sosial'
WHERE NOT EXISTS (SELECT 1 FROM kategori WHERE nama_kategori = 'Sosial');

INSERT INTO kategori (nama_kategori, slug)
SELECT 'Pendidikan', 'pendidikan'
WHERE NOT EXISTS (SELECT 1 FROM kategori WHERE nama_kategori = 'Pendidikan');

INSERT INTO kategori (nama_kategori, slug)
SELECT 'Kesehatan', 'kesehatan'
WHERE NOT EXISTS (SELECT 1 FROM kategori WHERE nama_kategori = 'Kesehatan');

ALTER TABLE laporan
    MODIFY status ENUM(
        'Menunggu Verifikasi',
        'Diverifikasi',
        'Ditugaskan',
        'Diproses',
        'Selesai',
        'Ditolak',
        'Pending',
        'Verifikasi'
    ) DEFAULT 'Menunggu Verifikasi';

UPDATE laporan
SET kode_tiket = id_laporan
WHERE kode_tiket IS NULL OR kode_tiket = '';

UPDATE laporan
SET status = 'Menunggu Verifikasi'
WHERE status = 'Pending';

UPDATE laporan
SET status = 'Ditugaskan'
WHERE status = 'Verifikasi' AND id_petugas IS NOT NULL;

UPDATE laporan
SET status = 'Diverifikasi'
WHERE status = 'Verifikasi';

UPDATE laporan
SET petugas_id = id_petugas
WHERE petugas_id IS NULL AND id_petugas IS NOT NULL;

CREATE TABLE IF NOT EXISTS riwayat_laporan (
    id_riwayat INT AUTO_INCREMENT PRIMARY KEY,
    id_laporan VARCHAR(20) NOT NULL,
    status VARCHAR(50) NOT NULL,
    catatan TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_riwayat_laporan (id_laporan)
);

INSERT INTO riwayat_laporan (id_laporan, status, catatan, created_at)
SELECT l.id_laporan, l.status, 'Data laporan yang sudah ada disinkronkan ke riwayat proses.', l.tanggal_lapor
FROM laporan l
LEFT JOIN riwayat_laporan r ON r.id_laporan = l.id_laporan
WHERE r.id_laporan IS NULL;
