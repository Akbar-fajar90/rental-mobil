<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php 
/**
 * @var \stdClass $summary Summary data (pendapatan, penyewaan, denda, utilisasi)
 * @var array<int, \stdClass> $laporan Array of rental records
 * @var array $filters Applied filters (start_date, end_date, status, search)
 * @var int $current_page Current page number
 * @var int $total_pages Total number of pages
 * @var int $total_data Total records count
 * @var int $per_page Records per page
 * @var array $chart_data Weekly revenue data for chart
 */

// Provide defaults if not set
$summary = $summary ?? (object)['pendapatan' => 0, 'penyewaan' => 0, 'denda' => 0, 'utilisasi' => 0];
$laporan = $laporan ?? [];
$filters = $filters ?? [];
$current_page = $current_page ?? 1;
$total_pages = $total_pages ?? 1;
$total_data = $total_data ?? 0;
$chart_data = $chart_data ?? array_fill(0, 7, 0);

helper('asset'); 
?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Laporan Operasional</h4>
        <p class="text-muted small">Detail Riwayat Rental Mobil Online</p>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="stat-card border-start border-primary border-3" style="border-radius: 12px;">
                <div class="stat-label text-primary">💰 Total Pendapatan</div>
                <div class="stat-value text-primary">Rp <?= number_format($summary?->pendapatan ?? 0, 0, ',', '.') ?></div>
                <div class="stat-sub mt-2"><span class="badge bg-success">▲ +12.4%</span> vs bulan lalu</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-info border-3" style="border-radius: 12px;">
                <div class="stat-label text-info">📋 Total Penyewaan</div>
                <div class="stat-value text-info"><?= $summary?->penyewaan ?? 0 ?></div>
                <div class="stat-sub mt-2"><span class="badge bg-info">⊕ Rerata 4.3/hari</span></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-danger border-3" style="border-radius: 12px;">
                <div class="stat-label text-danger">⚠️ Akumulasi Denda</div>
                <div class="stat-value text-danger">Rp <?= number_format($summary?->denda ?? 0, 0, ',', '.') ?></div>
                <div class="stat-sub mt-2"><span class="badge bg-warning">12 kasus</span> keterlambatan</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card border-start border-success border-3" style="border-radius: 12px;">
                <div class="stat-label text-success">✓ Status Armada</div>
                <div class="stat-value text-success"><?= $summary?->utilisasi ?? 0 ?>%</div>
                <div class="stat-sub mt-2"><span class="badge bg-success">Optimal</span></div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card-custom mb-4 shadow-sm">
        <div class="card-header-custom border-bottom bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <span class="fw-bold text-white" style="font-size: 1rem;"><i class="bi bi-funnel me-2"></i>Filter Laporan</span>
        </div>
        <div class="p-4 bg-white rounded-bottom">
            <div class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">📅 Periode Awal</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" id="startDate" value="<?= $filters['start_date'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">📅 Periode Akhir</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" id="endDate" value="<?= $filters['end_date'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted fw-bold">🏷️ Status</label>
                    <select name="status" class="form-select form-select-sm" id="statusFilter">
                        <option value="">Semua Status</option>
                        <option value="Berlangsung" <?= ($filters['status'] ?? '') == 'Berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
                        <option value="selesai" <?= ($filters['status'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
                        <option value="batal" <?= ($filters['status'] ?? '') == 'batal' ? 'selected' : '' ?>>Batal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold">🔍 Pencarian</label>
                    <input type="text" class="form-control form-control-sm" id="searchInput" value="<?= $filters['search'] ?? '' ?>" placeholder="Nama pelanggan/mobil...">
                </div>
                <div class="col-md-3 text-end">
                    <div class="btn-group w-100" role="group">
                        <button class="btn btn-sm btn-outline-secondary" id="btnResetFilter"><i class="bi bi-arrow-counterclockwise me-1"></i> Reset</button>
                        <button class="btn btn-sm btn-primary" id="btnApplyFilter"><i class="bi bi-funnel-fill me-1"></i> Terapkan</button>
                        <button class="btn btn-sm btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-download me-1"></i> Export
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" id="btnExportExcel"><i class="bi bi-file-earmark-excel text-success me-2"></i> Excel</a></li>
                            <li><a class="dropdown-item" href="#" id="btnExportPdf"><i class="bi bi-file-earmark-pdf text-danger me-2"></i> PDF</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card-custom shadow-sm">
        <div class="card-header-custom border-bottom bg-white">
            <span class="fw-bold text-dark">📊 Data Riwayat Penyewaan</span>
        </div>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead class="table-light">
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
                                    <div class="avatar" style="background: <?= '#' . substr(md5((string)($row->nama_pelanggan ?? '')), 0, 6) ?>">
                                        <?= strtoupper(substr((string)($row->nama_pelanggan ?? 'U'), 0, 1)) ?>
                                    </div>
                                    <?= esc((string)($row->nama_pelanggan ?? '')) ?>
                                </div>
                            </td>
                            <td><?= esc((string)($row->mobil_merk ?? '')) ?></td>
                            <td><span class="text-muted small"><?= esc((string)($row->plat_nomor ?? '-')) ?></span></td>
                            <td><span class="amount">Rp <?= number_format($row->pendapatan ?? 0, 0, ',', '.') ?></span></td>
                            <td>
                                <?php $totalDenda = ($row->denda_terlambat ?? 0) + ($row->denda_kerusakan ?? 0); ?>
                                <?php if ($totalDenda > 0): ?>
                                    <span class="badge bg-danger">Rp <?= number_format($totalDenda, 0, ',', '.') ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success">Rp 0</span>
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
        <div class="card-header-custom border-top d-flex justify-content-between align-items-center bg-light">
            <small class="text-muted">📊 Menampilkan <strong><?= count($laporan ?? []) ?></strong> dari <strong><?= $total_data ?? 0 ?></strong> laporan</small>
            <div class="pagination" id="pagination">
                <?php if (($current_page ?? 1) > 1): ?>
                    <button class="pg-btn" data-page="<?= ($current_page ?? 1) - 1 ?>">‹</button>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= min(5, $total_pages ?? 1); $i++): ?>
                    <button class="pg-btn <?= $i == ($current_page ?? 1) ? 'active' : '' ?>" data-page="<?= $i ?>"><?= $i ?></button>
                <?php endfor; ?>
                
                <?php if (($total_pages ?? 1) > 5): ?>
                    <span>...</span>
                    <button class="pg-btn" data-page="<?= $total_pages ?? 1 ?>"><?= $total_pages ?? 1 ?></button>
                <?php endif; ?>
                
                <?php if (($current_page ?? 1) < ($total_pages ?? 1)): ?>
                    <button class="pg-btn" data-page="<?= ($current_page ?? 1) + 1 ?>">›</button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card-custom mt-4 shadow-sm">
        <div class="card-header-custom border-bottom bg-white">
            <div>
                <div class="fw-bold">📈 Analisis Tren Pendapatan</div>
                <small class="text-muted">Pendapatan mingguan dalam juta Rupiah</small>
            </div>
        </div>
        <div class="p-4" style="background: linear-gradient(135deg, #f5f7fa 0%, #fafbfc 100%);">
            <div class="bar-chart" id="barChart">
                <?php
                $days = ['SEN', 'SEL', 'RAB', 'KAM', 'JUM', 'SAB', 'MIN'];
                $chartDataArray = $chart_data ?? array_fill(0, 7, 0);
                $maxChart = !empty($chartDataArray) ? max($chartDataArray) : 1;
                foreach ($chartDataArray as $i => $val):
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