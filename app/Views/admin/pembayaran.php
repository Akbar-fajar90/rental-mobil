<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
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
                                    <td><?= esc($payment->nama_pelanggan) ?></td>
                                    <td><?= esc($payment->mobil_merk) ?></td>
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
            // Data untuk konfirmasi card
            $trx = $selected_payment ?? null;
            $totalTagihan = $trx ? ($trx->total_tagihan ?? 0) : 2500000;
            $sisaBayar = $trx ? ($totalTagihan - ($trx->jumlah_bayar ?? 0)) : 1000000;
            $namaPelanggan = $trx ? ($trx->nama_pelanggan ?? 'Budi Santoso') : 'Budi Santoso';
            $idSewa = $trx ? ($trx->id_sewa ?? 'SW001') : 'SW001';
            $statusBayar = $trx ? ($trx->status_bayar ?? 'belum') : 'belum';
            ?>

            <!-- Konfirmasi Card -->
            <div class="konfirmasi-card mb-4">
                <div class="text-white small mb-2">KONFIRMASI PEMBAYARAN</div>
                <div class="fw-bold fs-5 mb-1">#<?= $idSewa ?></div>
                <div class="text-white-50 small mb-3"><?= esc($namaPelanggan) ?></div>

                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small">Total Tagihan</span>
                    <span class="fw-bold">Rp <?= number_format($totalTagihan, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-white-50 small">Sisa Bayar</span>
                    <span class="fw-bold text-warning">Rp <?= number_format($sisaBayar, 0, ',', '.') ?></span>
                </div>

                <div class="mt-3">
                    <span class="badge <?= $statusBayar == 'lunas' ? 'badge-lunas' : ($statusBayar == 'sebagian' ? 'badge-sebagian' : 'badge-belum') ?>">
                        <?= strtoupper($statusBayar) ?>
                    </span>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div class="card-custom mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Form Pembayaran</h6>

                    <form action="<?= base_url('admin/pembayaran/process') ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="form-label small text-white">ID Sewa</label>
                            <input type="text" name="id_sewa" class="form-input" value="<?= $idSewa ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-white">Jumlah Bayar</label>
                            <input type="text" name="jumlah_bayar" class="form-input" id="jumlahBayar" placeholder="0" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small text-white">Metode Pembayaran</label>
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
                            <select name="jenis_ewallet" class="form-select">
                                <option value="OVO">OVO</option>
                                <option value="GoPay">GoPay</option>
                                <option value="DANA">DANA</option>
                                <option value="LinkAja">LinkAja</option>
                            </select>
                        </div>

                        <div class="summary-box bg-dark p-3 rounded-3 mb-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white">Administrasi</span>
                                <span>Rp <span id="adminFee">5,000</span></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-white">Pajak (11%)</span>
                                <span>Rp <span id="taxAmount">0</span></span>
                            </div>
                            <hr class="my-2 border-secondary">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total Akhir</span>
                                <span class="text-primary">Rp <span id="totalAkhir">0</span></span>
                            </div>
                        </div>

                        <button type="submit" class="btn-konfirmasi">KONFIRMASI PEMBAYARAN</button>
                        <button type="button" class="btn-invoice" id="btnCetakInvoice">CETAK INVOICE</button>
                    </form>
                </div>
            </div>

            <!-- Detail Transaksi -->
            <div class="card-custom">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-3">Detail Transaksi</h6>
                    
                    <div class="mb-3">
                        <div class="text-white small">ID Transaksi Gateway</div>
                        <div class="font-monospace small"><?= $trx ? 'TRX-' . rand(100000, 999999) : '-' ?></div>
                    </div>

                    <div class="mb-3">
                        <div class="text-white small">Status Verifikasi</div>
                        <div class="badge bg-warning text-dark">⏳ Pending Konfirmasi</div>
                    </div>

                    <div class="verif-block bg-dark p-3 rounded-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-success">✅ Verifikasi Otomatis</span>
                            <span class="text-white small"><?= date('H:i:s') ?></span>
                        </div>
                        <div class="text-white small">Pembayaran telah diterima oleh payment gateway</div>
                    </div>

                    <button class="btn-admin w-100 mt-3 py-2 bg-dark border rounded-3 text-white">
                        💬 HUBUNGI ADMIN
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>

<?= $this->endSection() ?>