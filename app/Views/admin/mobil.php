<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

<div class="container-fluid p-4">
    <div class="page-header">
        <h4 class="fw-bold mb-1">Daftar Mobil</h4>
        <p class="text-muted small">Kelola ketersediaan dan rincian tarif unit kendaraan.</p>
    </div>

    <div class="row g-4">
        <!-- TABEL -->
        <div class="col-lg-8">
            <div class="table-container mb-4">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Mobil</th>
                                <th>Model</th>
                                <th>Tahun</th>
                                <th>Tarif / Hari</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($mobil_list)): ?>
                                <?php foreach ($mobil_list as $mobil): ?>
                                <tr>
                                    <td class="text-primary fw-bold"><?= sprintf('VHC-%03d', $mobil->id_mobil) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?= getCarImage($mobil->foto_mobil, $mobil->merk) ?>" 
                                                 class="car-thumb" 
                                                 alt="<?= esc($mobil->merk) ?>"
                                                 onerror="this.src='<?= base_url('assets/img/default-car.png') ?>'">
                                            <span class="fw-bold ms-2"><?= esc($mobil->merk) ?></span>
                                        </div>
                                    </td>
                                    <td><?= $mobil->tahun ?></td>
                                    <td class="fw-bold">Rp <?= number_format($mobil->tarif_per_hari, 0, ',', '.') ?></td>
                                    <td>
                                        <?php 
                                        switch($mobil->status):
                                            case 'tersedia':
                                                $status_class = 'status-tersedia';
                                                $status_text = 'Tersedia';
                                                break;
                                            case 'disewa':
                                                $status_class = 'status-disewa';
                                                $status_text = 'Disewa';
                                                break;
                                            case 'perbaikan':
                                                $status_class = 'status-perbaikan';
                                                $status_text = 'Perbaikan';
                                                break;
                                            default:
                                                $status_class = 'status-tersedia';
                                                $status_text = 'Tersedia';
                                        endswitch;
                                        ?>
                                        <span class="badge-status <?= $status_class ?>"><?= $status_text ?></span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-edit" data-id="<?= $mobil->id_mobil ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $mobil->id_mobil ?>" data-nama="<?= esc($mobil->merk) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                     </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        <i class="bi bi-car-front fs-1"></i>
                                        <p class="mt-2">Belum ada data mobil. Silakan tambah armada baru.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- STATISTIK -->
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="mini-stat">
                        <small class="text-muted d-block text-uppercase mb-1">Total Armada</small>
                        <h3 class="fw-bold m-0"><?= $total_armada ?? 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mini-stat" style="border-left-color: #28a745;">
                        <small class="text-muted d-block text-uppercase mb-1">Tersedia</small>
                        <h3 class="fw-bold m-0"><?= $total_tersedia ?? 0 ?></h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mini-stat" style="border-left-color: #f44336;">
                        <small class="text-muted d-block text-uppercase mb-1">Sedang Disewa</small>
                        <h3 class="fw-bold m-0"><?= $total_disewa ?? 0 ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORM (TAMBAH & EDIT) -->
        <div class="col-lg-4">
            <div class="card form-card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-1" id="formTitle">Tambah Armada Baru</h6>
                    <p class="text-muted small mb-4" id="formSubtitle">Masukkan rincian spesifikasi unit kendaraan baru.</p>
                    
                    <form action="<?= base_url('admin/mobil/simpan') ?>" method="POST" enctype="multipart/form-data" id="mobilForm">
                        <input type="hidden" name="id_mobil" id="id_mobil">
                        <input type="hidden" name="foto_mobil_lama" id="foto_mobil_lama">
                        
                        <div class="mb-3">
                            <label class="form-label">Plat Nomor</label>
                            <input type="text" name="plat_nomor" id="plat_nomor" class="form-control" placeholder="B 1234 ABC" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ID Device (GPS Tracker)</label>
                            <input type="text" name="device_id" id="device_id" class="form-control" placeholder="DEV_AVANZA">
                        </div>
                        <div class="row mb-3">
                            <div class="col-8">
                                <label class="form-label">Merk Kendaraan</label>
                                <input type="text" name="merk" id="merk" class="form-control" placeholder="Toyota Avanza" required>
                            </div>
                            <div class="col-4">
                                <label class="form-label">Tahun</label>
                                <input type="number" name="tahun" id="tahun" class="form-control" placeholder="2024" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Foto Mobil</label>
                            <div class="border rounded-3 p-3 bg-light text-center" style="cursor: pointer;" id="uploadArea">
                                <i class="bi bi-camera fs-3 text-muted d-block"></i>
                                <span class="text-muted small">Klik untuk upload gambar</span>
                                <input type="file" name="foto_mobil" id="uploadMobil" class="form-control mt-2 d-none" accept="image/*">
                            </div>
                            <img id="preview" class="image-preview" alt="Preview Foto">
                            <small id="fotoInfo" class="text-muted d-block mt-1"></small>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label">Tarif (IDR/Hari)</label>
                                <input type="number" name="tarif_per_hari" id="tarif_per_hari" class="form-control" placeholder="150000" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">Denda (IDR/Hari)</label>
                                <input type="number" name="denda_per_hari" id="denda_per_hari" class="form-control" placeholder="50000" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Status</label>
                            <select name="status" id="status" class="form-select">
                                <option value="tersedia">Tersedia</option>
                                <option value="disewa">Disewa</option>
                                <option value="perbaikan">Perbaikan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" id="btnSubmit">
                            <i class="bi bi-save me-2"></i> SIMPAN KENDARAAN
                        </button>
                        <button type="button" class="btn btn-secondary w-100 py-2 fw-bold mt-2" id="btnCancel" style="display: none;">
                            <i class="bi bi-x-circle me-2"></i> BATAL EDIT
                        </button>
                    </form>
                    <p class="text-muted text-center mt-3" style="font-size: 0.65rem;">
                        Pastikan semua data yang dimasukkan telah sesuai dengan dokumen legal kendaraan.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Definisikan baseUrl untuk digunakan di script.js
    window.baseUrl = '<?= base_url() ?>';
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>

<?= $this->endSection() ?>