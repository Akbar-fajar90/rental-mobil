<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<section class="auth-section py-5" style="background: #f8f9fa; min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold text-dark">Selamat Datang Kembali!</h3>
                            <p class="text-muted">Masuk untuk melanjutkan pesanan Anda</p>
                        </div>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger border-0 small" style="border-radius: 12px;">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->getFlashdata('success')) : ?>
                            <div class="alert alert-success border-0 small" style="border-radius: 12px;">
                                <?= session()->getFlashdata('success') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/auth/doLogin') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">ALAMAT EMAIL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0 p-3" placeholder="nama@email.com" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label small fw-bold text-muted">KATA SANDI</label>
                                    <a href="<?= base_url('/lupa_password') ?>" class="small text-decoration-none">Lupa kata sandi?</a>
                                </div>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 p-3" placeholder="••••••••" required style="border-radius: 0 12px 12px 0;">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye-slash text-muted"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mb-4 shadow-sm" style="border-radius: 12px; background: #1d63ed;">
                                Masuk ke Akun
                            </button>

                            <div class="text-center position-relative mb-4">
                                <hr class="text-muted opacity-25">
                                <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">Atau masuk dengan</span>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <a href="<?= base_url('/auth/google') ?>" class="btn btn-outline-light border w-100 py-2 d-flex align-items-center justify-content-center text-dark shadow-sm" style="border-radius: 12px; background: white;">
                                        <img src="https://www.gstatic.com/images/branding/product/1x/googleg_48dp.png" alt="" style="width: 18px; height: 18px;" class="me-2">
                                        <span class="small fw-bold">Google</span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="<?= base_url('/auth/facebook') ?>" class="btn btn-outline-light border w-100 py-2 d-flex align-items-center justify-content-center text-dark shadow-sm" style="border-radius: 12px; background: white;">
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/b/b8/2021_Facebook_icon.svg" alt="" style="width: 18px; height: 18px;" class="me-2">
                                        <span class="small fw-bold">Facebook</span>
                                    </a>
                                </div>
                            </div>

                            <p class="text-center text-muted small mb-0">
                                Belum punya akun? <a href="<?= base_url('/register') ?>" class="fw-bold text-primary text-decoration-none">Daftar Sekarang</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    function togglePassword(inputId, button) {
        const input = document.getElementById(inputId);
        const icon = button.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        } else {
            input.type = 'password';
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        }
    }
</script>
<?= $this->endSection(); ?>
