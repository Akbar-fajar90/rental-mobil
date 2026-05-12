<?= $this->extend('layout/main') ?>
<?php 
/** 
 * @var object[] $payments 
 * @var object|null $selected_payment 
 * @var object $stats 
 * @var object[] $pending_payments 
 * @var object[] $approved_rentals
 * @var string $midtrans_client_key
 */ 
?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $midtrans_client_key ?>"></script>

<div class="pembayaran-wrapper container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1 text-white">Pembayaran</h4>
        <p class="text-white small ">Kelola transaksi pembayaran pelanggan</p>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- LEFT: Riwayat Pembayaran -->
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header-custom">
                    <span class="fw-bold">Riwayat Transaksi Pembayaran</span>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-outline-secondary" id="btnFilter"><i class="bi bi-funnel"></i></button>
                        <button class="btn btn-sm btn-outline-secondary" id="btnExport"><i class="bi bi-download"></i></button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table-custom">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>ID Sewa</th>
                                <th>Pelanggan</th>
                                <th>Mobil</th>
                                <th>Jumlah Bayar</th>
                                <th>Metode</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($payments)): ?>
                                <?php foreach ($payments as $payment): ?>
                                <tr onclick="window.location='?id=<?= $payment->id_pembayaran ?>'"
                                    class="<?= ($selected_payment && $selected_payment->id_pembayaran == $payment->id_pembayaran) ? 'active' : '' ?>">
                                    <td><small><?= date('d/m/Y H:i', strtotime($payment->tgl_bayar)) ?></small></td>
                                    <td><span class="trx-id">#<?= $payment->id_sewa ?></span></td>
                                    <td><?= esc((string)($payment->nama_pelanggan ?? '')) ?></td>
                                    <td><?= esc((string)($payment->mobil_merk ?? '')) ?></td>
                                    <td class="fw-bold">Rp <?= number_format($payment->jumlah_bayar, 0, ',', '.') ?></td>
                                    <td>
                                        <?php
                                        $icon = '';
                                        if ($payment->metode_bayar == 'tunai') $icon = '💰';
                                        elseif ($payment->metode_bayar == 'transfer') $icon = '🏦';
                                        else $icon = '📱';
                                        echo "$icon " . ucfirst($payment->metode_bayar);
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge <?= $payment->status_bayar == 'lunas' ? 'badge-lunas' : ($payment->status_bayar == 'sebagian' ? 'badge-sebagian' : 'badge-belum') ?>">
                                            <?= strtoupper($payment->status_bayar) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-white">Belum ada data pembayaran</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-header-custom border-top">
                    <small class="text-white">Menampilkan <?= count($payments) ?> transaksi</small>
                    <div class="pagination">
                        <button class="btn btn-sm btn-outline-secondary me-1">Prev</button>
                        <button class="btn btn-sm btn-outline-secondary active bg-primary">1</button>
                        <button class="btn btn-sm btn-outline-secondary ms-1">Next</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="col-lg-4">
            <?php
            $trx = $selected_payment ?? null;
            ?>

            <!-- Konfirmasi Card -->
            <div class="konfirmasi-card mb-4">
                <div class="text-white small mb-2">KONFIRMASI PEMBAYARAN</div>
                <div class="fw-bold fs-5 mb-1" id="infoIdSewa">#<?= $trx ? $trx->id_sewa : '-' ?></div>
                <div class="text-white-50 small mb-3" id="infoNamaPelanggan"><?= esc((string)($trx->nama_pelanggan ?? 'Pilih sewa terlebih dahulu')) ?></div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small">Total Tagihan</span>
                    <span class="fw-bold" id="infoTotalTagihan">Rp <?= $trx ? number_format($trx->total_tagihan ?? 0, 0, ',', '.') : '0' ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small">Total Denda</span>
                    <span class="fw-bold text-danger" id="infoTotalDenda">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small">Sisa Bayar</span>
                    <span class="fw-bold text-warning" id="infoSisaBayar">Rp 0</span>
                </div>

                <div class="mt-3" id="infoStatusBadge">
                    <?php if ($trx): ?>
                        <span class="badge <?= ($trx->status_bayar ?? '') == 'lunas' ? 'badge-lunas' : (($trx->status_bayar ?? '') == 'sebagian' ? 'badge-sebagian' : 'badge-belum') ?>">
                            <?= strtoupper($trx->status_bayar ?? 'BELUM') ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div class="card-custom mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Form Pembayaran</h6>

                    <form action="<?= base_url('admin/pembayaran/process') ?>" method="post" id="formPembayaran">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label small text-white">Pilih Sewa <span class="text-danger">*</span></label>
                            <select name="id_sewa" class="form-select" id="selectSewa" required>
                                <option value="">-- Pilih Sewa --</option>
                                <?php if (!empty($approved_rentals)): ?>
                                    <?php foreach ($approved_rentals as $rental): ?>
                                        <option value="<?= $rental->id_sewa ?>"
                                            data-nama="<?= esc((string)($rental->nama_pelanggan ?? '')) ?>"
                                            data-mobil="<?= esc((string)($rental->mobil_merk ?? '')) ?>"
                                            data-total="<?= $rental->sub_total ?>"
                                            <?= $trx && $trx->id_sewa == $rental->id_sewa ? 'selected' : '' ?>>
                                            #<?= $rental->id_sewa ?> - <?= esc((string)($rental->nama_pelanggan ?? '')) ?> (<?= esc((string)($rental->mobil_merk ?? '')) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-white">Jumlah Bayar <span class="text-danger">*</span></label>
                            <input type="text" name="jumlah_bayar" class="form-input" id="jumlahBayar" placeholder="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-white">Metode Pembayaran <span class="text-danger">*</span></label>
                            <select name="metode_bayar" class="form-select" id="metodeBayar" required>
                                <option value="tunai">💰 Tunai</option>
                                <option value="transfer">🏦 Transfer Bank</option>
                                <option value="ewallet">📱 E-Wallet</option>
                            </select>
                        </div>

                        <div class="mb-3" id="bankGroup" style="display: none;">
                            <label class="form-label small text-white">Pilih Bank</label>
                            <select name="jenis_bank" class="form-select">
                                <option value="BCA">BCA</option>
                                <option value="BRI">BRI</option>
                                <option value="Mandiri">Mandiri</option>
                                <option value="BNI">BNI</option>
                            </select>
                        </div>

                        <div class="mb-3" id="ewalletGroup" style="display: none;">
                            <label class="form-label small text-white">Pilih E-Wallet</label>
                            <select name="jenis_ewallet" class="form-select mb-3">
                                <option value="OVO">OVO</option>
                                <option value="GoPay">GoPay</option>
                                <option value="DANA">DANA</option>
                                <option value="LinkAja">LinkAja</option>
                            </select>
                            
                            <div id="qrGroup" class="text-center bg-white p-3 rounded-3" style="display: none;">
                                <div class="text-dark small fw-bold mb-2">SCAN QRIS UNTUK BAYAR</div>
                                <img id="qrImage" src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=RENTAL-PAYMENT" alt="QR Code" class="img-fluid mb-2" style="width: 150px;">
                                <div class="text-muted" style="font-size: 0.7rem;">Silakan scan QR di atas menggunakan aplikasi E-Wallet Anda</div>
                            </div>
                        </div>

                        <div class="summary-box bg-dark p-3 rounded-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white-50 small">Total Tagihan</span>
                                <span id="summaryTotal">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white-50 small">Denda (Terlambat/Rusak)</span>
                                <span class="text-danger" id="summaryDenda">Rp 0</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white-50 small">Sudah Dibayar</span>
                                <span class="text-success" id="summaryPaid">Rp 0</span>
                            </div>
                            <hr class="my-2 border-secondary">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Sisa Bayar</span>
                                <span class="text-warning" id="summarySisa">Rp 0</span>
                            </div>
                        </div>

                        <button type="submit" class="btn-konfirmasi w-100 mb-2">KONFIRMASI PEMBAYARAN</button>
                        <button type="button" id="payWithMidtrans" class="btn btn-primary w-100 mb-2 fw-bold" style="display: none; border-radius: 10px; padding: 13px;">BAYAR DENGAN MIDTRANS</button>
                    </form>

                    <?php if ($trx && ($trx->id_pembayaran ?? false)): ?>
                        <a href="<?= base_url('admin/pembayaran/invoice/' . $trx->id_pembayaran) ?>" 
                           class="btn-invoice w-100 text-center d-block" target="_blank">
                            CETAK INVOICE
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Midtrans Gateway Info -->
            <div class="card-custom">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3"><i class="bi bi-credit-card me-2"></i>Midtrans Gateway</h6>
                    <p class="text-white-50 small mb-3">Untuk pembayaran online via Midtrans, pelanggan dapat mengakses melalui halaman riwayat sewa mereka.</p>
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-success-subtle text-success">Aktif</span>
                        <span class="text-white-50 small">Client Key: <?= substr($midtrans_client_key, 0, 12) ?>...</span>
                    </div>
                    <div class="text-white-50 small mt-2">
                        <i class="bi bi-info-circle me-1"></i> Webhook: <code class="text-white">/payment/notification</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectSewa = document.getElementById('selectSewa');
    const metodeBayar = document.getElementById('metodeBayar');
    const bankGroup = document.getElementById('bankGroup');
    const ewalletGroup = document.getElementById('ewalletGroup');
    const jumlahBayar = document.getElementById('jumlahBayar');
    const btnMidtrans = document.getElementById('payWithMidtrans');

    // Metode bayar toggle
    metodeBayar?.addEventListener('change', function() {
        bankGroup.style.display = this.value === 'transfer' ? 'block' : 'none';
        ewalletGroup.style.display = this.value === 'ewallet' ? 'block' : 'none';
        const qrGroup = document.getElementById('qrGroup');
        qrGroup.style.display = this.value === 'ewallet' ? 'block' : 'none';
    });

    // Midtrans Logic
    btnMidtrans?.addEventListener('click', function() {
        const idSewa = selectSewa.value;
        if (!idSewa) return alert('Pilih sewa terlebih dahulu');

        fetch(window.baseUrl + '/admin/pembayaran/getSnapToken/' + idSewa)
            .then(r => r.json())
            .then(data => {
                if (data.token) {
                    window.snap.pay(data.token, {
                        onSuccess: (res) => window.location.reload(),
                        onPending: (res) => window.location.reload(),
                        onError: (res) => alert('Pembayaran Gagal')
                    });
                }
            });
    });

    // Sewa selection - fetch sisa bayar from backend
    selectSewa?.addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        const id = this.value;
        if (!id) return;

        document.getElementById('infoIdSewa').textContent = '#' + id;
        document.getElementById('infoNamaPelanggan').textContent = opt.dataset.nama + ' - ' + opt.dataset.mobil;

        fetch(window.baseUrl + '/admin/pembayaran/getSisaBayar/' + id)
            .then(r => r.json())
            .then(data => {
                // Show midtrans button if there is outstanding amount
                btnMidtrans.style.display = data.sisa_bayar > 0 ? 'block' : 'none';
                
                const fmt = n => new Intl.NumberFormat('id-ID').format(n);
                document.getElementById('infoTotalTagihan').textContent = 'Rp ' + fmt(data.total_tagihan);
                document.getElementById('infoTotalDenda').textContent = 'Rp ' + fmt(data.total_denda || 0);
                document.getElementById('infoSisaBayar').textContent = 'Rp ' + fmt(data.sisa_bayar);
                document.getElementById('summaryTotal').textContent = 'Rp ' + fmt(data.total_tagihan);
                document.getElementById('summaryDenda').textContent = 'Rp ' + fmt(data.total_denda || 0);
                document.getElementById('summaryPaid').textContent = 'Rp ' + fmt(data.total_dibayar);
                document.getElementById('summarySisa').textContent = 'Rp ' + fmt(data.sisa_bayar);
                jumlahBayar.value = data.sisa_bayar;
            })
            .catch(err => console.error('Error:', err));
    });

    // Trigger initial load if a sewa is pre-selected
    if (selectSewa && selectSewa.value) {
        selectSewa.dispatchEvent(new Event('change'));
    }
});
</script>

<?= $this->endSection() ?>