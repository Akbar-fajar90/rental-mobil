<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>
<?php 
/** @var array $pending_requests */
/** @var array $history */
?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Konfirmasi Penyewaan</h4>
        <p class="text-muted small">Kelola persetujuan penyewaan unit armada terbaru</p>
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

    <div class="row g-4 mb-5">
        <!-- Booking Cards -->
        <div class="col-lg-4">
            <?php if (!empty($pending_requests)): ?>
                <?php foreach ($pending_requests as $request): ?>
                <div class="card booking-card mb-4" id="booking-<?= $request->id_sewa ?>">
                    <div class="position-relative">
                        <span class="floating-badge">MENUNGGU</span>
                        <img src="<?= getCarImage($request->foto_mobil, $request->merk) ?>" 
                             class="car-preview-img" 
                             alt="<?= esc((string)$request->merk) ?>"
                             onerror="this.src='https://via.placeholder.com/600x200/e9ecef/6c757d?text=No+Image'">
                        <div class="position-absolute bottom-0 start-0 p-3 text-white">
                            <small class="d-block opacity-75" style="font-size: 0.65rem;">BOOKING #<?= sprintf('SW%03d', $request->id_sewa) ?></small>
                            <h6 class="fw-bold m-0"><?= esc((string)$request->merk) ?></h6>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="<?= base_url('uploads/identitas/' . $request->foto_identitas) ?>" 
                                 class="cust-avatar" 
                                 style="width: 40px; height: 40px;"
                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= urlencode($request->nama_pelanggan) ?>&background=0d6efd&color=fff'">
                            <div class="flex-grow-1">
                                <small class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Customer</small>
                                <span class="fw-bold small"><?= esc((string)$request->nama_pelanggan) ?></span>
                            </div>
                            <div class="ms-2">
                                <?php if (($request->status_dokumen ?? '') == 'valid'): ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2" style="font-size: 0.6rem;">VALID</span>
                                <?php elseif (($request->status_dokumen ?? '') == 'tidak valid'): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2" style="font-size: 0.6rem;">INVALID</span>
                                <?php else: ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2" style="font-size: 0.6rem;">BELUM DICEK</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if (($request->status_dokumen ?? '') == 'tidak valid'): ?>
                            <div class="alert alert-danger py-2 px-3 border-0 small mb-4" style="font-size: 0.7rem;">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> <strong>Dokumen Tidak Valid:</strong> <?= esc((string)($request->catatan_dokumen ?? '')) ?>
                            </div>
                        <?php endif; ?>

                        <div class="row g-2 mb-4">
                            <div class="col-12">
                                <a href="<?= base_url('admin/penyewaan/verifikasi/' . $request->id_sewa) ?>" class="btn btn-sm btn-outline-primary w-100 fw-bold py-2" style="font-size: 0.75rem; border-radius: 8px;">
                                    <i class="bi bi-shield-check me-2"></i> VERIFIKASI DOKUMEN
                                </a>
                            </div>
                        </div>

                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="bg-light p-2 rounded-3 text-center">
                                    <small class="text-muted d-block" style="font-size: 0.6rem; font-weight: 700;">TANGGAL SEWA</small>
                                    <span class="fw-bold" style="font-size: 0.75rem;"><?= date('d M Y', strtotime($request->tgl_sewa)) ?></span>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light p-2 rounded-3 text-center">
                                    <small class="text-muted d-block" style="font-size: 0.6rem; font-weight: 700;">DURASI</small>
                                    <span class="fw-bold" style="font-size: 0.75rem;"><?= $request->total_hari ?> Hari</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <small class="text-muted" style="font-size: 0.75rem;">Total Rental Cost</small>
                            <h5 class="fw-bold text-primary m-0">Rp <?= number_format($request->sub_total, 0, ',', '.') ?></h5>
                        </div>

                        <div class="row g-2">
                            <div class="col-4">
                                <button class="btn btn-outline-danger w-100 fw-bold border-2 btn-reject" 
                                        style="font-size: 0.7rem; border-radius: 8px;"
                                        data-id="<?= (string)$request->id_sewa ?>"
                                        data-nama="<?= esc((string)$request->nama_pelanggan) ?>"
                                        data-merk="<?= esc((string)$request->merk) ?>">
                                    TOLAK
                                </button>
                            </div>
                            <div class="col-8">
                                <button class="btn btn-primary w-100 fw-bold btn-approve" 
                                        style="font-size: 0.7rem; border-radius: 8px;"
                                        data-id="<?= (string)$request->id_sewa ?>"
                                        data-nama="<?= esc((string)$request->nama_pelanggan) ?>"
                                        data-merk="<?= esc((string)$request->merk) ?>">
                                    KONFIRMASI SEWA
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                    Tidak ada pengajuan penyewaan yang menunggu
                </div>
            <?php endif; ?>
        </div>

        <div class="col-lg-8">
            <div class="card section-card mb-4">
                <small class="stat-label mb-3 d-block">Status Ringkasan Antrean</small>
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="stat-box bg-light border-start border-primary border-4">
                            <span class="stat-number text-primary"><?= $stats->menunggu ?? 0 ?></span>
                            <span class="stat-label">Menunggu</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box bg-light">
                            <span class="stat-number text-success"><?= $stats->disetujui ?? 0 ?></span>
                            <span class="stat-label">Disetujui</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box bg-light">
                            <span class="stat-number text-danger"><?= $stats->ditolak ?? 0 ?></span>
                            <span class="stat-label">Ditolak</span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="stat-box bg-primary text-white shadow-sm">
                            <span class="stat-number"><?= $stats->total ?? 0 ?></span>
                            <span class="stat-label text-white-50">Total Data</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="guidance-box shadow-sm">
                <div class="bg-white p-2 rounded-3 text-primary shadow-sm"><i class="bi bi-info-circle-fill"></i></div>
                <div>
                    <h6 class="fw-bold small mb-1">Panduan Verifikasi</h6>
                    <p class="text-muted m-0" style="font-size: 0.75rem; line-height: 1.5;">Pastikan dokumen identitas pelanggan telah divalidasi dan ketersediaan unit mobil telah dikonfirmasi sebelum menekan tombol Confirm Rental.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Approval -->
    <div class="card section-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h5 class="fw-bold m-0">Riwayat Persetujuan</h5>
                <p class="text-muted extra-small m-0">Log aktivitas persetujuan penyewaan terkini</p>
            </div>
            <div class="d-flex gap-2">
                <div class="position-relative">
                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.75rem;"></i>
                    <input type="text" class="form-control form-control-sm ps-5 bg-light border-0" placeholder="Cari riwayat..." id="searchHistory" style="width: 200px; border-radius: 8px;">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-history" id="historyTable">
                <thead>
                    <tr>
                        <th>ID Sewa</th>
                        <th>Nama Pelanggan</th>
                        <th>Mobil</th>
                        <th>Tanggal Sewa</th>
                        <th class="text-center">Dokumen</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($history)): ?>
                        <?php foreach ($history as $row): ?>
                        <tr>
                            <td class="fw-bold text-muted">#<?= sprintf('SW%03d', $row->id_sewa) ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode((string)$row->nama_pelanggan) ?>&background=0d6efd&color=fff" class="cust-avatar">
                                    <span class="fw-bold"><?= esc((string)$row->nama_pelanggan) ?></span>
                                </div>
                            </td>
                            <td class="text-muted"><?= esc((string)$row->merk) ?></td>
                            <td class="text-muted"><?= date('d M Y', strtotime($row->tgl_sewa)) ?></td>
                            <td class="text-center">
                                <?php if (($row->status_dokumen ?? '') == 'valid'): ?>
                                    <span class="badge bg-success-subtle text-success" style="font-size: 0.65rem;">Valid</span>
                                <?php elseif (($row->status_dokumen ?? '') == 'tidak valid'): ?>
                                    <span class="badge bg-danger-subtle text-danger" style="font-size: 0.65rem;">Invalid</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-muted" style="font-size: 0.65rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($row->status_pengajuan == 'disetujui'): ?>
                                    <span class="status-badge-mini lbl-approved">Disetujui</span>
                                <?php else: ?>
                                    <span class="status-badge-mini lbl-rejected">Ditolak</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="#" class="text-primary text-decoration-none fw-bold" style="font-size: 0.75rem;">LIHAT SEMUA RIWAYAT</a>
        </div>
    </div>
</div>

<!-- Modal Cek Dokumen -->
<div class="modal fade" id="modalDokumen" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-check me-2"></i> Validasi Dokumen Pelanggan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modalDokumenBody">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">Memuat data dokumen...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Approve -->
<div class="modal fade" id="modalApprove" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Persetujuan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formApprove" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menyetujui penyewaan ini?</p>
                    <p><strong id="approveNama"></strong> - <strong id="approveMerk"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="catatan" class="form-control" rows="2" placeholder="Tambahkan catatan..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Ya, Setujui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Reject -->
<div class="modal fade" id="modalReject" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formReject" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak penyewaan ini?</p>
                    <p><strong id="rejectNama"></strong> - <strong id="rejectMerk"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan *</label>
                        <textarea name="alasan" class="form-control" rows="2" placeholder="Masukkan alasan penolakan..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    window.baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>

<?= $this->endSection() ?>