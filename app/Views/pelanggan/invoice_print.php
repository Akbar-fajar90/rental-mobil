<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $sewa['id_sewa'] ?></title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #333; line-height: 1.6; }
        .invoice-box { max-width: 800px; margin: auto; padding: 30px; border: 1px solid #eee; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); font-size: 14px; }
        .invoice-box table { width: 100%; line-height: inherit; text-align: left; border-collapse: collapse; }
        .invoice-box table td { padding: 5px; vertical-align: top; }
        .invoice-box table tr td:nth-child(2) { text-align: right; }
        .invoice-box table tr.top table td { padding-bottom: 20px; }
        .invoice-box table tr.information table td { padding-bottom: 40px; }
        .invoice-box table tr.heading td { background: #eee; border-bottom: 1px solid #ddd; font-weight: bold; }
        .invoice-box table tr.details td { padding-bottom: 20px; }
        .invoice-box table tr.item td { border-bottom: 1px solid #eee; }
        .invoice-box table tr.total td:nth-child(2) { border-top: 2px solid #eee; font-weight: bold; font-size: 18px; color: #2563eb; }
        .logo { font-size: 24px; font-weight: bold; color: #2563eb; }
        @media print {
            .no-print { display: none; }
            .invoice-box { border: none; box-shadow: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="invoice-box">
        <table>
            <tr class="top">
                <td colspan="2">
                    <table>
                        <tr>
                            <td class="logo">Rental Mobil</td>
                            <td>
                                Invoice #: <?= $sewa['id_sewa'] ?><br>
                                Tanggal: <?= date('d M Y') ?><br>
                                Status: <strong>PAID</strong>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="2">
                    <table>
                        <tr>
                            <td>
                                <strong>Penyedia Layanan:</strong><br>
                                Jl. Raya Pusat Otomotif No. 123<br>
                                Jakarta Selatan, Indonesia<br>
                                +62 887 6728 908
                            </td>
                            <td>
                                <strong>Pelanggan:</strong><br>
                                <?= session()->get('nama_pelanggan') ?><br>
                                <?= session()->get('email_pelanggan') ?><br>
                                NIK: <?= $sewa['nik'] ?? '-' ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Item / Deskripsi</td>
                <td>Biaya</td>
            </tr>
            <tr class="item">
                <td>
                    Sewa Mobil: <strong><?= $sewa['merk'] ?></strong><br>
                    <small>Plat: <?= $sewa['plat_nomor'] ?> | Durasi: <?= $sewa['total_hari'] ?> Hari</small><br>
                    <small>Periode: <?= date('d M Y', strtotime($sewa['tgl_sewa'])) ?> - <?= date('d M Y', strtotime($sewa['tgl_kembali_rencana'])) ?></small>
                </td>
                <td>Rp <?= number_format($sewa['tarif_per_hari'], 0, ',', '.') ?> x <?= $sewa['total_hari'] ?></td>
            </tr>
            <tr class="details">
                <td>Metode Pembayaran</td>
                <td><?= $sewa['metode_bayar'] ?: 'Transfer Bank' ?></td>
            </tr>
            <tr class="total">
                <td></td>
                <td>Total: Rp <?= number_format($sewa['sub_total'], 0, ',', '.') ?></td>
            </tr>
        </table>
        <div style="margin-top: 50px; text-align: center; color: #888;">
            <p>Terima kasih telah mempercayakan perjalanan Anda kepada kami!</p>
            <button onclick="window.print()" class="no-print">Cetak Lagi</button>
        </div>
    </div>
</body>
</html>
