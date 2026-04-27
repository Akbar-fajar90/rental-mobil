<style>
    .sidebar {
        width: 260px;
        height: 100vh;
        background-color: #ffffff;
        border-right: 1px solid #dee2e6;
        display: flex;
        flex-direction: column;
        padding: 20px;
        position: sticky;
        top: 0;
        overflow-y: auto;
        overflow-x: hidden;
    }
    
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    
    .sidebar::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .sidebar::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 10px;
    }
    
    .nav-link {
        color: #495057;
        padding: 12px 15px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 15px;
        text-decoration: none;
        transition: 0.3s;
        margin: 2px 0;
        white-space: nowrap;
    }
    
    .nav-link:hover {
        background-color: #f0f7ff;
        color: #0d6efd;
    }
    
    .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    
    .nav-link i {
        font-size: 1.2rem;
        width: 24px;
        flex-shrink: 0;
    }
    
    .sidebar-footer {
        margin-top: auto;
        padding-top: 15px;
    }
    
    .brand-title {
        color: #0d6efd;
        font-weight: 700;
        font-size: 1.25rem;
        margin-bottom: 30px;
        padding-left: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    
    .brand-title i {
        font-size: 1.5rem;
    }
    
    .logo-img {
        height: 32px;
        width: auto;
        transition: filter 0.3s;
    }
    
    hr {
        margin: 15px 0;
    }
</style>

<div class="sidebar">
    <div class="brand-title">
        <?php 
        $logoPath = session()->get('logo_path') ?? '/assets/img/logo.png';
        if (file_exists(FCPATH . $logoPath)): ?>
            <img src="<?= base_url($logoPath) ?>" class="logo-img" alt="Logo">
        <?php else: ?>
            <i class="bi bi-car-front-fill"></i>
        <?php endif; ?>
        <?= session()->get('app_name') ?? 'Rental Mobil' ?>
    </div>

    <nav class="nav flex-column">
        <a href="<?= base_url('admin/dashboard') ?>" class="nav-link <?= ($active_menu ?? '') == 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid"></i> Dashboard
        </a>
        <a href="<?= base_url('admin/mobil') ?>" class="nav-link <?= ($active_menu ?? '') == 'mobil' ? 'active' : '' ?>">
            <i class="bi bi-car-front"></i> Mobil
        </a>
        <a href="<?= base_url('admin/admin') ?>" class="nav-link <?= ($active_menu ?? '') == 'admin' ? 'active' : '' ?>">
            <i class="bi bi-person-badge"></i> Admin
        </a>
        <a href="<?= base_url('admin/pelanggan') ?>" class="nav-link <?= ($active_menu ?? '') == 'pelanggan' ? 'active' : '' ?>">
            <i class="bi bi-people"></i> Pelanggan
        </a>
        <a href="<?= base_url('admin/penyewaan') ?>" class="nav-link <?= ($active_menu ?? '') == 'penyewaan' ? 'active' : '' ?>">
            <i class="bi bi-key"></i> Penyewaan
        </a>
        <a href="<?= base_url('admin/pengembalian') ?>" class="nav-link <?= ($active_menu ?? '') == 'pengembalian' ? 'active' : '' ?>">
            <i class="bi bi-arrow-repeat"></i> Pengembalian
        </a>
        <a href="<?= base_url('admin/pembayaran') ?>" class="nav-link <?= ($active_menu ?? '') == 'pembayaran' ? 'active' : '' ?>">
            <i class="bi bi-wallet2"></i> Pembayaran
        </a>
        <a href="<?= base_url('admin/laporan') ?>" class="nav-link <?= ($active_menu ?? '') == 'laporan' ? 'active' : '' ?>">
            <i class="bi bi-graph-up"></i> Laporan
        </a>
    </nav>

    <div class="sidebar-footer">
        <hr>
        <a href="#" class="nav-link">
            <i class="bi bi-question-circle"></i> Bantuan
        </a>
        <a href="<?= base_url('admin/logout'); ?>" class="nav-link text-danger">
            <i class="bi bi-box-arrow-right"></i> Keluar
        </a>
    </div>
</div>