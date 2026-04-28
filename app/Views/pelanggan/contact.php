<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<!-- Hero Contact -->
<section class="py-5 text-white" style="background: #2563eb; border-radius: 0 0 50px 50px;">
    <div class="container py-4 text-center">
        <h1 class="display-4 fw-bold mb-2">Hubungi Kami</h1>
        <p class="lead opacity-75">Kami siap membantu kebutuhan transportasi Anda</p>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="row g-5">
            <!-- Contact Info -->
            <div class="col-lg-5">
                <div class="card border-0 bg-primary text-white p-5 h-100 shadow-lg" style="border-radius: 30px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;">
                    <h3 class="fw-bold mb-4">Informasi Kontak</h3>
                    <p class="opacity-75 mb-5">Silakan hubungi kami melalui saluran di bawah ini atau isi formulir untuk mengirim pesan langsung.</p>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="fs-3 me-3"><i class="bi bi-geo-alt"></i></div>
                        <div>
                            <div class="fw-bold">Alamat Kantor</div>
                            <small class="opacity-75">Jl. Raya Pusat Otomotif No. 123, Jakarta Selatan</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="fs-3 me-3"><i class="bi bi-telephone"></i></div>
                        <div>
                            <div class="fw-bold">Telepon / WA</div>
                            <small class="opacity-75">+62 887 6728 908</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-4">
                        <div class="fs-3 me-3"><i class="bi bi-envelope"></i></div>
                        <div>
                            <div class="fw-bold">Email Support</div>
                            <small class="opacity-75">info@rentalmobil.com</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-5">
                        <div class="fs-3 me-3"><i class="bi bi-clock"></i></div>
                        <div>
                            <div class="fw-bold">Jam Operasional</div>
                            <small class="opacity-75">Senin - Minggu: 24 Jam</small>
                        </div>
                    </div>
                    
                    <div class="mt-auto">
                        <h6 class="fw-bold mb-3">Ikuti Kami</h6>
                        <div class="d-flex gap-3">
                            <a href="#" class="btn btn-light btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-facebook"></i></a>
                            <a href="#" class="btn btn-light btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-instagram"></i></a>
                            <a href="#" class="btn btn-light btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-twitter-x"></i></a>
                            <a href="#" class="btn btn-light btn-sm rounded-circle" style="width: 35px; height: 35px; display: flex; align-items: center; justify-content: center;"><i class="bi bi-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm p-4 p-md-5" style="border-radius: 30px;">
                    <h3 class="fw-bold mb-4">Kirim Pesan</h3>
                    
                    <?php if (session()->getFlashdata('success')) : ?>
                        <div class="alert alert-success border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('/contact/send') ?>" method="POST" id="contactForm">
                        <?= csrf_field() ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">NAMA LENGKAP</label>
                                <input type="text" name="nama" class="form-control bg-light border-0 p-3" placeholder="Masukkan nama Anda" required style="border-radius: 12px;">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ALAMAT EMAIL</label>
                                <input type="email" name="email" class="form-control bg-light border-0 p-3" placeholder="email@contoh.com" required style="border-radius: 12px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">NOMOR TELEPON (OPSIONAL)</label>
                                <input type="text" name="no_telp" class="form-control bg-light border-0 p-3" placeholder="Contoh: 08123456789" style="border-radius: 12px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">PESAN ANDA</label>
                                <textarea name="pesan" rows="5" class="form-control bg-light border-0 p-3" placeholder="Tuliskan pesan atau pertanyaan Anda di sini..." required style="border-radius: 12px;"></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius: 12px; background: #2563eb;">Kirim Pesan Sekarang</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Maps Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="fw-bold mb-4 text-center">Lokasi Kami</h3>
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 30px;">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126906.14324263056!2d106.7196777!3d-6.223388!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f3e945e34b9d%3A0x5371bf0fdad786a2!2sJakarta%20Selatan%2C%20Kota%20Jakarta%20Selatan%2C%20Daerah%20Khusus%20Ibukota%20Jakarta!5e0!3m2!1sid!2sid!4v1714275000000!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    document.getElementById('contactForm').addEventListener('submit', function(e) {
        const nama = this.querySelector('[name="nama"]').value;
        const email = this.querySelector('[name="email"]').value;
        const pesan = this.querySelector('[name="pesan"]').value;
        
        if (nama.length < 3) {
            alert('Nama minimal 3 karakter');
            e.preventDefault();
        } else if (pesan.length < 10) {
            alert('Pesan minimal 10 karakter');
            e.preventDefault();
        }
    });
</script>
<?= $this->endSection(); ?>
