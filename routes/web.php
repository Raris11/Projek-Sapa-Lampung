<?php

$router->get('/', [HomeController::class, 'index']);
$router->get('/index.php', [HomeController::class, 'index']);
$router->get('/cek-laporan', [HomeController::class, 'cekLaporan']);
$router->get('/cek-laporan.php', [HomeController::class, 'cekLaporan']);
$router->get('/login', [AuthController::class, 'showLogin']);
$router->get('/login.php', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/login.php', [AuthController::class, 'login']);
$router->post('/proses-login.php', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/logout.php', [AuthController::class, 'logout']);

$router->get('/masyarakat/dashboard', [LegacyPageController::class, 'masyarakatDashboard']);
$router->get('/masyarakat/dashboard.php', [LegacyPageController::class, 'masyarakatDashboard']);
$router->post('/masyarakat/dashboard.php', [LegacyPageController::class, 'masyarakatDashboard']);
$router->get('/buat-laporan.php', [MasyarakatController::class, 'create']);
$router->get('/masyarakat/buat-laporan', [MasyarakatController::class, 'create']);
$router->get('/masyarakat/buat-laporan.php', [MasyarakatController::class, 'create']);
$router->post('/masyarakat/laporan', [MasyarakatController::class, 'store']);
$router->post('/masyarakat/proses-lapor.php', [MasyarakatController::class, 'store']);
$router->get('/masyarakat/riwayat', [LegacyPageController::class, 'masyarakatRiwayat']);
$router->get('/masyarakat/riwayat.php', [LegacyPageController::class, 'masyarakatRiwayat']);
$router->get('/masyarakat/tracking', [LegacyPageController::class, 'masyarakatTracking']);
$router->get('/masyarakat/tracking.php', [LegacyPageController::class, 'masyarakatTracking']);
$router->get('/masyarakat/user-login.php', [LegacyPageController::class, 'masyarakatUserLogin']);

$router->get('/admin/dashboard', [LegacyPageController::class, 'adminDashboard']);
$router->get('/admin/dashboard.php', [LegacyPageController::class, 'adminDashboard']);
$router->get('/admin/laporan', [LegacyPageController::class, 'adminLaporan']);
$router->get('/admin/laporan.php', [LegacyPageController::class, 'adminLaporan']);
$router->post('/admin/laporan/update', [AdminController::class, 'updateLaporan']);
$router->post('/admin/laporan.php', [AdminController::class, 'updateLaporan']);
$router->get('/admin/petugas-by-kategori', [AdminController::class, 'petugasByKategori']);
$router->get('/admin/ambil_petugas.php', [AdminController::class, 'petugasByKategori']);
$router->get('/admin/tracking', [LegacyPageController::class, 'adminTracking']);
$router->get('/admin/tracking.php', [LegacyPageController::class, 'adminTracking']);
$router->get('/admin/statistik', [LegacyPageController::class, 'adminStatistik']);
$router->get('/admin/statistik.php', [LegacyPageController::class, 'adminStatistik']);
$router->get('/admin/petugas', [LegacyPageController::class, 'adminPetugas']);
$router->get('/admin/petugas.php', [LegacyPageController::class, 'adminPetugas']);
$router->post('/admin/petugas.php', [LegacyPageController::class, 'adminPetugas']);
$router->get('/admin/akun', [LegacyPageController::class, 'adminAkun']);
$router->get('/admin/akun.php', [LegacyPageController::class, 'adminAkun']);
$router->post('/admin/akun.php', [LegacyPageController::class, 'adminAkun']);

$router->get('/petugas/dashboard', [LegacyPageController::class, 'petugasDashboard']);
$router->get('/petugas/petugas.php', [LegacyPageController::class, 'petugasDashboard']);
$router->post('/petugas/progress', [PetugasController::class, 'updateProgress']);
$router->post('/petugas/petugas.php', [PetugasController::class, 'updateProgress']);
$router->get('/petugas/daftar-tugas', [LegacyPageController::class, 'petugasDaftarTugas']);
$router->get('/petugas/daftar-tugas.php', [LegacyPageController::class, 'petugasDaftarTugas']);
$router->post('/petugas/daftar-tugas.php', [LegacyPageController::class, 'petugasDaftarTugas']);
$router->get('/petugas/riwayat_tugas', [LegacyPageController::class, 'petugasRiwayat']);
$router->get('/petugas/riwayat_tugas.php', [LegacyPageController::class, 'petugasRiwayat']);
$router->get('/petugas/update-status.php', [LegacyPageController::class, 'petugasUpdateStatus']);
$router->post('/petugas/update-status.php', [LegacyPageController::class, 'petugasUpdateStatus']);

