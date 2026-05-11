<style>
    :root {
        --primary-blue: #1a73e8;
        --text-gray: #5f6368;
        --bg-white: #ffffff;
        --hover-bg: #f1f3f4;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        background-color: var(--bg-white);
        padding: 20px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
        display: flex;
        flex-direction: column;
        position: sticky;
        top: 0;
        overflow-y: auto;
        overflow-x: hidden;
        flex-shrink: 0;
        z-index: 100;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 24px;
        font-weight: bold;
        color: var(--primary-blue);
        margin-bottom: 40px;
        padding-left: 10px;
    }

    .logo-img {
        height: 32px;
        width: auto;
    }

    .nav-menu {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .nav-item {
        display: flex;
        align-items: center;
        text-decoration: none;
        color: var(--text-gray);
        padding: 14px 20px;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .nav-item i {
        margin-right: 15px;
        font-size: 20px;
        width: 24px;
        display: flex;
        justify-content: center;
    }

    /* State untuk menu yang sedang aktif (warna biru) */
    .nav-item.active {
        background-color: var(--primary-blue);
        color: white;
        box-shadow: 0 4px 15px rgba(26, 115, 232, 0.3);
    }

    /* Efek hover untuk menu yang tidak aktif */
    .nav-item:hover:not(.active) {
        background-color: var(--hover-bg);
        color: #333;
    }

    .sidebar-footer {
        margin-top: auto;
        padding-top: 20px;
    }
</style>

<div class="sidebar">
    <div class="logo">
        <?php 
        $logoPath = session()->get('logo_path') ?? '/assets/img/logo.png';
        if (file_exists(FCPATH . $logoPath)): ?>
            <img src="<?= base_url($logoPath) ?>" class="logo-img" alt="Logo">
        <?php else: ?>
            <i class="bi bi-car-front-fill"></i>
        <?php endif; ?>
        <span><?= session()->get('app_name') ?? 'Rental Mobil' ?></span>
    </div>

    <nav class="nav-menu">
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-item <?= ($active_menu ?? '') == 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        <a href="<?= base_url('admin/mobil') ?>" class="nav-item <?= ($active_menu ?? '') == 'mobil' ? 'active' : '' ?>">
            <i class="bi bi-car-front"></i> Mobil
        </a>
        <a href="<?= base_url('admin/admin') ?>" class="nav-item <?= ($active_menu ?? '') == 'admin' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> Admin
        </a>
        <a href="<?= base_url('admin/pelanggan') ?>" class="nav-item <?= ($active_menu ?? '') == 'pelanggan' ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Pelanggan
        </a>
        <a href="<?= base_url('admin/penyewaan') ?>" class="nav-item <?= ($active_menu ?? '') == 'penyewaan' ? 'active' : '' ?>">
            <i class="bi bi-key"></i> Penyewaan
        </a>
        <a href="<?= base_url('admin/pengembalian') ?>" class="nav-item <?= ($active_menu ?? '') == 'pengembalian' ? 'active' : '' ?>">
            <i class="bi bi-arrow-repeat"></i> Pengembalian
        </a>
        <a href="<?= base_url('admin/pembayaran') ?>" class="nav-item <?= ($active_menu ?? '') == 'pembayaran' ? 'active' : '' ?>">
            <i class="bi bi-wallet2"></i> Pembayaran
        </a>
        <a href="<?= base_url('admin/laporan') ?>" class="nav-item <?= ($active_menu ?? '') == 'laporan' ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i> Laporan
        </a>
    </nav>

    <div class="sidebar-footer">
        <hr style="opacity: 0.1;">
        <a href="#" class="nav-item">
            <i class="bi bi-question-circle"></i> Bantuan
        </a>
        <a href="<?= base_url('admin/logout'); ?>" class="nav-item" style="color: #dc3545;">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </div>
</div>