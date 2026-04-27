<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

<div class="container-fluid p-4">
    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- Main Content - Daftar Admin -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-white border-0 pt-4 pb-0 px-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <span class="badge bg-primary mb-2">Control Center</span>
                            <h3 class="fw-bold mb-0">Admin & Pengaturan</h3>
                        </div>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                            <i class="bi bi-person-plus-fill me-2"></i> TAMBAH ADMIN
                        </button>
                    </div>
                </div>

                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold mb-0">Daftar Admin</h5>
                        <div class="position-relative">
                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                            <input type="text" id="searchAdmin" class="form-control ps-5" placeholder="Cari admin..." style="width: 250px;">
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Nama Admin</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="adminTableBody">
                                <?php if (!empty($admins)): ?>
                                    <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td class="text-muted small">#ADM-<?= sprintf('%03d', $admin->id_pegawai) ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-circle bg-<?= $admin->jabatan == 'Owner' ? 'primary' : ($admin->jabatan == 'Admin' ? 'info' : 'secondary') ?> text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 12px; font-weight: bold;">
                                                    <?= strtoupper(substr($admin->nama, 0, 1)) ?>
                                                </div>
                                                <span class="fw-semibold"><?= esc($admin->nama) ?></span>
                                            </div>
                                        </td>
                                        <td><?= esc($admin->username) ?></td>
                                        <td>
                                            <span class="badge <?= $admin->jabatan == 'Owner' ? 'bg-primary' : ($admin->jabatan == 'Admin' ? 'bg-info' : 'bg-secondary') ?>">
                                                <?= esc($admin->jabatan) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?= $admin->status == 'aktif' ? 'bg-success' : 'bg-secondary' ?>">
                                                <?= $admin->status == 'aktif' ? 'Aktif' : 'Nonaktif' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary btn-edit" data-id="<?= $admin->id_pegawai ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger btn-delete" data-id="<?= $admin->id_pegawai ?>" data-nama="<?= esc($admin->nama) ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">Belum ada data admin</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar - Pengaturan Sistem -->
<div class="col-lg-4">
    <div class="card shadow-sm border-0 rounded-3 border-start border-4 border-primary">
        <div class="card-body p-4">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-sliders2 text-primary fs-4"></i>
                <h5 class="fw-bold mb-0">Pengaturan Sistem</h5>
            </div>

            <form action="<?= base_url('admin/admin/updateSettings') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <!-- Nama Aplikasi -->
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Nama Aplikasi</label>
                    <input type="text" name="app_name" class="form-control bg-light" 
                           value="<?= session()->get('app_name') ?? 'Rental Mobil' ?>" required>
                </div>

                <!-- Logo Aplikasi -->
                <div class="mb-3">
                    <label class="form-label small text-muted text-uppercase fw-bold">Logo Aplikasi</label>
                    <div class="border-2 border-dashed border-gray-300 rounded-3 p-4 text-center bg-light">
                        <?php $logoPath = session()->get('logo_path') ?? '/assets/img/logo.png'; ?>
                        <?php if (file_exists(FCPATH . $logoPath)): ?>
                            <img src="<?= base_url($logoPath) ?>" style="max-width: 100%; max-height: 80px; margin-bottom: 10px;">
                        <?php endif; ?>
                        <input type="file" name="logo" class="form-control mt-2" accept="image/png,image/jpeg,image/jpg,image/svg+xml">
                        <p class="small text-muted mt-2 mb-0">PNG, JPG, SVG, Max 2MB</p>
                    </div>
                </div>

                <!-- Mode Gelap -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <p class="fw-semibold mb-0">Mode Gelap</p>
                        <small class="text-muted">Ganti tema sistem ke aplikasi</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="dark_mode" id="darkModeToggle" 
                               <?= (session()->get('dark_mode') ?? 'off') == 'on' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <!-- Notifikasi Email -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <p class="fw-semibold mb-0">Notifikasi Email</p>
                        <small class="text-muted">Terima laporan harian via email</small>
                    </div>
                    <label class="switch">
                        <input type="checkbox" name="email_notif" 
                               <?= (session()->get('email_notification') ?? 'on') == 'on' ? 'checked' : '' ?>>
                        <span class="slider"></span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">
                    <i class="bi bi-save me-2"></i> SIMPAN PERUBAHAN
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Tambah Admin -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus-fill me-2"></i> Tambah Admin</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= base_url('admin/admin/save') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan" class="form-select" required>
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                            <option value="Owner">Owner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Telepon</label>
                        <input type="text" name="no_telp" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Admin -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i> Edit Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formEdit" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" id="edit_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" id="edit_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <select name="jabatan" id="edit_jabatan" class="form-select" required>
                            <option value="Staff">Staff</option>
                            <option value="Admin">Admin</option>
                            <option value="Owner">Owner</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No Telepon</label>
                        <input type="text" name="no_telp" id="edit_no_telp" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-select" required>
                            <option value="aktif">Aktif</option>
                            <option value="nonaktif">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Update Admin</button>
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