<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAPA Lampung — Sistem Aspirasi dan Pelaporan</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Lora:wght@400;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/navbar.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
</head>
<body>

    <nav class="navbar">
        <a href="index.php" class="nav-brand">
            <div class="nav-logo" style="background: transparent; display: flex; align-items: center; justify-content: center; width: 40px; height: 40px;">
                <img src="assets/img/logo-sapa.jpeg" alt="Logo SAPA" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
            </div>
            <div class="nav-brand-text">
                <span>SAPA</span> Lampung
            </div>
        </a>

        <div class="nav-links">
            <a href="#tentang">Tentang</a>
            <a href="#layanan">Layanan</a>
            <a href="#cara-kerja">Cara Kerja</a>
            <a href="#kontak">Kontak</a>
        </div>

        <div class="nav-actions">
            <a href="login.php" class="btn btn-outline">Masuk</a>
            <a href="masyarakat/buat-laporan.php" class="btn btn-red">Buat Laporan</a>
        </div>
    </nav>

    <section class="hero">
        <div class="hero-left">
            <span class="hero-badge">Portal Layanan Masyarakat Lampung</span>
            <h1>Sistem <em>Aspirasi</em> dan Pelaporan Masyarakat Lampung</h1>
            <p class="hero-desc">
                SAPA Lampung merupakan platform digital untuk membantu masyarakat menyampaikan laporan, pengaduan, dan aspirasi kepada pemerintah daerah secara cepat, transparan, dan terstruktur.
            </p>

            <div class="hero-actions">
                <a href="masyarakat/buat-laporan.php" class="btn btn-red btn-lg">Buat Laporan</a>
                <a href="#layanan" class="btn btn-outline btn-lg">Pelajari Layanan</a>
            </div>

            <div class="hero-stats">
                <div class="stat-item">
                    <div class="stat-num">15</div>
                    <div class="stat-label">Kabupaten/Kota</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">240+</div>
                    <div class="stat-label">Petugas Aktif</div>
                </div>
                <div class="stat-item">
                    <div class="stat-num">24 Jam</div>
                    <div class="stat-label">Layanan Online</div>
                </div>
            </div>
        </div>

        <div class="hero-right">
            <img src="assets/img/banner/siger.png" alt="Menara Siger Lampung">
            <div class="hero-overlay"></div>
        </div>
    </section>

    <section id="tentang" class="about">
        <div class="section">
            <div class="about-grid">
                <div>
                    <span class="section-tag">Tentang SAPA</span>
                    <h2 class="section-title">Pelayanan Publik yang Lebih Responsif</h2>
                    <p class="section-subtitle">
                        SAPA Lampung hadir sebagai media penghubung antara masyarakat dan pemerintah daerah dalam meningkatkan kualitas pelayanan publik.
                    </p>

                    <div class="about-features">
                        <div class="about-feat">
                            <div class="feat-icon">01</div>
                            <div>
                                <div class="feat-title">Pelayanan Cepat</div>
                                <div class="feat-desc">Laporan diteruskan kepada petugas sesuai bidang terkait.</div>
                            </div>
                        </div>

                        <div class="about-feat">
                            <div class="feat-icon">02</div>
                            <div>
                                <div class="feat-title">Transparan</div>
                                <div class="feat-desc">Masyarakat dapat memantau perkembangan laporan secara berkala.</div>
                            </div>
                        </div>

                        <div class="about-feat">
                            <div class="feat-icon">03</div>
                            <div>
                                <div class="feat-title">Terstruktur</div>
                                <div class="feat-desc">Penanganan laporan dilakukan secara sistematis dan terdokumentasi.</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-card-num">15</div>
                        <div class="stat-card-label">Kabupaten/Kota Terlayani</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-num">240+</div>
                        <div class="stat-card-label">Petugas Lapangan Aktif</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-num">24 Jam</div>
                        <div class="stat-card-label">Layanan Pengaduan Online</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-card-num">Real-time</div>
                        <div class="stat-card-label">Monitoring Status Laporan</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="layanan" class="services">
        <div class="section">
            <div class="services-header">
                <span class="section-tag">Kategori Layanan</span>
                <h2 class="section-title">Jenis Laporan Masyarakat</h2>
                <p class="section-subtitle">Beberapa kategori layanan yang dapat dilaporkan masyarakat.</p>
            </div>

            <div class="cat-grid">
                <div class="cat-card">
                    <h3>Infrastruktur</h3>
                    <p>Jalan rusak, drainase, jembatan, dan fasilitas umum.</p>
                </div>
                <div class="cat-card">
                    <h3>Fasilitas Umum</h3>
                    <p>Lampu jalan, taman kota, halte, dan fasilitas publik.</p>
                </div>
                <div class="cat-card">
                    <h3>Kebersihan</h3>
                    <p>Sampah, pencemaran lingkungan, and drainase tersumbat.</p>
                </div>
                <div class="cat-card">
                    <h3>Keamanan</h3>
                    <p>Gangguan ketertiban dan pengaduan masyarakat.</p>
                </div>
                <div class="cat-card">
                    <h3>Darurat</h3>
                    <p>Banjir, bencana alam, dan kondisi darurat lainnya.</p>
                </div>
                <div class="cat-card">
                    <h3>Aspirasi</h3>
                    <p>Kritik, saran, dan masukan terhadap pelayanan publik.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="cara-kerja" class="how">
        <div class="section">
            <span class="section-tag">Cara Kerja</span>
            <h2 class="section-title">Alur Pelaporan</h2>
            <p class="section-subtitle">Setiap laporan diproses secara bertahap dan transparan.</p>

            <div class="steps">
                <div class="step">
                    <div class="step-num">1</div>
                    <h4>Buat Laporan</h4>
                    <p>Isi form laporan sesuai permasalahan yang terjadi.</p>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <h4>Verifikasi</h4>
                    <p>Admin melakukan pengecekan dan validasi laporan.</p>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <h4>Penugasan</h4>
                    <p>Petugas menerima laporan sesuai kategori.</p>
                </div>
                <div class="step">
                    <div class="step-num">4</div>
                    <h4>Penanganan</h4>
                    <p>Laporan diproses dan diperbarui secara berkala.</p>
                </div>
                <div class="step">
                    <div class="step-num">5</div>
                    <h4>Selesai</h4>
                    <p>Masyarakat menerima hasil penanganan laporan.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="section">
            <h2>Mulai Gunakan SAPA Lampung</h2>
            <p>Gunakan sistem untuk menyampaikan laporan dan aspirasi masyarakat.</p>
            <a href="login.php" class="btn btn-white btn-lg">Masuk ke Sistem</a>
        </div>
    </section>

    <footer id="kontak" class="main-footer">
        <div class="footer-container">
            <div class="footer-grid">
                <div class="footer-brand-col">
                    <div class="footer-brand">
                        <div class="footer-logo">S</div>
                        <span class="footer-name">SAPA Lampung</span>
                    </div>
                    <p class="footer-desc">
                        Portal Pelayanan dan Pengaduan Masyarakat Provinsi Lampung. Wadah resmi untuk menyampaikan aspirasi dan keluhan demi pembangunan daerah yang lebih baik.
                    </p>
                </div>

                <div class="footer-col">
                    <h5>Tautan Kilat</h5>
                    <a href="#tentang">Tentang SAPA</a>
                    <a href="#cara-kerja">Alur Pengaduan</a>
                    <a href="masyarakat/buat-laporan.php">Buat Laporan</a>
                    <a href="login.php">Masuk Sistem</a>
                </div>

                <div class="footer-col">
                    <h5>Informasi</h5>
                    <a href="#layanan">Kategori Layanan</a>
                    <a href="#">Kebijakan Privasi</a>
                    <a href="#">F.A.Q</a>
                </div>

                <div class="footer-col">
                    <h5>Kontak Kami</h5>
                    <a href="#" style="cursor: default; text-decoration: none;">(0721) 481108</a>
                    <a href="#" style="cursor: default; text-decoration: none;">info@lampungprov.go.id</a>
                    <a href="#" style="cursor: default; text-decoration: none;">Jl. Wolter Monginsidi No. 69, Bandar Lampung</a>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Pemerintah Provinsi Lampung. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

</body>
</html>