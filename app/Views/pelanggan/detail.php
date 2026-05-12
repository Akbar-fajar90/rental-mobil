<?= $this->extend('layout/landing'); ?>

<?php
/**
 * @var array $mobil Detailed information of the selected car
 * @var array $wiki Wikipedia data containing 'extract' and 'features'
 * @var array $otherCars Array of other car records for recommendation
 */

// Safe defaults to prevent "Undefined variable" and "Undefined index" errors
$mobil = $mobil ?? [
    'merk' => 'Mobil Tidak Ditemukan',
    'tarif_per_hari' => 0,
    'foto_mobil' => null,
    'tahun' => '-',
    'plat_nomor' => '-',
    'id_mobil' => 0
];
$wiki = $wiki ?? ['extract' => 'Informasi detail kendaraan belum tersedia.', 'features' => []];
$otherCars = $otherCars ?? [];
?>

<style>
    .spec-card {
        transition: all 0.3s ease;
        border: 1px solid #edf2f7 !important;
    }
    .spec-card:hover {
        background-color: #f0f7ff !important;
        border-color: #1d63ed !important;
    }
</style>

<?= $this->section('content'); ?>

<!-- Detail Car Section -->
<section class="py-5">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/mobil') ?>">Mobil</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= esc((string)($mobil['merk'] ?? '')) ?></li>
            </ol>
        </nav>

        <div class="row g-5">
            <!-- Left Side: Image & Description -->
            <div class="col-lg-8">
                <div class="mb-4">
                    <h1 class="fw-bold mb-1"><?= esc((string)($mobil['merk'] ?? '')) ?></h1>
                    <div class="d-flex align-items-center gap-3">
                        <h4 class="text-primary fw-bold mb-0">Rp <?= number_format((float)$mobil['tarif_per_hari'], 0, ',', '.') ?> <small class="text-muted fw-normal" style="font-size: 0.9rem;">/ Hari</small></h4>
                        <span class="badge bg-light text-success border border-success-subtle px-3 py-2">Tersedia</span>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-5" style="border-radius: 24px; overflow: hidden;">
                    <img src="<?= getCarImage((string)($mobil['foto_mobil'] ?? ''), (string)($mobil['merk'] ?? '')) ?>" class="img-fluid" alt="<?= esc((string)($mobil['merk'] ?? '')) ?>" style="width: 100%; height: 450px; object-fit: cover;">
                </div>

                <!-- Wiki Info -->
                <div class="mb-5">
                    <h4 class="fw-bold mb-4">Informasi Kendaraan</h4>
                    <div class="p-4 bg-light" style="border-radius: 20px; border-left: 5px solid #1d63ed;">
                        <p class="mb-0 text-muted" style="line-height: 1.8;"><?= esc((string)($wiki['extract'] ?? '')) ?></p>
                        <small class="text-muted mt-3 d-block fst-italic text-end">Sumber: Wikipedia & Database Internal</small>
                    </div>
                </div>

                <!-- Technical Specs -->
                <div class="mb-4">
                    <h4 class="fw-bold mb-4">Informasi Umum</h4>
                    <div class="row g-3">
                         <div class="col-md-4 col-6">
                            <div class="card border-0 bg-light p-3" style="border-radius: 16px;">
                                <div class="text-muted small mb-1">Tahun</div>
                                <div class="fw-bold"><?= esc((string)($mobil['tahun'] ?? '-')) ?></div>
                            </div>
                        </div>
                        <div class="col-md-4 col-6">
                            <div class="card border-0 bg-light p-3" style="border-radius: 16px;">
                                <div class="text-muted small mb-1">Plat Nomor</div>
                                <div class="fw-bold"><?= esc((string)($mobil['plat_nomor'] ?? '-')) ?></div>
                            </div>
                        </div>
                        <?php foreach ($wiki['features'] as $key => $value) : ?>
                            <?php if(!empty($value) && $value !== '-'): ?>
                            <div class="col-md-4 col-6">
                                <div class="card border-0 bg-light p-3 spec-card" style="border-radius: 16px;">
                                    <div class="text-muted small mb-1"><?= esc((string)$key) ?></div>
                                    <div class="fw-bold"><?= esc((string)$value) ?></div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <div class="mt-3 text-muted small fst-italic">
                        *) Spesifikasi dapat berbeda tergantung tipe dan tahun keluaran unit.
                    </div>
                </div>

                <!-- Equipment -->
                <div class="mb-5">
                    <h4 class="fw-bold mb-4">Fitur & Fasilitas</h4>
                    <div class="row g-3">
                        <?php 
                        $standardFeatures = ['Asuransi All Risk', 'Layanan Darurat 24 Jam', 'Kebersihan Terjamin', 'Tangki Bahan Bakar Penuh'];
                        foreach($standardFeatures as $feature): 
                        ?>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center gap-3 p-3 border rounded-4 shadow-sm bg-white">
                                <div class="icon-circle bg-primary bg-opacity-10 text-primary p-2 rounded-circle">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <span class="fw-medium text-dark"><?= $feature ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Right Side: Booking Form -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm p-4 sticky-top" style="border-radius: 24px; top: 100px; z-index: 980;">
                    <h4 class="fw-bold mb-4">Sewa Mobil</h4>
                    
                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">TANGGAL SEWA</label>
                        <input type="date" id="tgl_sewa" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-muted small fw-bold">DURASI (HARI)</label>
                        <div class="input-group">
                            <button class="btn btn-light border-0 px-3" type="button" id="btn-minus" style="border-radius: 12px 0 0 12px;"><i class="bi bi-dash-lg"></i></button>
                            <input type="number" id="durasi" class="form-control border-0 bg-light text-center p-3" value="1" min="1" readonly>
                            <button class="btn btn-light border-0 px-3" type="button" id="btn-plus" style="border-radius: 0 12px 12px 0;"><i class="bi bi-plus-lg"></i></button>
                        </div>
                    </div>

                    <div class="p-3 bg-light mb-4" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Biaya Sewa</span>
                            <span class="fw-bold">Rp <?= number_format($mobil['tarif_per_hari'], 0, ',', '.') ?> x <span id="display-durasi">1</span> Hari</span>
                        </div>
                        <hr class="my-2 opacity-10">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold text-dark">Total Biaya</span>
                            <span class="fw-bold text-primary fs-5" id="total-biaya">Rp <?= number_format($mobil['tarif_per_hari'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <a href="<?= base_url('/sewa/' . $mobil['id_mobil']) ?>" class="btn btn-primary w-100 py-3 fw-bold mb-3 shadow" style="border-radius: 12px; background: #1d63ed;">Ajukan Sewa Sekarang</a>
                    
                    <?php if(!session()->get('isLoggedInPelanggan')): ?>
                        <p class="text-muted small text-center mb-0">Silakan login untuk melanjutkan pemesanan</p>
                    <?php else: ?>
                        <p class="text-success small text-center mb-0"><i class="bi bi-check-circle-fill me-1"></i> Anda sudah masuk</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recommendation Section -->
        <div class="mt-5 pt-5">
            <h4 class="fw-bold mb-4">Mobil Lainnya</h4>
            <div class="row g-4">
                <?php foreach ($otherCars as $c) : ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 20px; overflow: hidden;">
                        <img src="<?= getCarImage((string)($c['foto_mobil'] ?? ''), (string)($c['merk'] ?? '')) ?>" class="card-img-top" alt="<?= esc((string)($c['merk'] ?? '')) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold mb-0"><?= esc((string)($c['merk'] ?? '')) ?></h5>
                                <span class="text-primary fw-bold">Rp <?= number_format(((float)($c['tarif_per_hari'] ?? 0)) / 1000, 0) ?>K</span>
                            </div>
                            <a href="<?= base_url('/detail/' . $c['id_mobil']) ?>" class="btn btn-outline-primary w-100 py-2 fw-bold" style="border-radius: 10px;">Lihat Detail</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    const tarif = <?= $mobil['tarif_per_hari'] ?>;
    const durasiInput = document.getElementById('durasi');
    const displayDurasi = document.getElementById('display-durasi');
    const totalBiaya = document.getElementById('total-biaya');

    function formatRupiah(angka) {
        return 'Rp ' + angka.toLocaleString('id-ID');
    }

    function updateTotal() {
        const val = parseInt(durasiInput.value);
        displayDurasi.innerText = val;
        totalBiaya.innerText = formatRupiah(val * tarif);
    }

    document.getElementById('btn-plus').addEventListener('click', () => {
        durasiInput.value = parseInt(durasiInput.value) + 1;
        updateTotal();
    });

    document.getElementById('btn-minus').addEventListener('click', () => {
        if (parseInt(durasiInput.value) > 1) {
            durasiInput.value = parseInt(durasiInput.value) - 1;
            updateTotal();
        }
    });
</script>
<?= $this->endSection(); ?>
