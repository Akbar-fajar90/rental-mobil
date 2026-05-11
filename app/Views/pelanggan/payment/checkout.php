<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
                    <div class="card-header bg-dark text-white p-4 border-0 text-center">
                        <h4 class="fw-bold mb-1">Checkout Pembayaran</h4>
                        <p class="mb-0 opacity-75">Pilih metode pembayaran yang paling nyaman untuk Anda</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 bg-white">
                        <!-- Order Summary -->
                        <div class="bg-light p-4 rounded-4 mb-5 border-start border-4 border-primary">
                            <h6 class="text-muted small fw-bold mb-3 text-uppercase">Ringkasan Penyewaan</h6>
                            <div class="row align-items-center">
                                <div class="col-md-3">
                                    <img src="<?= getCarImage($sewa['foto_mobil'], $sewa['merk']) ?>" class="img-fluid rounded-3 shadow-sm">
                                </div>
                                <div class="col-md-9">
                                    <h5 class="fw-bold mb-1"><?= $sewa['merk'] ?></h5>
                                    <p class="text-muted small mb-3"><?= $sewa['plat_nomor'] ?></p>
                                    
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Durasi</small>
                                            <span class="fw-bold"><?= $sewa['total_hari'] ?> Hari</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block">Total Bayar</small>
                                            <span class="fw-bold text-primary fs-5">Rp <?= number_format($sewa['sub_total'], 0, ',', '.') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method Options -->
                        <h5 class="fw-bold mb-4">Pilih Tipe Pembayaran</h5>
                        
                        <div class="row g-3 mb-5">
                            <!-- Digital Payment (Midtrans) -->
                            <div class="col-md-6">
                                <div class="payment-card p-4 border rounded-4 h-100 cursor-pointer active" onclick="selectType('online', this)">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="payment-icon bg-primary text-white rounded-circle me-3">
                                            <i class="bi bi-shield-lock-fill"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">Digital (Midtrans)</h6>
                                    </div>
                                    <p class="small text-muted mb-0">Transfer Bank (VA), GoPay, OVO, Dana, QRIS. Konfirmasi Otomatis.</p>
                                    <input type="radio" name="payment_type" value="online" class="d-none" checked>
                                </div>
                            </div>

                            <!-- Manual/Tunai Payment -->
                            <div class="col-md-6">
                                <div class="payment-card p-4 border rounded-4 h-100 cursor-pointer" onclick="selectType('manual', this)">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="payment-icon bg-success text-white rounded-circle me-3">
                                            <i class="bi bi-cash-stack"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">Manual / Tunai</h6>
                                    </div>
                                    <p class="small text-muted mb-0">Bayar Tunai di Kantor atau Transfer Manual ke Admin. Konfirmasi Manual.</p>
                                    <input type="radio" name="payment_type" value="manual" class="d-none">
                                </div>
                            </div>
                        </div>

                        <!-- Manual Options (Hidden by default) -->
                        <div id="manual-options" class="mb-5 d-none">
                            <h6 class="fw-bold mb-3">Pilih Metode Manual</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="radio" name="metode_manual" value="Tunai" id="tunai" class="btn-check">
                                    <label class="btn btn-outline-light border text-dark w-100 py-3" for="tunai">Bayar di Tempat (Tunai)</label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" name="metode_manual" value="Transfer Manual" id="tf_manual" class="btn-check">
                                    <label class="btn btn-outline-light border text-dark w-100 py-3" for="tf_manual">Transfer Manual</label>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <button id="pay-button" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow" style="border-radius: 12px;">
                                <i class="bi bi-credit-card me-2"></i>Lanjutkan Pembayaran
                            </button>
                            <p class="mt-3 small text-muted">Aman & Terenkripsi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .payment-card {
        transition: all 0.3s ease;
        border-width: 2px !important;
    }
    .payment-card:hover {
        border-color: #0d6efd !important;
        background-color: #f8f9ff;
    }
    .payment-card.active {
        border-color: #0d6efd !important;
        background-color: #f0f4ff;
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.1);
    }
    .payment-icon {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<!-- Midtrans Snap Script -->
<script src="<?= $is_production ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' ?>" data-client-key="<?= $client_key ?>"></script>
<script type="text/javascript">
    let selectedType = 'online';

    function selectType(type, element) {
        selectedType = type;
        document.querySelectorAll('.payment-card').forEach(card => card.classList.remove('active'));
        element.classList.add('active');
        
        const manualOptions = document.getElementById('manual-options');
        if (type === 'manual') {
            manualOptions.classList.remove('d-none');
        } else {
            manualOptions.classList.add('d-none');
        }
    }

    const payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        if (selectedType === 'online') {
            processOnlinePayment();
        } else {
            processManualPayment();
        }
    });

    function processOnlinePayment() {
        window.location.href = '<?= base_url('/payment/simulate/' . $sewa['id_sewa']) ?>';
    }

    function processManualPayment() {
        const metodeManual = document.querySelector('input[name="metode_manual"]:checked');
        if (!metodeManual) {
            alert('Silakan pilih metode manual (Tunai/Transfer Manual)');
            return;
        }

        payButton.disabled = true;
        
        const formData = new FormData();
        formData.append('id_sewa', '<?= $sewa['id_sewa'] ?>');
        formData.append('metode_bayar', metodeManual.value);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('/payment/manual') ?>', {
            method: 'POST',
            body: formData,
            credentials: 'include'
        })
        .then(response => {
            window.location.href = '<?= base_url('/riwayat/detail/' . $sewa['id_sewa']) ?>';
        })
        .catch(error => {
            alert('Terjadi kesalahan.');
            payButton.disabled = false;
        });
    }
</script>

<?= $this->endSection(); ?>
