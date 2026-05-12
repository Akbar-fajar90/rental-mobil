<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h4 class="fw-bold mb-1">Transfer Bank <?= esc($bank_info['name']) ?></h4>
                            <p class="text-muted small mb-0">Selesaikan pembayaran Anda</p>
                        </div>
                        
                        <div class="bg-light p-4 rounded text-center mb-4 border">
                            <p class="mb-1 text-muted small">Nomor Virtual Account / Rekening</p>
                            <h3 class="fw-bold text-dark tracking-wide mb-1"><?= esc($virtual_account) ?></h3>
                            <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 mt-2" onclick="navigator.clipboard.writeText('<?= esc($virtual_account) ?>'); alert('Disalin!')"><i class="bi bi-clipboard me-1"></i> Salin</button>
                        </div>
                        
                        <ul class="list-group list-group-flush mb-4 small text-muted">
                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                                <span>Total Tagihan</span>
                                <span class="fw-bold text-primary fs-6">Rp <?= number_format($amount, 0, ',', '.') ?></span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 bg-transparent">
                                <span>Batas Waktu Pembayaran</span>
                                <span class="fw-bold text-danger"><?= date('d M Y, H:i', strtotime($expired)) ?></span>
                            </li>
                        </ul>
                        
                        <div class="alert alert-info small" style="border-radius: 10px;">
                            <i class="bi bi-info-circle me-2"></i>Setelah melakukan transfer, sistem akan memverifikasi secara otomatis. Atau Anda bisa konfirmasi manual ke admin kami.
                        </div>
                        
                        <a href="<?= base_url('/riwayat/' . $sewa->id_sewa) ?>" class="btn btn-primary w-100 py-3 fw-bold mt-2" style="border-radius: 10px;">Kembali ke Riwayat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
