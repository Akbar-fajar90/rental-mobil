<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">


<div class="container-fluid p-0">
    <h4 class="fw-bold mb-1">Ringkasan Operasional</h4>
    <p class="text-muted small mb-4">Selamat datang kembali, <?= esc((string)(session()->get('adminNama') ?? 'Admin')) ?>. Berikut performa armada Anda hari ini.</p>

    <!-- Statistik Cards (DATA DARI DATABASE) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                        <i class="bi bi-people"></i>
                    </div>
                    <span class="text-success-badge">+12%</span>
                </div>
                <div class="text-muted small">Total Pelanggan</div>
                <h3 class="fw-bold mb-0"><?= number_format((float)($total_pelanggan ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-car-front"></i>
                </div>
                <div class="text-muted small">Mobil Tersedia</div>
                <h3 class="fw-bold mb-0"><?= $mobil_tersedia ?? 0 ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-key"></i>
                </div>
                <div class="text-muted small">Sewa Berlangsung</div>
                <h3 class="fw-bold mb-0"><?= $sewa_berlangsung ?? 0 ?></h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-bottom border-primary border-4">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="text-muted small">Pendapatan Bulan Ini</div>
                <h3 class="fw-bold text-primary mb-0">Rp <?= number_format((float)($pendapatan_bulan_ini ?? 0), 0, ',', '.') ?></h3>
            </div>
        </div>
    </div>

    <!-- Armada Aktif (DATA DARI DATABASE) -->
    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="card stat-card shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold m-0"><i class="bi bi-geo-alt me-2"></i> Pelacakan Armada Live</h6>
                    <span class="badge bg-success bg-opacity-10 text-success small">● LIVE</span>
                </div>
                <div id="map" style="height: 450px; width: 100%; border-radius: 12px;"></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card stat-card shadow-sm">
                <div class="d-flex justify-content-between mb-3">
                    <h6 class="fw-bold m-0">Armada Aktif</h6>
                    <i class="bi bi-funnel text-muted"></i>
                </div>
                <div class="list-group list-group-flush">
                    <?php if (!empty($armada_aktif)): ?>
                        <?php foreach ($armada_aktif as $armada): ?>
                        <div class="list-item">
                            <div class="stat-icon bg-light mb-0 me-3"><i class="bi bi-car-front"></i></div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small"><?= esc((string)($armada->merk ?? '')) ?> — <?= esc((string)($armada->plat_nomor ?? '')) ?></div>
                                <div class="text-muted" style="font-size: 0.7rem;">Sewa hingga <?= date('d/m/Y', strtotime($armada->tgl_kembali_rencana)) ?></div>
                            </div>
                            <div class="status-dot bg-success"></div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted text-center py-3">Tidak ada armada aktif</div>
                    <?php endif; ?>
                </div>
                <div class="text-center mt-auto pt-3">
                    <a href="<?= base_url('admin/sewa') ?>" class="text-primary text-decoration-none fw-bold small">LIHAT SEMUA STATUS</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performer (DATA MOBIL POPULER DARI DATABASE) -->
    <div class="d-flex justify-content-between align-items-end mb-3">
        <div>
            <h5 class="fw-bold mb-0">Top Performer</h5>
            <p class="text-muted small m-0">Armada dengan utilitas tertinggi bulan ini</p>
        </div>
        <a href="<?= base_url('admin/mobil') ?>" class="text-primary text-decoration-none fw-bold small">Lihat Semua Armada →</a>
    </div>

    <div class="row g-4">
        <?php if (!empty($mobil_populer)): ?>
            <?php foreach ($mobil_populer as $index => $mobil): ?>
            <div class="col-md-4">
                <div class="card car-card">
                    <div class="position-relative">
                        <?php if ($index == 0): ?>
                        <span class="badge-popular">TERPOPULER</span>
                        <?php endif; ?>
                        <img src="<?= getCarImage((string)($mobil->foto_mobil ?? ''), (string)($mobil->merk ?? '')) ?>" 
                        class="car-img" 
                        alt="<?= esc((string)($mobil->merk ?? '')) ?>"
                        onerror="this.src='<?= base_url('assets/img/default-car.png') ?>'">
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between">
                            <h6 class="fw-bold mb-1"><?= esc((string)($mobil->merk ?? '')) ?></h6>
                            <span class="text-muted small"><?= esc((string)($mobil->tahun ?? '')) ?></span>
                        </div>
                        <div class="d-flex gap-3 text-muted mb-4" style="font-size: 0.8rem;">
                            <span><i class="bi bi-people me-1"></i> 7 Seats</span>
                            <span><i class="bi bi-gear me-1"></i> Automatic</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="fw-bold text-primary m-0">Rp <?= number_format((float)($mobil->tarif_per_hari ?? 0), 0, ',', '.') ?> <small class="text-muted fw-normal" style="font-size: 0.7rem;">/ hari</small></h5>
                            <div class="small fw-bold">
                                <i class="bi bi-star-fill text-warning"></i> 4.9 
                                <span class="text-muted fw-normal">(<?= $mobil->total_disewa ?>x)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center">Belum ada data mobil populer</div>
            </div>
        <?php endif; ?>
        
        <!-- Promo Card -->
        <div class="col-md-4">
            <div class="card promo-card border-0">
                <i class="bi bi-car-front-fill bg-icon"></i>
                <div class="z-1">
                    <h3 class="fw-bold mb-3">Optimalkan Manajemen Armada</h3>
                    <p class="mb-4 opacity-75">Tambah unit baru atau buat laporan keuangan komprehensif sekarang.</p>
                    <button class="btn btn-light text-primary fw-bold px-4 py-2 mt-2">MULAI SEKARANG</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Menyimpan variabel PHP ke object window (global) agar bisa diakses oleh script.js
window.armadaData = <?= json_encode($armada_aktif_live ?? []) ?>;
</script>

<!-- Load Script Eksternal -->
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<?= $this->endSection() ?>