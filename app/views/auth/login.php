<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Sistem - SAPA Lampung</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/global.css'); ?>">
    <link rel="stylesheet" href="<?= asset('css/login.css'); ?>">
</head>
<body>
    <div class="login-container">
        <div class="login-left">
            <div class="login-box">
                <div class="login-header">
                    <a href="<?= url('/'); ?>" class="back-link">&larr; Kembali ke Beranda</a>
                    <h2><span>SAPA</span> Lampung</h2>
                    <p>Silakan masuk untuk memantau pengaduan Anda</p>
                </div>

                <form action="<?= url('/login'); ?>" method="POST">
                    <div class="form-group">
                        <label for="email">Alamat Email</label>
                        <input type="email" id="email" name="email" class="form-input" placeholder="contoh: warga@email.com" required autocomplete="off">
                    </div>

                    <div class="form-group">
                        <label for="password">Kata Sandi</label>
                        <input type="password" id="password" name="password" class="form-input" placeholder="********" required>
                    </div>

                    <button type="submit" name="login" class="btn-login">Masuk ke Sistem</button>
                </form>
            </div>
        </div>

        <div class="login-right">
            <img src="<?= asset('img/login/kantor-lampung.jpg'); ?>" alt="Menara Siger Lampung">
            <div class="hero-overlay"></div>
            <div class="hero-text">
                <h3>Sistem Aspirasi & Pelaporan</h3>
                <p>Menghubungkan suara masyarakat langsung dengan jajaran dinas Pemerintah Provinsi Lampung.</p>
            </div>
        </div>
    </div>
</body>
</html>
