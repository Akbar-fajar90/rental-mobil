<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php helper('asset'); ?>
<?php
    if (!function_exists('getConditionBadge')) {
    function getConditionBadge(string $condition)
    {
        switch ($condition) {
            case 'baik':
                return 'cond-flawless';
            case 'rusak-ringan':
                return 'cond-minor';
            case 'rusak-berat':
                return 'cond-major';
            default:
                return 'cond-flawless';
        }
    }
}

if (!function_exists('getConditionText')) {
    function getConditionText(string $condition)
    {
        switch ($condition) {
            case 'baik':
                return 'Perfect';
            case 'rusak-ringan':
                return 'Minor Damage';
            case 'rusak-berat':
                return 'Major Damage';
            default:
                return ucfirst(str_replace('-', ' ', $condition));
        }
    }
}
?>
<link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

<div class="container-fluid p-4">
    <div class="mb-4">
        <h4 class="fw-bold mb-1">Pengembalian Mobil</h4>
        <p class="text-muted small">Selesaikan proses serah terima mobil, cek kondisi kendaraan, dan hitung tagihan akhir.</p>
    </div>

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

    <!-- Pilih Sewa Aktif -->
    <div class="section-card mb-4">
        <h6 class="fw-bold mb-3">Pilih Penyewaan Aktif</h6>
        <div class="row g-3">
            <?php if (!empty($active_rentals)): ?>
                <?php foreach ($active_rentals as $rental): ?>
                <div class="col-md-4">
                    <div class="rental-card select-rental-btn p-3 rounded-3 bg-white shadow-sm" 
                         data-id="<?= $rental->id_sewa ?>"
                         data-nama="<?= esc((string)($rental->nama_pelanggan ?? '')) ?>"
                         data-merk="<?= esc((string)($rental->mobil_merk ?? '')) ?>"
                         data-plat="<?= esc((string)($rental->plat_nomor ?? '')) ?>"
                         data-tgl_sewa="<?= $rental->tgl_sewa ?>"
                         data-tgl_kembali="<?= $rental->tgl_kembali_rencana ?>"
                         data-tarif="<?= $rental->tarif_per_hari ?>"
                         data-sub_total="<?= $rental->sub_total ?>"
                         data-foto="<?= (string)$rental->foto_mobil ?>">
                        <div class="d-flex align-items-center gap-3">
                            <img src="<?= getCarImage((string)$rental->foto_mobil) ?>" 
                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 10px;">
                            <div>
                                <h6 class="fw-bold mb-0"><?= esc((string)$rental->mobil_merk) ?></h6>
                                <small class="text-muted"><?= esc((string)$rental->plat_nomor) ?></small>
                                <div class="mt-1">
                                    <small><i class="bi bi-person me-1"></i> <?= esc((string)$rental->nama_pelanggan) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info text-center mb-0">Tidak ada penyewaan aktif</div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <div class="col-lg-8">
            <div class="section-card" id="returnFormSection" style="display: none;">
                <form action="<?= base_url('admin/pengembalian/process') ?>" method="post" id="returnForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_sewa" id="rental_id">
                    
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <span class="badge-process mb-2 d-inline-block">ACTIVE RETRIEVAL</span>
                            <h5 class="fw-bold m-0" id="carTitle">-</h5>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block" style="font-size: 0.65rem; font-weight: 700;">RENTAL ID</small>
                            <span class="text-primary fw-bold" id="rentalIdDisplay">-</span>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-5">
                            <small class="text-muted d-block mb-2 font-weight-bold" style="font-size: 0.65rem; text-transform: uppercase;">Customer Details</small>
                            <div class="info-box d-flex align-items-center mb-3">
                                <div class="cust-profile me-3">
                                    <img src="" id="customerAvatar" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                </div>
                                <div>
                                    <div class="fw-bold small" id="customerName">-</div>
                                    <small class="text-muted" style="font-size: 0.65rem;" id="customerSince">Member since -</small>
                                </div>
                            </div>

                            <label class="text-muted d-block mb-2" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Actual Return Date</label>
                            <input type="date" name="tgl_kembali" id="tgl_kembali_actual" class="form-control bg-light border-0 py-2 mb-3" style="font-size: 0.8rem; border-radius: 8px;" value="<?= date('Y-m-d') ?>" required>

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <small class="text-muted d-block" style="font-size: 0.6rem;">SCHEDULED RETURN</small>
                                    <span class="fw-bold small" id="scheduledReturn">-</span>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted d-block" style="font-size: 0.6rem;">DURATION STATUS</small>
                                    <span class="text-danger fw-bold small" id="lateStatus"><i class="bi bi-exclamation-triangle-fill me-1"></i> -</span>
                                </div>
                            </div>

                            <label class="text-muted d-block mb-2" style="font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Vehicle Condition Assessment</label>
                            <select name="kondisi_mobil" id="kondisi_mobil_select" class="form-select bg-light border-0 py-2 mb-3" style="font-size: 0.8rem; border-radius: 8px;" required>
                                <option value="baik">Perfect / As Received</option>
                                <option value="rusak-ringan">Minor Scratch</option>
                                <option value="rusak-berat">Major Damage</option>
                            </select>
                            
                            <div id="damageInput" style="display: none;">
                                <label class="text-muted d-block mb-2" style="font-size: 0.65rem; font-weight: 700;">Damage Fee</label>
                                <div class="input-group mb-3">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" name="denda_kerusakan" id="denda_kerusakan_input" class="form-control" placeholder="0" value="0">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-7">
                            <div class="financial-card">
                                <h6 class="fw-bold mb-4">Financial Summary</h6>
                                <div class="price-row">
                                    <span class="text-muted">Base Rate (<span id="totalHari">0</span> hari)</span>
                                    <span class="fw-bold">Rp <span id="baseRate">0</span></span>
                                </div>
                                <div class="price-row" id="lateFeeRow" style="display: none;">
                                    <span class="text-muted">Late Return Fee (<span id="lateDays">0</span> hari)</span>
                                    <span class="text-danger fw-bold">+ Rp <span id="lateFee">0</span></span>
                                </div>
                                <div class="price-row" id="damageFeeRow" style="display: none;">
                                    <span class="text-muted">Damage Fee</span>
                                    <span class="text-danger fw-bold">+ Rp <span id="damageFee">0</span></span>
                                </div>
                                <hr class="my-4" style="border-style: dashed;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold d-block small">Total Fine</span>
                                    </div>
                                    <div class="total-fine">Rp <span id="totalFine">0</span></div>
                                </div>
                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mt-4" style="border-radius: 10px; font-size: 0.8rem;">COMPLETE PROCESS</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div id="noRentalSelected" class="section-card text-center py-5">
                <i class="bi bi-car-front fs-1 text-muted"></i>
                <h5 class="mt-3">Pilih Penyewaan</h5>
                <p class="text-muted small">Klik salah satu kartu penyewaan di atas untuk memulai proses pengembalian</p>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 rounded-4 overflow-hidden mb-4 shadow-sm" id="carPreview">
                <img src="https://via.placeholder.com/600x400/e9ecef/6c757d?text=Select+Rental" 
                     class="car-preview-img" id="previewCarImage" alt="Car Preview">
                <div class="card-body bg-dark text-white p-3 text-center">
                    <span class="small fw-bold" id="previewCarTitle">Pilih penyewaan terlebih dahulu</span>
                </div>
            </div>

            <div class="checklist-card shadow-sm">
                <h6 class="fw-bold mb-3" style="font-size: 0.75rem; text-transform: uppercase;">Inspection Checklist</h6>
                <div class="check-item">
                    <i class="bi bi-check-circle-fill check-icon"></i>
                    <span>Fuel tank level matched (Full)</span>
                </div>
                <div class="check-item">
                    <i class="bi bi-check-circle-fill check-icon"></i>
                    <span>Interior cleaned and sanitized</span>
                </div>
                <div class="check-item">
                    <i class="bi bi-check-circle-fill check-icon"></i>
                    <span>External body inspection</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Return History -->
