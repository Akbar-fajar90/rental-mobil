<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Laporan Operasional</h4>
        <p class="text-muted small">Detail Riwayat Rental Mobil Online</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Pendapatan</div>
                <div class="stat-value">Rp <?= number_format($summary->pendapatan, 0, ',', '.') ?></div>
                <div class="stat-sub"><span class="up">▲ +12.4%</span> vs bulan lalu</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Total Penyewaan</div>
                <div class="stat-value"><?= $summary->penyewaan ?> <span style="font-size:1rem;">Sewa</span></div>
                <div class="stat-sub"><span class="up">⊕</span> Rerata 4.3 sewa/hari</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Akumulasi Denda</div>
                <div class="stat-value">Rp <?= number_format($summary->denda, 0, ',', '.') ?></div>
                <div class="stat-sub"><span class="warn">⚠</span> 12 kasus keterlambatan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Status Armada</div>
                <div class="stat-value"><?= $summary->utilisasi ?>% <span style="font-size:.9rem;">Utilisasi</span></div>
                <div class="stat-sub"><span class="up">✓</span> Performa Optimal</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="fw-bold">Filter Laporan</span>
            <div class="filter-group">
                <input type="date" name="start_date" class="filter-input" id="startDate" value="<?= $filters['start_date'] ?? '' ?>" placeholder="Dari Tanggal">
                <span>—</span>
                <input type="date" name="end_date" class="filter-input" id="endDate" value="<?= $filters['end_date'] ?? '' ?>" placeholder="Sampai Tanggal">
                <select name="status" class="filter-input" id="statusFilter">
                    <option value="">Semua Status</option>
                    <option value="Berlangsung" <?= ($filters['status'] ?? '') == 'Berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
                    <option value="selesai" <?= ($filters['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                    <option value="batal" <?= ($filters['status'] ?? '') == 'batal' ? 'selected' : '' ?>>Batal</option>
                </select>
                <input type="text" class="filter-input" id="searchInput" value="<?= $filters['search'] ?? '' ?>" placeholder="Cari pelanggan/mobil..." style="width: 200px;">
                <button class="btn-filter" id="btnApplyFilter"><i class="bi bi-search me-1"></i> Terapkan</button>
                <button class="btn-reset" id="btnResetFilter"><i class="bi bi-arrow-repeat me-1"></i> Reset</button>
                <div class="dropdown d-inline">
                    <button class="btn-export dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i> Export
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" id="btnExportExcel"><i class="bi bi-file-earmark-excel text-success me-2"></i> Export ke Excel</a></li>
                        <li><a class="dropdown-item" href="#" id="btnExportPdf"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> Export ke PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="fw-bold">Data Riwayat Penyewaan</span>
        </div>
        <div class="table-responsive">
            <table class="table-custom">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>ID Sewa</th>
                        <th>Pelanggan</th>
                        <th>Mobil</th>
                        <th>Plat Nomor</th>
                        <th>Pendapatan</th>
                        <th>Denda</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (!empty($laporan)): ?>
                        <?php foreach ($laporan as $row): ?>
                        <tr>
                            <td class="text-muted small"><?= date('d/m/Y', strtotime($row->tgl_sewa)) ?></td>
                            <td><span class="trx-id">#<?= $row->id_sewa ?></span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar" style="background: <?= '#' . substr(md5($row->nama_pelanggan), 0, 6) ?>">
                                        <?= strtoupper(substr($row->nama_pelanggan, 0, 1)) ?>
                                    </div>
                                    <?= esc($row->nama_pelanggan) ?>
                                </div>
                            </td>
                            <td><?= esc($row->mobil_merk) ?></td>
                            <td><span class="text-muted small"><?= esc($row->plat_nomor ?? '-') ?></span></td>
                            <td><span class="amount">Rp <?= number_format($row->pendapatan ?? 0, 0, ',', '.') ?></span></td>
                            <td>
                                <?php $totalDenda = ($row->denda_terlambat ?? 0) + ($row->denda_kerusakan ?? 0); ?>
                                <?php if ($totalDenda > 0): ?>
                                    <span class="denda">Rp <?= number_format($totalDenda, 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="no-denda">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $statusClass = '';
                                $statusText = '';
                                if ($row->status == 'selesai') {
                                    $statusClass = 'badge bg-success';
                                    $statusText = 'Selesai';
                                } elseif ($row->status == 'Berlangsung') {
                                    $statusClass = 'badge bg-primary';
                                    $statusText = 'Berlangsung';
                                } else {
                                    $statusClass = 'badge bg-danger';
                                    $statusText = 'Batal';
                                }
                                ?>
                                <span class="<?= $statusClass ?>"><?= $statusText ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">Belum ada data laporan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-header-custom border-top">
            <small class="text-muted">Menampilkan <?= count($laporan) ?> dari <?= $total_data ?> laporan</small>
            <div class="pagination" id="pagination">
                <?php if ($current_page > 1): ?>
                    <button class="pg-btn" data-page="<?= $current_page - 1 ?>">‹</button>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= min(5, $total_pages); $i++): ?>
                    <button class="pg-btn <?= $i == $current_page ? 'active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
                <?php endfor; ?>
                
                <?php if ($total_pages > 5): ?>
                    <span>...</span>
                    <button class="pg-btn" data-page="<?= $total_pages ?>"><?= $total_pages ?></button>
                <?php endif; ?>
                
                <?php if ($current_page < $total_pages): ?>
                    <button class="pg-btn" data-page="<?= $current_page + 1 ?>">›</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card-custom">
        <div class="card-header-custom">
            <div>
                <div class="fw-bold">Analisis Tren Pendapatan</div>
                <small class="text-muted">Pendapatan mingguan dalam juta Rupiah</small>
            </div>
        </div>
        <div class="p-4">
            <div class="bar-chart" id="barChart">
                <?php
                $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
                $maxChart = !empty($chart_data) ? max($chart_data) : 1;
                foreach ($chart_data as $i => $val):
                    $pct = $maxChart > 0 ? round(($val / $maxChart) * 100) : 0;
                    $highlight = ($val == $maxChart && $val > 0) ? 'highlight' : '';
                ?>
                <div class="bar-group">
                    <div class="bar <?= $highlight ?>" style="height: <?= $pct ?>%;"></div>
                    <div class="bar-label"><?= $days[$i] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<script>
    window.baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>

<?= $this->endSection() ?>