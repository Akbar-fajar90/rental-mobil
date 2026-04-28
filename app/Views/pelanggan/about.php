<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<!-- Hero About -->
<section class="py-5 text-white" style="background: linear-gradient(rgba(37, 99, 235, 0.9), rgba(37, 99, 235, 0.9)), url('https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&q=80&w=1920'); background-size: cover; background-position: center; border-radius: 0 0 50px 50px;">
    <div class="container py-5 text-center">
        <h1 class="display-3 fw-bold mb-3 animate__animated animate__fadeInDown">Tentang Kami</h1>
        <p class="lead opacity-75 animate__animated animate__fadeInUp">Menyediakan solusi transportasi terbaik sejak 2015</p>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 mt-n5">
    <div class="container">
        <div class="row g-4 justify-content-center">
            <div class="col-md-3">
                <div class="card border-0 shadow-lg text-center p-4 animate__animated animate__zoomIn" style="border-radius: 20px;">
                    <div class="display-5 fw-bold text-primary mb-1"><?= $stats['total_pelanggan'] ?>+</div>
                    <div class="text-muted small fw-bold">Pelanggan Puas</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg text-center p-4 animate__animated animate__zoomIn" style="border-radius: 20px; animation-delay: 0.1s;">
                    <div class="display-5 fw-bold text-primary mb-1"><?= $stats['total_mobil'] ?>+</div>
                    <div class="text-muted small fw-bold">Armada Tersedia</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg text-center p-4 animate__animated animate__zoomIn" style="border-radius: 20px; animation-delay: 0.2s;">
                    <div class="display-5 fw-bold text-primary mb-1"><?= $stats['tahun_pengalaman'] ?>+</div>
                    <div class="text-muted small fw-bold">Tahun Pengalaman</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-0 shadow-lg text-center p-4 animate__animated animate__zoomIn" style="border-radius: 20px; animation-delay: 0.3s;">
                    <div class="display-5 fw-bold text-primary mb-1"><?= $stats['total_sewa'] ?>+</div>
                    <div class="text-muted small fw-bold">Penyewaan Selesai</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- History & Vision -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1562519324-718600021c27?auto=format&fit=crop&q=80&w=800" class="img-fluid rounded-4 shadow-lg animate__animated animate__fadeInLeft" alt="Our History">
            </div>
            <div class="col-lg-6 animate__animated animate__fadeInRight">
                <h2 class="fw-bold mb-4">Sejarah Kami</h2>
                <p class="text-muted mb-4">Rental Mobil didirikan pada tahun 2015 dengan visi untuk merevolusi cara orang menyewa kendaraan. Dimulai hanya dengan 5 armada, kini kami telah tumbuh menjadi salah satu penyedia jasa rental mobil terkemuka di Indonesia.</p>
                
                <div class="row g-4 mt-2">
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3">
                            <h5 class="fw-bold text-primary mb-2">Visi</h5>
                            <p class="small text-muted mb-0">Menjadi perusahaan rental mobil yang paling terpercaya dan inovatif di Asia Tenggara.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="p-3 bg-light rounded-3">
                            <h5 class="fw-bold text-primary mb-2">Misi</h5>
                            <p class="small text-muted mb-0">Memberikan layanan berkualitas tinggi dengan harga transparan dan armada yang selalu terawat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Keunggulan Kami</h2>
            <p class="text-muted">Mengapa ribuan pelanggan memilih kami?</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 border-top border-primary border-4">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-shield-check"></i></div>
                    <h5 class="fw-bold">Keamanan Terjamin</h5>
                    <p class="text-muted small mb-0">Semua armada kami dilengkapi dengan asuransi komprehensif dan sistem pelacakan GPS.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 border-top border-primary border-4">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-gear-wide-connected"></i></div>
                    <h5 class="fw-bold">Perawatan Rutin</h5>
                    <p class="text-muted small mb-0">Mobil selalu dalam kondisi prima karena kami melakukan pemeriksaan rutin setiap minggu.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 border-top border-primary border-4">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-headset"></i></div>
                    <h5 class="fw-bold">Layanan 24/7</h5>
                    <p class="text-muted small mb-0">Tim dukungan pelanggan kami siap membantu Anda kapan saja, 24 jam sehari, 7 hari seminggu.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 border-top border-primary border-4">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-cash-stack"></i></div>
                    <h5 class="fw-bold">Harga Transparan</h5>
                    <p class="text-muted small mb-0">Tidak ada biaya tersembunyi. Harga yang Anda lihat adalah harga yang Anda bayar.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100 border-top border-primary border-4">
                    <div class="fs-1 text-primary mb-3"><i class="bi bi-lightning-charge"></i></div>
                    <h5 class="fw-bold">Proses Cepat</h5>
                    <p class="text-muted small mb-0">Booking hanya butuh waktu kurang dari 5 menit melalui website atau aplikasi kami.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section (Optional) -->
<section class="py-5">
    <div class="container text-center">
        <h2 class="fw-bold mb-5">Tim Kami</h2>
        <div class="row g-4 justify-content-center">
            <div class="col-md-3 col-6">
                <div class="team-card mb-4 text-center">
                    <img src="<?= getCustomerAvatar('Andi Pratama') ?>" class="rounded-circle mb-3 border border-4 border-primary p-1" style="width: 120px; height: 120px;" alt="Founder">
                    <h6 class="fw-bold mb-1">Andi Pratama</h6>
                    <small class="text-muted">CEO & Founder</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="team-card mb-4 text-center">
                    <img src="<?= getCustomerAvatar('Sari Wijaya') ?>" class="rounded-circle mb-3 border border-4 border-primary p-1" style="width: 120px; height: 120px;" alt="Manager">
                    <h6 class="fw-bold mb-1">Sari Wijaya</h6>
                    <small class="text-muted">Operations Manager</small>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="team-card mb-4 text-center">
                    <img src="<?= getCustomerAvatar('Budi Santoso') ?>" class="rounded-circle mb-3 border border-4 border-primary p-1" style="width: 120px; height: 120px;" alt="Marketing">
                    <h6 class="fw-bold mb-1">Budi Santoso</h6>
                    <small class="text-muted">Marketing Lead</small>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<?= $this->endSection(); ?>
