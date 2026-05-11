<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<section class="auth-section py-5" style="background: #f8f9fa; min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5 col-md-8">
                <div class="card border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
                    <div class="card-body p-5">
                        <div class="text-center mb-5">
                            <h3 class="fw-bold text-dark">Daftar Akun Baru</h3>
                            <p class="text-muted">Mulai perjalanan Anda bersama kami</p>
                        </div>

                        <?php if (session()->getFlashdata('error')) : ?>
                            <div class="alert alert-danger border-0 small" style="border-radius: 12px;">
                                <?= session()->getFlashdata('error') ?>
                            </div>
                        <?php endif; ?>

                        <form action="<?= base_url('/auth/doRegister') ?>" method="POST">
                            <?= csrf_field() ?>
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">NAMA LENGKAP</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-person text-muted"></i></span>
                                    <input type="text" name="nama" class="form-control bg-light border-0 p-3" placeholder="Masukkan nama lengkap" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">ALAMAT EMAIL</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-envelope text-muted"></i></span>
                                    <input type="email" name="email" class="form-control bg-light border-0 p-3" placeholder="nama@email.com" required style="border-radius: 0 12px 12px 0;">
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">KATA SANDI</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-lock text-muted"></i></span>
                                    <input type="password" name="password" id="password" class="form-control bg-light border-0 p-3" placeholder="Minimal 8 karakter" required style="border-radius: 0 12px 12px 0;">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePassword('password', this)">
                                        <i class="bi bi-eye-slash text-muted"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">KONFIRMASI KATA SANDI</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i class="bi bi-shield-check text-muted"></i></span>
                                    <input type="password" name="password_confirm" id="password_confirm" class="form-control bg-light border-0 p-3" placeholder="Ulangi kata sandi" required style="border-radius: 0 12px 12px 0;">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePassword('password_confirm', this)">
                                        <i class="bi bi-eye-slash text-muted"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mb-4 shadow-sm" style="border-radius: 12px; background: #1d63ed;">
                                Buat Akun Sekarang
                            </button>

                            <p class="text-center text-muted small mb-0">
                                Sudah punya akun? <a href="<?= base_url('/login') ?>" class="fw-bold text-primary text-decoration-none">Masuk Disini</a>
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
