<aside class="sidebar">

    <div class="sidebar-brand">

        <div class="sidebar-logo">
            A
        </div>

        <div>

            <div class="sidebar-brand-name">
                SAPA Admin
            </div>

            <div class="sidebar-brand-sub">
                Panel Administrator
            </div>

        </div>

    </div>

    <nav class="sidebar-nav">

        <a href="dashboard.php"
           class="sidebar-item <?= ($page == 'dashboard') ? 'active' : ''; ?>">
            Dashboard
        </a>

        <a href="laporan.php"
           class="sidebar-item <?= ($page == 'laporan') ? 'active' : ''; ?>">
            Laporan
        </a>

        <a href="tracking.php"
           class="sidebar-item <?= ($page == 'tracking') ? 'active' : ''; ?>">
            Tracking
        </a>

        <a href="statistik.php"
           class="sidebar-item <?= ($page == 'statistik') ? 'active' : ''; ?>">
            Statistik
        </a>

        <a href="petugas.php"
           class="sidebar-item <?= ($page == 'petugas') ? 'active' : ''; ?>">
            Petugas
        </a>

        <a href="akun.php"
           class="sidebar-item <?= ($page == 'akun') ? 'active' : ''; ?>">
            Akun

        </a>

    </nav>

</aside>