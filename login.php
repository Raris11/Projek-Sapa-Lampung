<?php
session_start();
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
        exit;
    } elseif ($_SESSION['role'] === 'petugas') {
        header("Location: petugas/petugas.php");
        exit;
    } elseif ($_SESSION['role'] === 'masyarakat') {
        header("Location: masyarakat/dashboard.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem — SAPA Lampung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/global.css">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

    <div class="login-container">
        
        <div class="login-left">
            <div class="login-box">
                <div class="login-header">
                    <a href="index.php" class="back-link">&larr; Kembali ke Beranda</a>
                    <h2><span>SAPA</span> Lampung</h2>
                    <p>Silakan masuk untuk memantau pengaduan Anda</p>
                </div>

                <form action="proses-login.php" method="POST">
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="contoh: warga@email.com" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="••••••••" required>
                    </div>

                    <button type="submit" name="login" class="btn-login">Masuk ke Sistem</button>
                </form>

                <div class="login-footer">
                    <p>Belum punya akun? <a href="#">Daftar disini</a></p>
                </div>
            </div>
        </div>

        <div class="login-right">
            <img src="assets/img/login/kantor-lampung.jpg" alt="Menara Siger Lampung">
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h3>Sistem Aspirasi & Pelaporan</h3>
                <p>Menghubungkan suara masyarakat langsung dengan jajaran dinas Pemerintah Provinsi Lampung.</p>
            </div>
        </div>

    </div>

</body>
</html>