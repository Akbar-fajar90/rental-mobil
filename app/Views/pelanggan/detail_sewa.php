<?= $this->extend('layout/landing'); ?>

<?php
/**
 * @var array $sewa Rental transaction details
 * @var array $wiki External car information
 */

// Default values to prevent "undefined variable" or "undefined index" errors
$sewa = $sewa ?? [
    'id_sewa'             => 0,
    'tgl_pengajuan'       => date('Y-m-d H:i:s'),
    'status'              => 'batal',
    'status_pengajuan'    => 'ditolak',
    'foto_mobil'          => null,
    'merk'                => 'Tidak Diketahui',
    'tarif_per_hari'      => 0,
    'plat_nomor'          => '-',
    'tgl_sewa'            => date('Y-m-d'),
    'tgl_kembali_rencana' => date('Y-m-d'),
    'total_hari'          => 0,
    'sub_total'           => 0,
    'status_bayar'        => 'belum_lunas',
    'metode_bayar'        => '-',
    'tgl_kembali'         => null,
    'denda'               => 0,
    'catatan_admin'       => '',
    'dokumen_ktp'         => null,
    'dokumen_sim'         => null,
];
$wiki = $wiki ?? ['extract' => 'Informasi tambahan tidak tersedia saat ini.'];
?>

<?= $this->section('content'); ?>

