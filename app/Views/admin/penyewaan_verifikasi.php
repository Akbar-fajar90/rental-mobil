<?= $this->extend('layout/main') ?>

<?php /** @var object $sewa */ ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="m-0 fw-bold text-primary">Verifikasi Dokumen Penyewaan #<?= (string)$sewa->id_sewa ?></h5>
                        <a href="<?= base_url('admin/penyewaan') ?>" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-borderless sm">
                                <tr>
                                    <td width="150" class="text-muted">Pelanggan</td>
                                    <td class="fw-bold">: <?= esc((string)$sewa->nama_pelanggan) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">NIK</td>
                                    <td class="fw-bold">: <?= esc((string)$sewa->nik) ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">No SIM</td>
                                    <td class="fw-bold">: <?= esc((string)($sewa->no_sim ?: 'Tidak diisi')) ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info border-0 py-2">
                                <i class="bi bi-info-circle me-2"></i> Periksa apakah data pada gambar sesuai dengan data yang diinput pelanggan.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Card KTP -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="bg-light p-3 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold"><i class="bi bi-card-heading me-2"></i> Foto KTP / Identitas</span>
                                    <a href="<?= base_url('admin/penyewaan/download/ktp/' . $sewa->id_sewa) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                                <div class="card-body text-center p-0" style="background: #f8f9fa;">
                                    <?php 
                                    $ktp = ($sewa->dokumen_ktp ?? '') ?: ($sewa->foto_identitas ?? '');
                                    if ($ktp && file_exists('uploads/identitas/' . $ktp)): ?>
                                        <img src="<?= base_url('uploads/identitas/' . $ktp) ?>" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: contain;" alt="KTP">
                                    <?php else: ?>
                                        <div class="py-5 text-muted">
                                            <i class="bi bi-image-fill fs-1 d-block mb-2"></i>
                                            Foto KTP tidak ditemukan
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Card SIM -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="bg-light p-3 d-flex justify-content-between align-items-center">
                                    <span class="fw-bold"><i class="bi bi-card-text me-2"></i> Foto SIM A</span>
                                    <a href="<?= base_url('admin/penyewaan/download/sim/' . $sewa->id_sewa) ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                                <div class="card-body text-center p-0" style="background: #f8f9fa;">
                                    <?php if (($sewa->dokumen_sim ?? false) && file_exists('uploads/dokumen/' . $sewa->dokumen_sim)): ?>
                                        <img src="<?= base_url('uploads/dokumen/' . $sewa->dokumen_sim) ?>" class="img-fluid" style="max-height: 400px; width: 100%; object-fit: contain;" alt="SIM">
                                    <?php else: ?>
                                        <div class="py-5 text-muted">
                                            <i class="bi bi-image-fill fs-1 d-block mb-2"></i>
                                            Foto SIM tidak diupload
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Verifikasi -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body p-4">
                                    <h6 class="fw-bold mb-3">Keputusan Verifikasi</h6>
                                    <form action="<?= base_url('admin/penyewaan/submit-verifikasi/' . $sewa->id_sewa) ?>" method="post">
                                        <?= csrf_field() ?>
                                        <div class="mb-4">
                                            <div class="form-check form-check-inline me-4">
                                                <input class="form-check-input" type="radio" name="status_dokumen" id="valid" value="valid" <?= ($sewa->status_dokumen ?? '') == 'valid' ? 'checked' : '' ?> required>
                                                <label class="form-check-label text-success fw-bold" for="valid">
                                                    <i class="bi bi-check-circle-fill me-1"></i> SETUJU (DOKUMEN VALID)
                                                </label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="status_dokumen" id="invalid" value="tidak valid" <?= ($sewa->status_dokumen ?? '') == 'tidak valid' ? 'checked' : '' ?> required>
                                                <label class="form-check-label text-danger fw-bold" for="invalid">
                                                    <i class="bi bi-x-circle-fill me-1"></i> TIDAK VALID
                                                </label>
                                            </div>
                                        </div>
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">Catatan Verifikasi / Alasan Penolakan</label>
                                            <textarea name="catatan_dokumen" class="form-control" rows="3" placeholder="Contoh: Foto KTP kurang jelas, Nama tidak sesuai, dsb."><?= esc((string)($sewa->catatan_dokumen ?? '')) ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                                            <i class="bi bi-save me-2"></i> SIMPAN HASIL VERIFIKASI
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
