<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #2563eb; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #2563eb; color: white; padding: 10px; border: 1px solid #ddd; }
        td { padding: 8px; border: 1px solid #ddd; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        .summary { margin-bottom: 20px; }
        .summary-item { display: inline-block; width: 23%; margin: 1%; padding: 10px; background: #f5f6fa; border-radius: 8px; }
    </style>
</head>
<body>
    <h1>LAPORAN OPERASIONAL RENTAL MOBIL</h1>
    <p style="text-align: center; color: #666;">Periode: <?= date('d/m/Y H:i:s') ?></p>
    
    <div class="summary">
        <div class="summary-item">
            <strong>Total Pendapatan</strong><br>
            Rp <?= number_format($summary->pendapatan, 0, ',', '.') ?>
        </div>
        <div class="summary-item">
            <strong>Total Penyewaan</strong><br>
            <?= $summary->penyewaan ?> Sewa
        </div>
        <div class="summary-item">
            <strong>Total Denda</strong><br>
            Rp <?= number_format($summary->denda, 0, ',', '.') ?>
        </div>
        <div class="summary-item">
            <strong>Utilisasi Armada</strong><br>
            <?= $summary->utilisasi ?>%
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>ID Sewa</th>
                <th>Pelanggan</th>
                <th>Mobil</th>
                <th>Pendapatan</th>
                <th>Denda</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($laporan as $row): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($row->tgl_sewa)) ?></td>
                <td>#<?= $row->id_sewa ?></td>
                <td><?= esc($row->nama_pelanggan) ?></td>
                <td><?= esc($row->mobil_merk) ?></td>
                <td>Rp <?= number_format($row->pendapatan ?? 0, 0, ',', '.') ?></td>
                <td>Rp <?= number_format(($row->denda_terlambat ?? 0) + ($row->denda_kerusakan ?? 0), 0, ',', '.') ?></td>
                <td><?= $row->status ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <div class="footer">
        <p>Dicetak pada: <?= date('d/m/Y H:i:s') ?></p>
        <p>Sistem Rental Mobil</p>
    </div>
</body>
</html>