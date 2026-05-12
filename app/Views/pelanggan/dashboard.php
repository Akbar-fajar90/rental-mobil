<?php
/**
 * @var array $popularCars
 * @var array $stats
 */
?>
<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<!-- Hero Section -->
<section class="hero-section py-5" style="background: linear-gradient(135deg, #1d63ed 0%, #2a3eff 100%); min-height: 500px; border-radius: 0 0 50px 50px; position: relative; overflow: hidden;">
    <div class="container position-relative" style="z-index: 2;">
        <div class="row align-items-center">
            <div class="col-lg-7 text-white mb-5 mb-lg-0">
                <h1 class="display-4 fw-800 mb-3" style="font-weight: 800;">Tanpa ribet, cukup <br> KTP dan SIM</h1>
                <p class="lead mb-4 opacity-75">Sewa mobil impianmu sekarang juga dengan proses yang cepat, mudah, dan transparan. Perjalananmu dimulai di sini.</p>
                <a href="<?= base_url('/mobil') ?>" class="btn btn-warning btn-lg px-5 py-3 fw-bold" style="border-radius: 12px; background: #ff9f43; border: none; color: white;">Sewa Sekarang</a>
            </div>
            <div class="col-lg-5">
                <div class="card border-0 shadow-lg p-4" style="border-radius: 20px;">
                    <h4 class="fw-bold mb-4">Book your car</h4>
                    <form action="<?= base_url('/mobil') ?>" method="GET">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Pilih Mobil</label>
                            <select name="merk" class="form-select border-0 bg-light p-3" style="border-radius: 10px;">
                                <option value="">Semua Merk</option>
                                <option value="Toyota">Toyota</option>
                                <option value="Honda">Honda</option>
                                <option value="Daihatsu">Daihatsu</option>
                                <option value="Suzuki">Suzuki</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Tanggal Sewa</label>
                            <input type="date" class="form-control border-0 bg-light p-3" style="border-radius: 10px;">
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Tanggal Kembali</label>
                            <input type="date" class="form-control border-0 bg-light p-3" style="border-radius: 10px;">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold" style="border-radius: 10px; background: #1d63ed;">Cari Mobil</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5 mt-5">
    <div class="container">
        <div class="row text-center g-4">
            <div class="col-md-4">
                <div class="p-4">
                    <div class="feature-icon mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #f0f4ff; border-radius: 20px; color: #1d63ed; font-size: 2rem;">
                        <i class="bi bi-geo-alt"></i>
                    </div>
                    <h5 class="fw-bold">Availability</h5>
                    <p class="text-muted small">Cek ketersediaan mobil 24/7. Pesan kapan saja di mana saja hanya dengan beberapa klik.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div class="feature-icon mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #f0f4ff; border-radius: 20px; color: #1d63ed; font-size: 2rem;">
                        <i class="bi bi-car-front"></i>
                    </div>
                    <h5 class="fw-bold">Comfort</h5>
                    <p class="text-muted small">Mobil bersih, AC dingin, mesin terawat. Nikmati kenyamanan perjalanan maksimal bersama kami.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4">
                    <div class="feature-icon mb-4 mx-auto d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; background: #f0f4ff; border-radius: 20px; color: #1d63ed; font-size: 2rem;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h5 class="fw-bold">Savings</h5>
                    <p class="text-muted small">Harga sewa paling kompetitif. Dapatkan promo menarik setiap bulan khusus pelanggan setia.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Popular Cars Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="fw-bold mb-0">Pilih Mobil Yang Kamu Suka</h2>
                <p class="text-muted">Rekomendasi armada terbaik untuk kenyamanan Anda</p>
            </div>
            <a href="<?= base_url('/mobil') ?>" class="btn btn-outline-primary px-4 fw-bold" style="border-radius: 10px;">Lihat Semua <i class="bi bi-arrow-right ms-2"></i></a>
        </div>
        
        <div class="row g-4">
            <?php foreach ($popularCars as $c) : ?>
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                    <div class="position-relative">
                        <img src="<?= getCarImage($c['foto_mobil'], $c['merk']) ?>" class="card-img-top" alt="<?= $c['merk'] ?>" style="height: 200px; object-fit: cover;">
                        <div class="position-absolute top-0 end-0 p-3">
                            <span class="badge bg-white text-primary fw-bold px-3 py-2" style="border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">Tersedia</span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-bold mb-1"><?= $c['merk'] ?></h5>
                                <p class="text-muted small mb-0">SUV / Sedan</p>
                            </div>
                            <div class="text-end">
                                <h5 class="text-primary fw-bold mb-0">Rp <?= number_format($c['tarif_per_hari'] / 1000, 0) ?>K</h5>
                                <small class="text-muted">per hari</small>
                            </div>
                        </div>
                        
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
    </div>
</section>

<!-- Stats Section -->
<section class="py-5" style="background: #6f42c1; border-radius: 50px 50px 0 0;">
    <div class="container py-4">
        <div class="text-center text-white mb-5">
            <h2 class="fw-bold">Fakta Dalam Angka</h2>
            <p class="opacity-75">Kepercayaan Anda adalah prioritas utama kami</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-6">
                <div class="bg-white p-4" style="border-radius: 20px;">
                    <div class="text-warning mb-2" style="font-size: 2rem;">
                        <i class="bi bi-car-front-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['total_mobil'] ?>+</h3>
                    <p class="text-muted small mb-0">Armada Mobil</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="bg-white p-4" style="border-radius: 20px;">
                    <div class="text-primary mb-2" style="font-size: 2rem;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['total_pelanggan'] ?>+</h3>
                    <p class="text-muted small mb-0">Pelanggan Puas</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="bg-white p-4" style="border-radius: 20px;">
                    <div class="text-success mb-2" style="font-size: 2rem;">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['total_sewa'] ?>+</h3>
                    <p class="text-muted small mb-0">Penyewaan</p>
                </div>
            </div>
            <div class="col-lg-3 col-6">
                <div class="bg-white p-4" style="border-radius: 20px;">
                    <div class="text-danger mb-2" style="font-size: 2rem;">
                        <i class="bi bi-speedometer2"></i>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= $stats['jarak_tempuh'] ?></h3>
                    <p class="text-muted small mb-0">Jarak Tempuh (km)</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>
