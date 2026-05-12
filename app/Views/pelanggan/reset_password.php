<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold text-primary">Buat Password Baru</h3>
                            <p class="text-muted">Silakan masukkan password baru Anda.</p>
                        </div>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger" style="border-radius: 10px;"><?= session()->getFlashdata('error') ?></div>
                        <?php endif; ?>

                        <form action="<?= base_url('/lupa_password/update_password') ?>" method="POST">
                            <input type="hidden" name="token" value="<?= $token ?>">
                            
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold">PASSWORD BARU</label>
                                <input type="password" name="password" class="form-control bg-light border-0 p-3" style="border-radius: 10px;" required>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold">KONFIRMASI PASSWORD</label>
                                <input type="password" name="confirm_password" class="form-control bg-light border-0 p-3" style="border-radius: 10px;" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mb-3" style="border-radius: 10px;">Simpan Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection(); ?>
