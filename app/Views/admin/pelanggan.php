<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<style>
    :root {
        --bs-primary: #1d63ed;
        --bg-light: #f8f9fa;
    }
    body { background-color: var(--bg-light); font-family: 'Inter', sans-serif; }
    
    /* Header & Table Styles */
    .table-container { background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.02); }
    .search-box { background: #f1f3f5; border: none; border-radius: 8px; padding: 8px 15px; font-size: 0.85rem; width: 250px; }
    
    .table thead th { border-bottom: 1px solid #f1f3f5; color: #333; font-size: 0.75rem; text-transform: uppercase; font-weight: 700; padding-bottom: 15px; }
    .table tbody td { vertical-align: middle; padding: 1.2rem 0.75rem; border-color: #f8f9fa; font-size: 0.85rem; }
    
    /* Profile Image & Member Tags */
    .profile-img { width: 35px; height: 35px; border-radius: 50%; object-fit: cover; margin-right: 12px; }
    .member-type { display: block; font-size: 0.7rem; color: #adb5bd; margin-top: 2px; }
    
    /* Form Registration Styles */
    .form-card { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
    .form-label { font-size: 0.7rem; font-weight: 700; color: #333; text-transform: uppercase; margin-bottom: 0.4rem; }
    .form-control { border: none; border-bottom: 1.5px solid #eee; border-radius: 0; padding: 0.5rem 0; font-size: 0.85rem; background: transparent; transition: 0.3s; }
    .form-control:focus { box-shadow: none; border-color: var(--bs-primary); background: transparent; }
    .form-control-plaintext {
        font-size: 0.85rem;
        padding: 0.5rem 0;
    }
    
    /* Pagination & Footer */
    .pagination-info { font-size: 0.7rem; color: #adb5bd; text-transform: uppercase; font-weight: 600; }
    .nav-pagination { font-size: 0.75rem; font-weight: 600; color: #333; text-decoration: none; }
    
    .status-indicator { font-size: 0.65rem; color: #6c757d; text-transform: uppercase; font-weight: 700; }

    .btn-action {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
    }
    .table tbody td {
        vertical-align: middle;
    }
    
    /* Alert styles */
    .alert-custom {
        border-radius: 8px;
        padding: 12px 16px;
        margin-bottom: 20px;
    }
    
    /* Modal styles */
    .modal-content {
        border-radius: 12px;
    }
    .modal-header {
        border-bottom: 1px solid #f1f3f5;
        padding: 16px 24px;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        border-top: 1px solid #f1f3f5;
        padding: 16px 24px;
    }
    
    /* Edit mode indicator */
    .edit-mode-badge {
        background: #0d6efd;
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        margin-left: 10px;
    }
</style>

<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center">
            <h3 class="fw-bold m-0">Manajemen Pelanggan</h3>
            <span id="modeBadge" class="edit-mode-badge" style="display: none;">MODE EDIT</span>
        </div>
        <button id="btnCancelEdit" class="btn btn-secondary btn-sm px-3 py-2 fw-bold" style="border-radius: 8px; display: none;">
            <i class="bi bi-x-lg me-2"></i> BATAL EDIT
        </button>
        <button id="btnTambah" class="btn btn-primary btn-sm px-3 py-2 fw-bold" style="border-radius: 8px;">
            <i class="bi bi-person-plus-fill me-2"></i> TAMBAH PELANGGAN
        </button>
    </div>

    <!-- Alert Messages -->
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($errors) && !empty($errors)): ?>
        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> 
            <?php foreach ($errors as $error): ?>
                <?= $error ?><br>
            <?php endforeach; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="table-container">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold m-0">Daftar Pelanggan</h6>
                    <form method="get" action="<?= base_url('admin/pelanggan') ?>" class="position-relative">
                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted" style="font-size: 0.8rem;"></i>
                        <input type="text" name="keyword" class="search-box ps-5" placeholder="Cari pelanggan..." value="<?= esc($keyword ?? '') ?>">
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nama Pelanggan</th>
                                <th>Kontak & NIK</th>
                                <th>Email</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
                            <?php if (!empty($pelanggan)): ?>
                                <?php foreach ($pelanggan as $row): ?>
                                <tr data-id="<?= $row->id_pelanggan ?>">
                                    <td class="text-primary fw-bold">#<?= sprintf('PEL%03d', $row->id_pelanggan) ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <?php if ($row->foto_identitas && file_exists('uploads/identitas/' . $row->foto_identitas)): ?>
                                                <img src="<?= base_url('uploads/identitas/' . $row->foto_identitas) ?>" class="profile-img">
                                            <?php else: ?>
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row->nama) ?>&background=0d6efd&color=fff" class="profile-img">
                                            <?php endif; ?>
                                            <div>
                                                <div class="fw-bold"><?= esc($row->nama) ?></div>
                                                <span class="member-type"><?= $row->no_sim ? 'Memiliki SIM' : 'Belum ada SIM' ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= esc($row->no_telp) ?></div>
                                        <span class="text-muted small"><?= esc(substr($row->nik, 0, 10)) . '...' ?></span>
                                    </td>
                                    <td class="text-muted"><?= esc($row->email) ?: '-' ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary btn-action btn-edit" data-id="<?= $row->id_pelanggan ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger btn-action btn-delete" data-id="<?= $row->id_pelanggan ?>" data-nama="<?= esc($row->nama) ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        Belum ada data pelanggan
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <span class="pagination-info">
                        Menampilkan <?= count($pelanggan) ?> dari <?= $pager->getTotal() ?> Pelanggan
                    </span>
                    <div>
                        <?= $pager->links() ?>
                    </div>
                </div>
            </div>
            
            <div class="mt-5 status-indicator">
                <span class="text-success">●</span> System Operational
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card form-card">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-1" id="formTitle">Form Registrasi</h6>
                    <p class="text-muted mb-4" id="formSubtitle" style="font-size: 0.75rem;">Input data pelanggan baru ke database.</p>
                    
                    <form id="pelangganForm" action="<?= base_url('admin/pelanggan/save') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        <input type="hidden" name="id_pelanggan" id="id_pelanggan" value="">
                        
                        <div class="mb-4">
                            <label class="form-label">Nama Lengkap *</label>
                            <input type="text" name="nama" id="nama" class="form-control" value="<?= old('nama') ?>" placeholder="Ex: John Doe" required>
                        </div>
                        <div class="row mb-4">
                            <div class="col-6">
                                <label class="form-label">No Telepon *</label>
                                <input type="text" name="no_telp" id="no_telp" class="form-control" value="<?= old('no_telp') ?>" placeholder="08..." required>
                            </div>
                            <div class="col-6">
                                <label class="form-label">No KTP *</label>
                                <input type="text" name="nik" id="nik" class="form-control" value="<?= old('nik') ?>" placeholder="16 digit" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" value="<?= old('email') ?>" placeholder="john@fleet.com">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Alamat Lengkap *</label>
                            <input type="text" name="alamat" id="alamat" class="form-control" value="<?= old('alamat') ?>" placeholder="Enter residential address..." required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label">No SIM</label>
                            <input type="text" name="no_sim" id="no_sim" class="form-control" value="<?= old('no_sim') ?>" placeholder="Nomor SIM (opsional)">
                        </div>
                        <div class="mb-5">
                            <label class="form-label">Foto KTP</label>
                            <input type="file" name="foto_identitas" id="foto_identitas" class="form-control" accept="image/*">
                            <small class="text-muted" id="fotoInfo">Upload foto KTP (opsional)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 py-2 fw-bold mb-3" style="border-radius: 8px;" id="btnSubmit">
                            <i class="bi bi-save-fill me-2"></i> SIMPAN PELANGGAN
                        </button>
                        <button type="reset" class="btn btn-link w-100 text-decoration-none text-muted fw-bold small" id="btnReset">
                            RESET FORM
                        </button>
                    </form>

                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="d-flex">
                                <div style="width: 25px; height: 25px; background: #cbd5e0; border-radius: 50%; border: 2px solid white;"></div>
                                <div style="width: 25px; height: 25px; background: #a0aec0; border-radius: 50%; border: 2px solid white; margin-left: -10px;"></div>
                                <div style="width: 25px; height: 25px; background: #feb2b2; border-radius: 50%; border: 2px solid white; margin-left: -10px;"></div>
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem; font-weight: 600;">
                                <?= $pager->getTotal() ?> Active Customers
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data pelanggan <strong id="namaPelanggan"></strong>?</p>
                <p class="text-danger small">Data yang dihapus tidak dapat dikembalikan!</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnHapusConfirm" class="btn btn-danger">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

<script>
    // Variable untuk menyimpan status mode
    let isEditMode = false;
    let currentEditId = null;
    
    // Form elements
    const form = document.getElementById('pelangganForm');
    const formTitle = document.getElementById('formTitle');
    const formSubtitle = document.getElementById('formSubtitle');
    const btnSubmit = document.getElementById('btnSubmit');
    const btnReset = document.getElementById('btnReset');
    const btnTambah = document.getElementById('btnTambah');
    const btnCancelEdit = document.getElementById('btnCancelEdit');
    const modeBadge = document.getElementById('modeBadge');
    const fotoInfo = document.getElementById('fotoInfo');
    
    // Input fields
    const idPelangganInput = document.getElementById('id_pelanggan');
    const namaInput = document.getElementById('nama');
    const noTelpInput = document.getElementById('no_telp');
    const nikInput = document.getElementById('nik');
    const emailInput = document.getElementById('email');
    const alamatInput = document.getElementById('alamat');
    const noSimInput = document.getElementById('no_sim');
    const fotoInput = document.getElementById('foto_identitas');
    
    // Fungsi untuk reset form ke mode tambah
    function resetToAddMode() {
        isEditMode = false;
        currentEditId = null;
        
        // Reset form action
        form.action = '<?= base_url('admin/pelanggan/save') ?>';
        
        // Reset title dan subtitle
        formTitle.innerHTML = 'Form Registrasi';
        formSubtitle.innerHTML = 'Input data pelanggan baru ke database.';
        btnSubmit.innerHTML = '<i class="bi bi-save-fill me-2"></i> SIMPAN PELANGGAN';
        fotoInfo.innerHTML = 'Upload foto KTP (opsional)';
        
        // Reset input values
        idPelangganInput.value = '';
        namaInput.value = '';
        noTelpInput.value = '';
        nikInput.value = '';
        emailInput.value = '';
        alamatInput.value = '';
        noSimInput.value = '';
        fotoInput.value = '';
        
        // Reset required attributes
        namaInput.required = true;
        noTelpInput.required = true;
        nikInput.required = true;
        alamatInput.required = true;
        
        // Sembunyikan tombol cancel dan badge
        btnCancelEdit.style.display = 'none';
        modeBadge.style.display = 'none';
        
        // Hapus highlight di tabel
        document.querySelectorAll('#tableBody tr').forEach(row => {
            row.style.backgroundColor = '';
        });
    }
    
    // Fungsi untuk mengisi form dengan data edit
    function loadEditData(id) {
        // Tampilkan loading
        btnSubmit.innerHTML = '<i class="bi bi-hourglass-split me-2"></i> Memuat...';
        btnSubmit.disabled = true;
        
        // Fetch data dari server
        fetch('<?= base_url('admin/pelanggan/editForm/') ?>' + id)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert('Error: ' + data.error);
                    resetToAddMode();
                    return;
                }
                
                // Set mode edit
                isEditMode = true;
                currentEditId = id;
                
                // Ubah form action ke update
                form.action = '<?= base_url('admin/pelanggan/update/') ?>' + id;
                
                // Ubah title dan subtitle
                formTitle.innerHTML = 'Form Edit Pelanggan';
                formSubtitle.innerHTML = 'Edit data pelanggan yang sudah ada.';
                btnSubmit.innerHTML = '<i class="bi bi-pencil-square me-2"></i> UPDATE PELANGGAN';
                fotoInfo.innerHTML = 'Upload foto baru (kosongkan jika tidak ingin mengubah)';
                
                // Isi form dengan data
                idPelangganInput.value = data.id_pelanggan;
                namaInput.value = data.nama;
                noTelpInput.value = data.no_telp;
                nikInput.value = data.nik;
                emailInput.value = data.email || '';
                alamatInput.value = data.alamat;
                noSimInput.value = data.no_sim || '';
                
                // Tampilkan tombol cancel dan badge
                btnCancelEdit.style.display = 'block';
                modeBadge.style.display = 'inline-block';
                
                // Highlight row yang sedang diedit
                document.querySelectorAll('#tableBody tr').forEach(row => {
                    row.style.backgroundColor = '';
                });
                const currentRow = document.querySelector(`#tableBody tr[data-id="${id}"]`);
                if (currentRow) {
                    currentRow.style.backgroundColor = '#e8f4fd';
                }
                
                // Scroll ke form
                document.querySelector('.form-card').scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memuat data pelanggan');
                resetToAddMode();
            })
            .finally(() => {
                btnSubmit.innerHTML = '<i class="bi bi-save-fill me-2"></i> SIMPAN PELANGGAN';
                btnSubmit.disabled = false;
            });
    }
    
    // Event listener untuk tombol edit
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            loadEditData(id);
        });
    });
    
    // Event listener untuk tombol tambah
    btnTambah.addEventListener('click', function() {
        resetToAddMode();
    });
    
    // Event listener untuk tombol cancel edit
    btnCancelEdit.addEventListener('click', function() {
        resetToAddMode();
    });
    
    // Event listener untuk tombol reset
    btnReset.addEventListener('click', function(e) {
        e.preventDefault();
        if (isEditMode) {
            if (confirm('Reset form akan membatalkan edit. Lanjutkan?')) {
                resetToAddMode();
            }
        } else {
            form.reset();
            fotoInput.value = '';
        }
    });
    
    // Konfirmasi sebelum submit edit
    form.addEventListener('submit', function(e) {
        if (isEditMode) {
            if (!confirm('Apakah Anda yakin ingin mengupdate data ini?')) {
                e.preventDefault();
            }
        }
    });
    
    // Konfirmasi hapus dengan modal
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const nama = this.getAttribute('data-nama');
            const url = '<?= base_url('admin/pelanggan/delete/') ?>' + id;
            
            document.getElementById('namaPelanggan').innerText = nama;
            document.getElementById('btnHapusConfirm').setAttribute('href', url);
            new bootstrap.Modal(document.getElementById('modalHapus')).show();
        });
    });
    
    // Set data-id pada row untuk highlight
    document.querySelectorAll('#tableBody tr').forEach(row => {
        const editBtn = row.querySelector('.btn-edit');
        if (editBtn) {
            row.setAttribute('data-id', editBtn.getAttribute('data-id'));
        }
    });
</script>

<?= $this->endSection() ?>