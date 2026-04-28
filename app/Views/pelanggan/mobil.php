<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<!-- Page Header -->
<section class="py-5 bg-light">
    <div class="container py-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="fw-bold mb-2">Daftar Mobil Kami</h1>
                <p class="text-muted mb-0">Temukan kendaraan yang tepat untuk setiap perjalanan Anda</p>
            </div>
            <div class="col-md-6 text-md-end mt-4 mt-md-0">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb justify-content-md-end mb-0">
                        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Mobil</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Filter Section -->
<section class="py-4 border-bottom sticky-top bg-white shadow-sm" style="top: 70px; z-index: 990;">
    <div class="container">
        <form action="<?= base_url('/mobil') ?>" method="GET" class="row g-3 align-items-end">
            <div class="col-lg-3 col-md-6">
                <label class="form-label small fw-bold text-muted">Cari Merk</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                    <input type="text" name="merk" class="form-control bg-light border-0" placeholder="Contoh: Toyota" value="<?= $filters['merk'] ?? '' ?>">
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted">Tahun</label>
                <select name="tahun" class="form-select bg-light border-0">
                    <option value="">Semua Tahun</option>
                    <?php for($y = date('Y'); $y >= 2018; $y--) : ?>
                    <option value="<?= $y ?>" <?= ($filters['tahun'] ?? '') == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted">Harga Min</label>
                <input type="number" name="harga_min" class="form-control bg-light border-0" placeholder="Rp 0" value="<?= $filters['harga_min'] ?? '' ?>">
            </div>
            <div class="col-lg-2 col-md-6">
                <label class="form-label small fw-bold text-muted">Harga Max</label>
                <input type="number" name="harga_max" class="form-control bg-light border-0" placeholder="Rp 1M" value="<?= $filters['harga_max'] ?? '' ?>">
            </div>
            <div class="col-lg-3">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold" style="border-radius: 10px;">Filter Sekarang</button>
                    <a href="<?= base_url('/mobil') ?>" class="btn btn-light" title="Reset"><i class="bi bi-arrow-counterclockwise"></i></a>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Car Grid Section -->
<section class="py-5 mt-4">
    <div class="container">
        <?php if (empty($mobil)) : ?>
        <div class="text-center py-5">
            <img src="<?= base_url('assets/img/no-data.svg') ?>" alt="No data" style="width: 200px; opacity: 0.5;">
            <h4 class="mt-4 fw-bold text-muted">Mobil tidak ditemukan</h4>
            <p class="text-muted">Coba ubah kriteria pencarian Anda</p>
            <a href="<?= base_url('/mobil') ?>" class="btn btn-primary mt-2 px-4 py-2" style="border-radius: 10px;">Tampilkan Semua Mobil</a>
        </div>
        <?php else : ?>
        <div class="row g-4">
            <?php foreach ($mobil as $c) : ?>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden; transition: transform 0.3s;">
                    <div class="position-relative">
                        <img src="<?= getCarImage($c['foto_mobil'], $c['merk']) ?>" class="card-img-top" alt="<?= $c['merk'] ?>" style="height: 220px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge bg-white text-primary fw-bold px-3 py-2" style="border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Rp <?= number_format($c['tarif_per_hari'] / 1000, 0) ?>K / Hari</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-1"><?= $c['merk'] ?></h5>
                        <p class="text-muted small mb-3">Tahun Produksi: <?= $c['tahun'] ?></p>
                        
                        <div class="row g-2 mb-4">
                            <div class="col-4">
                                <div class="bg-light p-2 text-center" style="border-radius: 8px;">
                                    <i class="bi bi-gear small me-1"></i> <span class="small">Matic</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light p-2 text-center" style="border-radius: 8px;">
                                    <i class="bi bi-people small me-1"></i> <span class="small">7 Seats</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="bg-light p-2 text-center" style="border-radius: 8px;">
                                    <i class="bi bi-snow small me-1"></i> <span class="small">Aircon</span>
                                </div>
                            </div>
                        </div>
                        
                        <a href="<?= base_url('/detail/' . $c['id_mobil']) ?>" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius: 10px;">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="mt-5 d-flex justify-content-center">
            <?= $pager->links('mobil', 'bootstrap_pagination') ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .card:hover {
        transform: translateY(-10px);
    }
    .pagination {
        gap: 8px;
    }
    .page-link {
        border: none;
        background: #f0f4ff;
        color: #1d63ed;
        border-radius: 8px !important;
        padding: 10px 18px;
        font-weight: 600;
    }
    .page-item.active .page-link {
        background: #1d63ed;
        color: white;
    }
</style>
<?= $this->endSection(); ?>