<div class="d-flex gap-2">
    <div class="dropdown">
        <button class="btn btn-outline-primary btn-sm fw-bold px-3 dropdown-toggle" 
                style="border-radius: 8px;" 
                type="button" 
                data-bs-toggle="dropdown">
            <i class="bi bi-download me-2"></i> EXPORT
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="<?= base_url('admin/pengembalian/export/excel') ?>">
                <i class="bi bi-file-earmark-excel text-success me-2"></i> Export ke Excel
            </a></li>
            <li><a class="dropdown-item" href="<?= base_url('admin/pengembalian/export/word') ?>">
                <i class="bi bi-file-earmark-word text-primary me-2"></i> Export ke Word
            </a></li>
            <li><a class="dropdown-item" href="<?= base_url('admin/pengembalian/export/pdf') ?>">
                <i class="bi bi-file-earmark-pdf text-danger me-2"></i> Export ke PDF
            </a></li>
        </ul>
    </div>
</div>

        <div class="table-responsive">
            <table class="table table-history">
                <thead>
                    <tr>
                        <th>ID Rental</th>
                        <th>Customer</th>
                        <th>Vehicle Unit</th>
                        <th>Return Date</th>
                        <th>Condition</th>
                        <th class="text-end">Settlement</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($history)): ?>
                        <?php foreach ($history as $row): ?>
                        <tr>
                            <td class="text-primary fw-bold">#RTN-<?= sprintf('%05d', $row->id_pengembalian) ?></td>
                            <td>
                                <div class="fw-bold"><?= esc((string)($row->nama_pelanggan ?? '')) ?></div>
                                <small class="text-muted" style="font-size: 0.65rem;"><?= esc((string)($row->plat_nomor ?? '-')) ?></small>
                            </td>
                            <td><i class="bi bi-car-front me-2"></i> <?= esc((string)($row->mobil_merk ?? '')) ?></td>
                            <td class="text-muted"><?= date('d M Y', strtotime($row->tgl_kembali)) ?></td>
                            <td>
                                <span class="badge-condition <?= getConditionBadge((string)$row->kondisi_mobil) ?>">
                                    <?= getConditionText((string)$row->kondisi_mobil) ?>
                                </span>
                            </td>
                            <td class="text-end fw-bold">
                                Rp <?= number_format(($row->denda_terlambat ?? 0) + ($row->denda_kerusakan ?? 0), 0, ',', '.') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada riwayat pengembalian</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="text-center mt-3">
            <a href="#" class="text-primary text-decoration-none fw-bold small">VIEW ALL HISTORY</a>
        </div>
    </div>
