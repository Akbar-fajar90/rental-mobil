<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
                    <div class="card-header bg-primary text-white p-4 border-0 text-center">
                        <h4 class="fw-bold mb-1">Pembayaran Sewa</h4>
                        <p class="mb-0 opacity-75">Selesaikan pembayaran Anda untuk konfirmasi pesanan</p>
                    </div>
                    
                    <div class="card-body p-4 p-md-5 bg-white">
                        <div class="row g-4 mb-5">
                            <div class="col-md-6 border-end">
                                <h6 class="text-muted small fw-bold mb-3 text-uppercase">Ringkasan Pesanan</h6>
                                <div class="d-flex align-items-center mb-3">
                                    <img src="<?= getCarImage($sewa['foto_mobil'], $sewa['merk']) ?>" class="rounded-3 shadow-sm me-3" style="width: 80px; height: 60px; object-fit: cover;">
                                    <div>
                                        <h6 class="fw-bold mb-0"><?= $sewa['merk'] ?></h6>
                                        <small class="text-muted"><?= $sewa['total_hari'] ?> Hari Sewa</small>
                                    </div>
                                </div>
                                <div class="bg-light p-3 rounded-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="small">Biaya Sewa:</span>
                                        <span class="small fw-bold">Rp <?= number_format($sewa['sub_total'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between pt-2 border-top">
                                        <span class="fw-bold">Total Tagihan:</span>
                                        <h5 class="fw-bold text-primary mb-0">Rp <?= number_format($sewa['sub_total'], 0, ',', '.') ?></h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted small fw-bold mb-3 text-uppercase">Detail Penyewa</h6>
                                <p class="mb-1 fw-bold"><?= session()->get('nama_pelanggan') ?></p>
                                <p class="mb-1 text-muted small"><?= session()->get('email_pelanggan') ?></p>
                                <p class="mb-0 text-muted small">ID Pesanan: #<?= $sewa['id_sewa'] ?></p>
                            </div>
                        </div>

                        <form action="<?= base_url('/pembayaran/proses') ?>" method="POST" id="paymentForm">
                            <?= csrf_field() ?>
                            <input type="hidden" name="id_sewa" value="<?= $sewa['id_sewa'] ?>">
                            
                            <h5 class="fw-bold mb-4">Pilih Metode Pembayaran</h5>
                            
                            <div class="row g-3 mb-5">
                                <!-- E-Wallet -->
                                <div class="col-md-6">
                                    <div class="payment-card p-3 border rounded-4 h-100 cursor-pointer" onclick="selectMethod('e-wallet', this)">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="payment-icon bg-info text-white rounded-circle me-3">
                                                <i class="bi bi-wallet2"></i>
                                            </div>
                                            <h6 class="fw-bold mb-0">E-Wallet</h6>
                                        </div>
                                        <p class="small text-muted mb-0">Dana, OVO, GoPay, LinkAja</p>
                                        <input type="radio" name="metode_bayar" value="e-wallet" class="d-none">
                                    </div>
                                </div>

                                <!-- E-Bank / Virtual Account -->
                                <div class="col-md-6">
                                    <div class="payment-card p-3 border rounded-4 h-100 cursor-pointer" onclick="selectMethod('e-bank', this)">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="payment-icon bg-primary text-white rounded-circle me-3">
                                                <i class="bi bi-bank"></i>
                                            </div>
                                            <h6 class="fw-bold mb-0">E-Bank / VA</h6>
                                        </div>
                                        <p class="small text-muted mb-0">BCA, Mandiri, BNI, BRI</p>
                                        <input type="radio" name="metode_bayar" value="e-bank" class="d-none">
                                    </div>
                                </div>

                                <!-- Transfer Manual -->
                                <div class="col-md-6">
                                    <div class="payment-card p-3 border rounded-4 h-100 cursor-pointer" onclick="selectMethod('transfer', this)">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="payment-icon bg-success text-white rounded-circle me-3">
                                                <i class="bi bi-cash-stack"></i>
                                            </div>
                                            <h6 class="fw-bold mb-0">Transfer Bank</h6>
                                        </div>
                                        <p class="small text-muted mb-0">Manual transfer ke rekening admin</p>
                                        <input type="radio" name="metode_bayar" value="transfer" class="d-none">
                                    </div>
                                </div>

                                <!-- Tunai -->
                                <div class="col-md-6">
                                    <div class="payment-card p-3 border rounded-4 h-100 cursor-pointer" onclick="selectMethod('tunai', this)">
                                        <div class="d-flex align-items-center mb-3">
                                            <div class="payment-icon bg-dark text-white rounded-circle me-3">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <h6 class="fw-bold mb-0">Tunai / Offline</h6>
                                        </div>
                                        <p class="small text-muted mb-0">Bayar di tempat saat pengambilan unit</p>
                                        <input type="radio" name="metode_bayar" value="tunai" class="d-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-options for E-Wallet -->
                            <div id="sub-e-wallet" class="mb-5 d-none">
                                <h6 class="fw-bold mb-3">Pilih Provider E-Wallet</h6>
                                <div class="row g-2">
                                    <div class="col-3">
                                        <input type="radio" name="jenis_ewallet" value="Dana" id="dana" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="dana">Dana</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="radio" name="jenis_ewallet" value="OVO" id="ovo" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="ovo">OVO</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="radio" name="jenis_ewallet" value="GoPay" id="gopay" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="gopay">GoPay</label>
                                    </div>
                                    <div class="col-3">
                                        <input type="radio" name="jenis_ewallet" value="ShopeePay" id="shopee" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="shopee">Shopee</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Sub-options for Bank -->
                            <div id="sub-bank" class="mb-5 d-none">
                                <h6 class="fw-bold mb-3">Pilih Bank</h6>
                                <div class="row g-2">
                                    <div class="col-4">
                                        <input type="radio" name="jenis_bank" value="BCA" id="bca" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="bca">BCA</label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" name="jenis_bank" value="Mandiri" id="mandiri" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="mandiri">Mandiri</label>
                                    </div>
                                    <div class="col-4">
                                        <input type="radio" name="jenis_bank" value="BNI" id="bni" class="btn-check">
                                        <label class="btn btn-outline-light border text-dark w-100 py-3" for="bni">BNI</label>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow-lg" style="border-radius: 12px; font-size: 1.1rem;" disabled id="btnPay">
                                Konfirmasi Pembayaran
                            </button>
                        </form>
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
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    function selectMethod(method, element) {
        // Reset all cards
        document.querySelectorAll('.payment-card').forEach(card => card.classList.remove('active'));
        document.querySelectorAll('input[name="metode_bayar"]').forEach(radio => radio.checked = false);
        
        // Activate selected
        element.classList.add('active');
        element.querySelector('input').checked = true;
        
        // Show/Hide sub-options
        document.getElementById('sub-e-wallet').classList.add('d-none');
        document.getElementById('sub-bank').classList.add('d-none');
        
        if (method === 'e-wallet') {
            document.getElementById('sub-e-wallet').classList.remove('d-none');
        } else if (method === 'e-bank' || method === 'transfer') {
            document.getElementById('sub-bank').classList.remove('d-none');
        }
        
        // Enable button
        document.getElementById('btnPay').disabled = false;
    }
</script>
<?= $this->endSection(); ?>
