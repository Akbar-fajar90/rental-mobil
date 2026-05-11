<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice Pembayaran</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; }
        .header { text-align: center; margin-bottom: 30px; }
        .row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .total { font-size: 18px; font-weight: bold; margin-top: 20px; padding-top: 10px; border-top: 2px solid #333; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="invoice-box">
        <div class="header">
            <h2>INVOICE PEMBAYARAN</h2>
            <p>Sistem Rental Mobil</p>
        </div>

        <?php if (isset($payment) && $payment): ?>
        <div class="row">
            <span><strong>ID Pembayaran:</strong></span>
            <span>#<?= $payment->id_pembayaran ?></span>
        </div>
        <div class="row">
            <span><strong>ID Sewa:</strong></span>
            <span>#<?= $payment->id_sewa ?></span>
        </div>
        <div class="row">
            <span><strong>Pelanggan:</strong></span>
            <span><?= esc($payment->nama_pelanggan) ?></span>
        </div>
        <div class="row">
            <span><strong>Mobil:</strong></span>
            <span><?= esc($payment->mobil_merk) ?> (<?= esc($payment->plat_nomor) ?>)</span>
        </div>
        <div class="row">
            <span><strong>Tanggal Bayar:</strong></span>
            <span><?= date('d/m/Y H:i:s', strtotime($payment->tgl_bayar)) ?></span>
        </div>
        <div class="row">
            <span><strong>Metode Bayar:</strong></span>
            <span><?= ucfirst($payment->metode_bayar) ?></span>
        </div>
        <div class="row">
            <span><strong>Jumlah Bayar:</strong></span>
            <span>Rp <?= number_format($payment->jumlah_bayar, 0, ',', '.') ?></span>
        </div>
        <div class="row">
            <span><strong>Status:</strong></span>
            <span><?= strtoupper($payment->status_bayar) ?></span>
        </div>

        <div class="total">
            <div class="row">
                <span><strong>TOTAL PEMBAYARAN</strong></span>
                <span><strong>Rp <?= number_format($payment->jumlah_bayar, 0, ',', '.') ?></strong></span>
            </div>
        </div>
        <?php else: ?>
        <p class="text-center">Data pembayaran tidak ditemukan</p>
        <?php endif; ?>

        <div class="no-print" style="margin-top: 30px; text-align: center;">
            <button onclick="window.print()" class="btn btn-primary">Cetak Invoice</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</body>
</html>