</div>

<script>
    window.baseUrl = '<?= base_url() ?>';

    document.addEventListener('DOMContentLoaded', function() {
        const rentalCards = document.querySelectorAll('.select-rental-btn');
        const returnFormSection = document.getElementById('returnFormSection');
        const noRentalSelected = document.getElementById('noRentalSelected');
        
        // Input & Display Elements
        const tglKembaliActual = document.getElementById('tgl_kembali_actual');
        const kondisiSelect = document.getElementById('kondisi_mobil_select');
        const dendaKerusakanInput = document.getElementById('denda_kerusakan_input');
        const damageInputDiv = document.getElementById('damageInput');
        
        let currentRental = null;

        function formatRupiah(number) {
            return new Intl.NumberFormat('id-ID').format(number);
        }

        function calculateFines() {
            if (!currentRental) return;

            const actualDate = new Date(tglKembaliActual.value);
            const plannedDate = new Date(currentRental.tgl_kembali);
            const tarif = parseInt(currentRental.tarif);
            
            // Calculate Late Fee
            const diffTime = actualDate - plannedDate;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            const lateDays = diffDays > 0 ? diffDays : 0;
            const lateFee = lateDays * tarif;

            // Update Late UI
            document.getElementById('lateDays').innerText = lateDays;
            document.getElementById('lateFee').innerText = formatRupiah(lateFee);
            document.getElementById('lateFeeRow').style.display = lateDays > 0 ? 'flex' : 'none';
            document.getElementById('lateStatus').innerHTML = lateDays > 0 
                ? `<i class="bi bi-exclamation-triangle-fill me-1"></i> Terlambat ${lateDays} Hari`
                : `<i class="bi bi-check-circle-fill me-1 text-success"></i> Tepat Waktu`;
            document.getElementById('lateStatus').className = lateDays > 0 ? 'text-danger fw-bold small' : 'text-success fw-bold small';

            // Damage Fee
            const damageFee = parseInt(dendaKerusakanInput.value) || 0;
            document.getElementById('damageFee').innerText = formatRupiah(damageFee);
            document.getElementById('damageFeeRow').style.display = damageFee > 0 ? 'flex' : 'none';

            // Total
            const totalFine = lateFee + damageFee;
            document.getElementById('totalFine').innerText = formatRupiah(totalFine);
        }

        rentalCards.forEach(card => {
            card.addEventListener('click', function() {
                // Toggle Active Class
                rentalCards.forEach(c => c.classList.remove('active'));
                this.classList.add('active');

                // Store data
                currentRental = this.dataset;
                
                // Fill Form
                document.getElementById('rental_id').value = currentRental.id;
                document.getElementById('carTitle').innerText = currentRental.merk;
                document.getElementById('rentalIdDisplay').innerText = '#' + currentRental.id;
                document.getElementById('customerName').innerText = currentRental.nama;
                document.getElementById('scheduledReturn').innerText = new Date(currentRental.tgl_kembali).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
                document.getElementById('totalHari').innerText = Math.ceil((new Date(currentRental.tgl_kembali) - new Date(currentRental.tgl_sewa)) / (1000 * 60 * 60 * 24));
                document.getElementById('baseRate').innerText = formatRupiah(currentRental.sub_total);
                
                // Preview
                document.getElementById('previewCarImage').src = window.baseUrl + '/uploads/mobil/' + currentRental.foto;
                document.getElementById('previewCarTitle').innerText = currentRental.merk + ' (' + currentRental.plat + ')';

                // Show Section
                returnFormSection.style.display = 'block';
                noRentalSelected.style.display = 'none';

                calculateFines();
            });
        });

        tglKembaliActual.addEventListener('change', calculateFines);
        
        kondisiSelect.addEventListener('change', function() {
            if (this.value !== 'baik') {
                damageInputDiv.style.display = 'block';
            } else {
                damageInputDiv.style.display = 'none';
                dendaKerusakanInput.value = 0;
            }
            calculateFines();
        });

        dendaKerusakanInput.addEventListener('input', calculateFines);
    });
</script>
<script src="<?= base_url('assets/js/script.js') ?>"></script>

<?= $this->endSection() ?>