<section class="py-5 bg-light">
    <div class="container">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Beranda</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('/riwayat') ?>">Riwayat Sewa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Detail Sewa #<?= esc((string)$sewa['id_sewa']) ?></li>
            </ol>
        </nav>

        <div class="row g-4">
            <!-- Left Side: Detail Information -->
            <div class="col-lg-8">
                <!-- Status Card -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold mb-1">Status Pengajuan</h4>
                            <p class="text-muted small mb-0">Diajukan pada: <?= date('d M Y, H:i', strtotime($sewa['tgl_pengajuan'])) ?></p>
                        </div>
                        <div class="text-end">
                            <?php 
                                $badge = 'bg-warning text-dark';
                                $status_label = 'Menunggu Konfirmasi';
                                
                                if ($sewa['status'] == 'selesai') {
                                    $badge = 'bg-info text-white';
                                    $status_label = 'Sewa Selesai';
                                } elseif ($sewa['status_pengajuan'] == 'disetujui') {
                                    $badge = 'bg-success text-white';
                                    $status_label = 'Pengajuan Disetujui';
                                } elseif ($sewa['status_pengajuan'] == 'ditolak') {
                                    $badge = 'bg-danger text-white';
                                    $status_label = 'Pengajuan Ditolak';
                                }
                            ?>
                            <span class="badge <?= $badge ?> px-4 py-2 fs-6" style="border-radius: 10px;"><?= $status_label ?></span>
                        </div>
                    </div>
                </div>

                <!-- Car & Rental Detail -->
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header bg-white p-4 border-0">
                        <h5 class="fw-bold mb-0">Informasi Unit & Penyewaan</h5>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="row g-4">
                            <div class="col-md-5">
                                <img src="<?= getCarImage((string)$sewa['foto_mobil'], (string)$sewa['merk']) ?>" class="img-fluid rounded-4 shadow-sm mb-3">
                                <h5 class="fw-bold mb-1"><?= esc((string)$sewa['merk']) ?></h5>
                                <p class="text-primary fw-bold mb-3">Rp <?= number_format((float)$sewa['tarif_per_hari'], 0, ',', '.') ?> / Hari</p>
                                
                                <div class="bg-light p-3 rounded-4">
                                    <h6 class="fw-bold small mb-2">Wiki Info:</h6>
                                    <p class="small text-muted mb-0"><?= esc(substr((string)$wiki['extract'], 0, 150)) ?>...</p>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="table-responsive">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td class="text-muted small py-2" width="40%">Plat Nomor</td>
                                            <td class="fw-bold py-2"><?= esc((string)$sewa['plat_nomor']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small py-2">Tanggal Sewa</td>
                                            <td class="fw-bold py-2"><?= date('d M Y', strtotime($sewa['tgl_sewa'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small py-2">Tanggal Kembali (Rencana)</td>
                                            <td class="fw-bold py-2"><?= date('d M Y', strtotime($sewa['tgl_kembali_rencana'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted small py-2">Durasi Sewa</td>
                                            <td class="fw-bold py-2"><?= $sewa['total_hari'] ?> Hari</td>
                                        </tr>
                                        <tr class="border-top">
                                            <td class="text-muted fw-bold py-3">Total Biaya Sewa</td>
                                            <td class="fw-bold text-primary fs-5 py-3">Rp <?= number_format((float)$sewa['sub_total'], 0, ',', '.') ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment & Return Info (Conditional) -->
                <?php if ($sewa['status_pengajuan'] == 'disetujui') : ?>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                            <div class="card-body p-4 text-center">
                                <h6 class="fw-bold text-muted mb-3">Status Pembayaran</h6>
                                <?php if ($sewa['status_bayar'] == 'lunas') : ?>
                                    <div class="text-success mb-2 fs-1"><i class="bi bi-check-circle-fill"></i></div>
                                    <h5 class="fw-bold text-success">Lunas</h5>
                                    <p class="small text-muted">Metode: <?= esc((string)$sewa['metode_bayar']) ?></p>
                                <?php else : ?>
                                    <div class="text-warning mb-2 fs-1"><i class="bi bi-clock-history"></i></div>
                                    <h5 class="fw-bold text-warning">Belum Lunas</h5>
                                    <a href="<?= base_url('/payment/checkout/' . $sewa['id_sewa']) ?>" class="btn btn-primary btn-sm mt-2 px-3 shadow-sm" style="border-radius: 8px;">Bayar Sekarang</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                            <div class="card-body p-4 text-center">
                                <h6 class="fw-bold text-muted mb-3">Pengembalian Unit</h6>
                                <?php if ($sewa['status'] == 'selesai') : ?>
                                    <div class="text-primary mb-2 fs-1"><i class="bi bi-car-front-fill"></i></div>
                                    <h5 class="fw-bold text-primary">Sudah Dikembalikan</h5>
                                    <p class="small text-muted mb-0">Tgl: <?= date('d M Y', strtotime($sewa['tgl_kembali'])) ?></p>
                                    <p class="small text-danger fw-bold">Denda: Rp <?= number_format((float)($sewa['denda'] ?? 0), 0, ',', '.') ?></p>
                                <?php else : ?>
                                    <div class="text-muted mb-2 fs-1"><i class="bi bi-hourglass-split"></i></div>
                                    <h5 class="fw-bold text-muted">Masih Digunakan</h5>
                                    <p class="small text-muted">Harap kembalikan tepat waktu.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Side: Actions & Documents -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Aksi Penyewaan</h5>
                        <div class="d-grid gap-2">
                            <?php if ($sewa['status_pengajuan'] == 'disetujui') : ?>
                                <a href="<?= base_url('/riwayat/invoice/' . esc((string)$sewa['id_sewa'])) ?>" target="_blank" class="btn btn-outline-dark py-3 fw-bold" style="border-radius: 12px;">
                                    <i class="bi bi-printer me-2"></i> Cetak Invoice
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($sewa['status_pengajuan'] == 'mengajukan') : ?>
                                <div class="alert alert-warning border-0 small text-center" style="border-radius: 12px;">
                                    <i class="bi bi-info-circle-fill me-2"></i> Pengajuan sedang diproses oleh admin.
                                </div>
                            <?php elseif ($sewa['status_pengajuan'] == 'ditolak') : ?>
                                <div class="alert alert-danger border-0 small" style="border-radius: 12px;">
                                    <h6 class="fw-bold">Alasan Penolakan:</h6>
                                    <p class="mb-0"><?= esc((string)($sewa['catatan_admin'] ?: 'Tidak ada catatan.')) ?></p>
                                </div>
                            <?php endif; ?>
                            
                            <a href="<?= base_url('/riwayat') ?>" class="btn btn-light py-3 fw-bold" style="border-radius: 12px;">Kembali ke Riwayat</a>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-4">Dokumen Pengajuan</h5>
                        <div class="mb-4 pb-3 border-bottom">
                            <label class="form-label small fw-bold text-muted">DOKUMEN KTP</label>
                            <?php if ($sewa['dokumen_ktp']) : ?>
                                <a href="<?= base_url('uploads/identitas/' . $sewa['dokumen_ktp']) ?>" target="_blank">
                                    <img src="<?= base_url('uploads/identitas/' . $sewa['dokumen_ktp']) ?>" class="img-fluid rounded-3 border">
                                </a>
                            <?php else : ?>
                                <div class="bg-light p-3 text-center rounded-3 small text-muted">KTP belum diunggah</div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label class="form-label small fw-bold text-muted">DOKUMEN SIM</label>
                            <?php if ($sewa['dokumen_sim']) : ?>
                                <a href="<?= base_url('uploads/dokumen/' . $sewa['dokumen_sim']) ?>" target="_blank">
                                    <img src="<?= base_url('uploads/dokumen/' . $sewa['dokumen_sim']) ?>" class="img-fluid rounded-3 border">
                                </a>
                            <?php else : ?>
                                <div class="bg-light p-3 text-center rounded-3 small text-muted">SIM belum diunggah</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection(); ?>
