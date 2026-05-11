<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-end mb-5">
            <div>
                <h2 class="fw-bold mb-1">Riwayat Sewa Saya</h2>
                <p class="text-muted mb-0">Pantau status pengajuan dan riwayat perjalanan Anda</p>
            </div>
            <a href="<?= base_url('/sewa') ?>" class="btn btn-primary px-4 py-2 shadow-sm" style="border-radius: 10px;">
                <i class="bi bi-plus-lg me-2"></i>Sewa Mobil Baru
            </a>
        </div>

        <?php if (session()->getFlashdata('success')) : ?>
            <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <!-- Filter & Search -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
            <div class="card-body p-4">
                <form action="<?= base_url('/riwayat') ?>" method="GET" class="row g-3 align-items-end">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small fw-bold text-muted">FILTER STATUS</label>
                        <select name="status" class="form-select border-0 bg-light p-2" style="border-radius: 10px;" onchange="this.form.submit()">
                            <option value="semua" <?= $filter == 'semua' ? 'selected' : '' ?>>Semua Pengajuan</option>
                            <option value="menunggu" <?= $filter == 'menunggu' ? 'selected' : '' ?>>Menunggu Konfirmasi</option>
                            <option value="disetujui" <?= $filter == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
                            <option value="ditolak" <?= $filter == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                            <option value="selesai" <?= $filter == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-lg-2 col-md-4 ms-auto text-end">
                        <span class="text-muted small">Menampilkan <strong><?= count($history) ?></strong> data</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- History List -->
        <?php if (empty($history)) : ?>
            <div class="text-center py-5">
                <div class="fs-1 text-muted mb-3"><i class="bi bi-calendar-x"></i></div>
                <h4 class="fw-bold text-muted">Belum ada riwayat sewa</h4>
                <p class="text-muted">Anda belum pernah mengajukan penyewaan mobil.</p>
                <a href="<?= base_url('/mobil') ?>" class="btn btn-outline-primary mt-3 px-4">Cari Mobil Sekarang</a>
            </div>
        <?php else : ?>
            <div class="row g-4">
                <?php foreach ($history as $h) : ?>
                    <div class="col-12">
                        <div class="card border-0 shadow-sm hover-shadow" style="border-radius: 20px; transition: transform 0.3s, box-shadow 0.3s;">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-2 mb-3 mb-md-0">
                                        <img src="<?= getCarImage($h['foto_mobil'], $h['merk']) ?>" class="img-fluid rounded-3" style="height: 80px; width: 100%; object-fit: cover;">
                                    </div>
                                    <div class="col-md-3 mb-3 mb-md-0">
                                        <h6 class="fw-bold mb-1"><?= $h['merk'] ?></h6>
                                        <p class="text-muted small mb-0"><i class="bi bi-tag me-1"></i> <?= $h['plat_nomor'] ?></p>
                                        <small class="text-muted">ID: #<?= $h['id_sewa'] ?></small>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <div class="row text-center">
                                            <div class="col-6 border-end">
                                                <div class="small text-muted">Tgl Sewa</div>
                                                <div class="fw-bold small"><?= date('d M Y', strtotime($h['tgl_sewa'])) ?></div>
                                            </div>
                                            <div class="col-6">
                                                <div class="small text-muted">Total Hari</div>
                                                <div class="fw-bold small"><?= $h['total_hari'] ?> Hari</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-md-end">
                                        <div class="mb-2">
                                            <?php 
                                                $badge = 'bg-warning text-dark';
                                                $status_label = 'Menunggu';
                                                
                                                if ($h['status'] == 'selesai') {
                                                    $badge = 'bg-info text-white';
                                                    $status_label = 'Selesai';
                                                } elseif ($h['status_pengajuan'] == 'disetujui') {
                                                    $badge = 'bg-success text-white';
                                                    $status_label = 'Disetujui';
                                                } elseif ($h['status_pengajuan'] == 'ditolak') {
                                                    $badge = 'bg-danger text-white';
                                                    $status_label = 'Ditolak';
                                                }
                                            ?>
                                            <span class="badge <?= $badge ?> px-3 py-2" style="border-radius: 8px;"><?= $status_label ?></span>
                                        </div>
                                        <h6 class="fw-bold text-primary mb-3">Rp <?= number_format($h['sub_total'], 0, ',', '.') ?></h6>
                                        <div class="d-flex gap-2 justify-content-md-end">
                                            <?php if ($h['status_pengajuan'] == 'disetujui' && $h['status'] != 'selesai' && (empty($h['status_bayar']) || $h['status_bayar'] == 'belum')): ?>
                                                <a href="<?= base_url('payment/checkout/' . $h['id_sewa']) ?>" class="btn btn-primary btn-sm px-4 fw-bold" style="border-radius: 8px;">
                                                    <i class="bi bi-wallet2 me-1"></i> BAYAR SEWA
                                                </a>
                                            <?php endif; ?>
                                            <a href="<?= base_url('/riwayat/detail/' . $h['id_sewa']) ?>" class="btn btn-outline-primary btn-sm px-4" style="border-radius: 8px;">Lihat Detail</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-5 d-flex justify-content-center">
                <?= $pager ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
</style>
<?= $this->endSection(); ?>
