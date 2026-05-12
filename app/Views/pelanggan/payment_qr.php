<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm text-center" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <h4 class="fw-bold mb-3">Pembayaran via <?= esc($ewallet) ?></h4>
                        <p class="text-muted mb-4">Silakan scan QR Code di bawah ini menggunakan aplikasi <?= esc($ewallet) ?> Anda.</p>
                        
                        <div class="bg-white p-3 d-inline-block rounded shadow-sm mb-4">
                            <img src="<?= $qrCode ?>" alt="QR Code" class="img-fluid">
                        </div>
                        
                        <div class="mb-4">
                            <p class="mb-1 text-muted small">Total Pembayaran</p>
                            <h3 class="fw-bold text-primary">Rp <?= number_format($amount, 0, ',', '.') ?></h3>
                            <p class="small text-muted mt-2">Order ID: <?= esc($order_id) ?></p>
                        </div>
                        
                        <div class="alert alert-warning small text-start" style="border-radius: 10px;">
                            <i class="bi bi-info-circle me-2"></i>Sistem akan otomatis mengecek status pembayaran setiap beberapa detik. Anda juga bisa menekan tombol di bawah jika sudah membayar.
                        </div>
                        
                        <a href="<?= base_url('/riwayat/' . $sewa->id_sewa) ?>" class="btn btn-primary w-100 py-3 fw-bold mt-2" style="border-radius: 10px;" id="btn-cek">Saya Sudah Membayar</a>
                        <a href="<?= base_url('/riwayat') ?>" class="btn btn-outline-secondary w-100 py-3 fw-bold mt-2" style="border-radius: 10px;">Kembali ke Riwayat</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    // Simulate polling
    setInterval(() => {
        console.log("Checking payment status for " + "<?= esc($order_id) ?>");
        // Di sini bisa dibuat panggilan AJAX sungguhan ke endpoint pengecekan Midtrans
    }, 3000);
</script>
<?= $this->endSection(); ?>
