<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card border-0 shadow-lg overflow-hidden" style="border-radius: 20px;">
                <!-- Header Simulasi -->
                <div class="bg-primary p-4 text-white text-center position-relative">
                    <div class="mb-2">
                        <i class="bi bi-shield-lock-fill fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-0">Secure Mock Gateway</h5>
                    <p class="small opacity-75 mb-0">Order ID: #<?= 'SIM-' . time() ?></p>
                </div>

                <div class="card-body p-0">
                    <!-- Ringkasan Singkat -->
                    <div class="p-4 bg-light border-bottom">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted small mb-1">TOTAL PEMBAYARAN</h6>
                                <h4 class="fw-bold text-primary mb-0">Rp <?= number_format($sewa['sub_total'], 0, ',', '.') ?></h4>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-white text-dark border p-2">
                                    <i class="bi bi-clock me-1"></i> <span id="timer">23:59:59</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Pilihan Bank Simulasi -->
                    <div class="p-4">
                        <h6 class="fw-bold mb-3 small text-uppercase">Pilih Bank Transfer (Simulasi)</h6>
                        
                        <div class="list-group list-group-flush gap-2">
                            <div class="list-group-item border rounded-3 p-3 cursor-pointer payment-item" onclick="showVA('BCA', '8837261552')">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3" style="width: 50px; text-align: center;">
                                        <strong class="text-primary">BCA</strong>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">BCA Virtual Account</h6>
                                        <small class="text-muted">Cek otomatis</small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>

                            <div class="list-group-item border rounded-3 p-3 cursor-pointer payment-item" onclick="showVA('Mandiri', '70012288339')">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3" style="width: 50px; text-align: center;">
                                        <strong class="text-warning">MDR</strong>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">Mandiri Bill Payment</h6>
                                        <small class="text-muted">Cek otomatis</small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>

                            <div class="list-group-item border rounded-3 p-3 cursor-pointer payment-item" onclick="showVA('QRIS', 'QR-CODE-MOCK')">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded p-2 me-3" style="width: 50px; text-align: center;">
                                        <i class="bi bi-qr-code"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold">QRIS (Gopay/OVO/Dana)</h6>
                                        <small class="text-muted">Scan QR Code</small>
                                    </div>
                                    <i class="bi bi-chevron-right text-muted"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Gateway -->
                <div class="p-3 bg-light text-center">
                    <img src="https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_92x30dp.png" style="height: 15px; opacity: 0.5; filter: grayscale(1);">
                    <span class="small text-muted ms-2" style="font-size: 0.7rem;">Powered by Simulator Gateway</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Virtual Account -->
<div class="modal fade" id="vaModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-body p-4 text-center">
                <div class="text-primary fs-1 mb-3"><i class="bi bi-bank"></i></div>
                <h5 class="fw-bold mb-1" id="bankName">BCA Virtual Account</h5>
                <p class="small text-muted mb-4">Salin nomor VA di bawah ini:</p>
                
                <div class="bg-light p-3 rounded-3 mb-4 position-relative">
                    <h4 class="fw-bold text-dark mb-0" id="vaNumber">8837261552</h4>
                    <small class="text-primary fw-bold" style="cursor: pointer;" onclick="copyVA()">SALIN</small>
                </div>

                <div class="alert alert-warning small border-0 py-2">
                    <i class="bi bi-info-circle me-1"></i> Ini adalah simulasi pembayaran
                </div>

                <button class="btn btn-primary w-100 py-3 fw-bold shadow mb-2" id="btnSelesai" onclick="finishPayment()">
                    SIMULASI BAYAR BERHASIL
                </button>
                <button class="btn btn-link btn-sm text-decoration-none text-muted" data-bs-dismiss="modal">Kembali</button>
            </div>
        </div>
    </div>
</div>

<style>
    .payment-item {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    .payment-item:hover {
        background-color: #f0f7ff;
        border-color: #0d6efd !important;
        transform: scale(1.02);
    }
    #timer {
        font-family: monospace;
        font-weight: bold;
    }
</style>

<script>
    let currentBank = '';

    function showVA(bank, number) {
        currentBank = bank;
        document.getElementById('bankName').innerText = bank + ' Virtual Account';
        document.getElementById('vaNumber').innerText = number;
        new bootstrap.Modal(document.getElementById('vaModal')).show();
    }

    function copyVA() {
        const va = document.getElementById('vaNumber').innerText;
        navigator.clipboard.writeText(va);
        alert('Nomor VA disalin!');
    }

    function finishPayment() {
        const btn = document.getElementById('btnSelesai');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        const formData = new FormData();
        formData.append('id_sewa', '<?= $sewa['id_sewa'] ?>');
        formData.append('metode', currentBank);
        formData.append('total', '<?= $sewa['sub_total'] ?>');
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('/payment/process-simulation') ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                window.location.href = '<?= base_url('/payment/finish') ?>';
            }
        });
    }

    // Timer simulasi
    let time = 86399;
    setInterval(() => {
        const h = Math.floor(time / 3600).toString().padStart(2, '0');
        const m = Math.floor((time % 3600) / 60).toString().padStart(2, '0');
        const s = (time % 60).toString().padStart(2, '0');
        document.getElementById('timer').innerText = `${h}:${m}:${s}`;
        time--;
    }, 1000);
</script>

<?= $this->endSection(); ?>
