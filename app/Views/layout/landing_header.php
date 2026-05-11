<header class="landing-header shadow-sm bg-white position-fixed w-100 top-0">
    <div class="container py-2 d-flex align-items-center justify-content-between">
        <div class="logo-area d-flex align-items-center">
            <a href="<?= base_url('/') ?>" class="text-decoration-none d-flex align-items-center">
                <img src="<?= base_url('assets/img/logo_1777259352.png') ?>" alt="Rental Mobil Logo" class="logo-img" style="height: 40px;">
                <span class="logo-text ms-2 fw-bold text-primary fs-4">Rental Mobil</span>
            </a>
        </div>
        
        <nav class="main-nav d-none d-lg-block">
            <ul class="nav-list d-flex list-unstyled m-0 gap-4">
                <li><a href="<?= base_url('/') ?>" class="nav-link fw-semibold text-dark">Beranda</a></li>
                <li><a href="<?= base_url('/mobil') ?>" class="nav-link fw-semibold text-dark">Mobil</a></li>
                <li><a href="<?= base_url('/tentang') ?>" class="nav-link fw-semibold text-dark">Tentang</a></li>
                <li><a href="<?= base_url('/kontak') ?>" class="nav-link fw-semibold text-dark">Kontak</a></li>
            </ul>
        </nav>

        <div class="header-right d-flex align-items-center">
            <div class="help-section d-none d-md-flex align-items-center me-4">
                <div class="help-icon bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="help-text ms-2">
                    <small class="text-muted d-block" style="font-size: 0.7rem; line-height: 1;">Butuh Bantuan?</small>
                    <strong class="text-dark small">+62 887 6728 908</strong>
                </div>
            </div>
            
            <?php if(session()->get('isLoggedInPelanggan')): ?>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle px-3" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 10px;">
                        <i class="bi bi-person-circle me-1"></i> <?= session()->get('nama_pelanggan') ?>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-3" aria-labelledby="userMenu" style="border-radius: 15px;">
                        <li><a class="dropdown-item py-2" href="<?= base_url('/riwayat') ?>"><i class="bi bi-clock-history me-2"></i>Riwayat Sewa</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item py-2 text-danger" href="<?= base_url('/logout') ?>"><i class="bi bi-box-arrow-right me-2"></i>Keluar</a></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= base_url('/login') ?>" class="btn btn-primary px-4 py-2" style="border-radius: 10px; font-weight: 600;">
                    Masuk
                </a>
            <?php endif; ?>

            <button class="mobile-toggle d-lg-none border-0 bg-transparent ms-3" id="mobileMenuToggle">
                <i class="bi bi-list fs-2 text-primary"></i>
            </button>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div class="mobile-menu-overlay" id="mobileMenuOverlay"></div>

    <!-- Mobile Menu -->
    <div class="mobile-menu" id="mobileMenu">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="logo-area d-flex align-items-center">
                <img src="<?= base_url('assets/img/logo_1777259352.png') ?>" alt="Logo" class="logo-img" style="height: 35px;">
                <span class="logo-text ms-2 fw-bold text-primary">Rental Mobil</span>
            </div>
            <button class="btn-close" id="closeMobileMenu"></button>
        </div>
        
        <ul class="mobile-nav-list list-unstyled fs-5">
            <li class="mb-3"><a href="<?= base_url('/') ?>" class="text-decoration-none text-dark d-block p-2">Beranda</a></li>
            <li class="mb-3"><a href="<?= base_url('/mobil') ?>" class="text-decoration-none text-dark d-block p-2">Mobil</a></li>
            <li class="mb-3"><a href="<?= base_url('/tentang') ?>" class="text-decoration-none text-dark d-block p-2">Tentang</a></li>
            <li class="mb-3"><a href="<?= base_url('/kontak') ?>" class="text-decoration-none text-dark d-block p-2">Kontak</a></li>
            <li class="mb-3"><a href="<?= base_url('/login') ?>" class="text-decoration-none text-dark d-block p-2">Masuk</a></li>
        </ul>

        <div class="mt-auto pt-4 border-top">
            <div class="help-section d-flex align-items-center">
                <div class="help-icon bg-light text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                    <i class="bi bi-telephone-fill"></i>
                </div>
                <div class="help-text ms-3">
                    <small class="text-muted d-block">Butuh Bantuan?</small>
                    <strong class="text-dark">+62 887 6728 908</strong>
                </div>
            </div>
        </div>
    </div>
</header>
<!-- No spacer needed if using sticky-top without fixed-top, or adjust as needed -->
