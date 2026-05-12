<?php
/**
 * @var array $mobil
 * @var object|null $selected_mobil
 * @var object $user
 */
?>
<?= $this->extend('layout/landing'); ?>

<?= $this->section('content'); ?>

<section class="py-5 bg-light">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-lg" style="border-radius: 30px; overflow: hidden;">
                    <div class="row g-0">
                        <!-- Left Side: Form -->
                        <div class="col-lg-7 p-4 p-md-5 bg-white">
                            <h3 class="fw-bold mb-4">Pengajuan Sewa Mobil</h3>
                            
                            <?php if (session()->getFlashdata('error')) : ?>
                                <div class="alert alert-danger border-0 small mb-4" style="border-radius: 12px;">
                                    <?= session()->getFlashdata('error') ?>
                                </div>
                            <?php endif; ?>

                            <form action="<?= base_url('/sewa/proses') ?>" method="POST" enctype="multipart/form-data" id="formSewa">
                                <?= csrf_field() ?>
                                
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">PILIH MOBIL</label>
                                    <select name="id_mobil" id="id_mobil" class="form-select border-0 bg-light p-3" style="border-radius: 12px;" required>
                                        <option value="" data-tarif="0" data-foto="<?= getCarImage() ?>">-- Pilih Mobil --</option>
                                        <?php foreach ($mobil as $m) : ?>
                                            <option value="<?= $m->id_mobil ?>" 
                                                data-tarif="<?= $m->tarif_per_hari ?>" 
                                                data-foto="<?= getCarImage((string)$m->foto_mobil, (string)$m->merk) ?>"
                                                <?= ($selected_mobil && $selected_mobil->id_mobil == $m->id_mobil) ? 'selected' : '' ?>>
                                                <?= esc((string)$m->merk) ?> - Rp <?= number_format((float)$m->tarif_per_hari, 0, ',', '.') ?>/hari
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <!-- User Profile Data (Request if missing) -->
                                <div class="bg-light p-4 rounded-4 mb-4">
                                    <h6 class="fw-bold mb-3 border-bottom pb-2">Informasi Identitas</h6>
                                    
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted">NIK (NOMOR INDUK KEPENDUDUKAN)</label>
                                        <input type="text" name="nik" class="form-control border-0 p-3" style="border-radius: 12px;" placeholder="16 Digit NIK" value="<?= $user->nik ?>" required>
                                    </div>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">NOMOR TELEPON</label>
                                            <input type="text" name="no_telp" class="form-control border-0 p-3" style="border-radius: 12px;" placeholder="Contoh: 0812..." value="<?= $user->no_telp ?>" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">NOMOR SIM</label>
                                            <input type="text" name="no_sim" class="form-control border-0 p-3" style="border-radius: 12px;" placeholder="Nomor SIM A" value="<?= $user->no_sim ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-0">
                                        <label class="form-label small fw-bold text-muted">ALAMAT LENGKAP</label>
                                        <textarea name="alamat" class="form-control border-0 p-3" rows="2" style="border-radius: 12px;" placeholder="Alamat lengkap sesuai KTP" required><?= $user->alamat ?></textarea>
                                    </div>
                                </div>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">TANGGAL SEWA</label>
                                        <input type="date" name="tgl_sewa" id="tgl_sewa" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" min="<?= date('Y-m-d') ?>" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted">TANGGAL KEMBALI</label>
                                        <input type="date" name="tgl_kembali_rencana" id="tgl_kembali" class="form-control border-0 bg-light p-3" style="border-radius: 12px;" required>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">CATATAN TAMBAHAN (OPSIONAL)</label>
                                    <textarea name="catatan_pelanggan" class="form-control border-0 bg-light p-3" rows="2" style="border-radius: 12px;" placeholder="Contoh: Titik penjemputan, permintaan khusus, dll."></textarea>
                                </div>

                                <div class="row g-3 mb-4 border-top pt-4">
                                    <div class="col-md-6 text-center">
                                        <label class="form-label small fw-bold text-muted d-block mb-3">UPLOAD FOTO KTP</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" name="dokumen_ktp" id="dokumen_ktp" class="form-control border-0 bg-light p-2" style="border-radius: 12px;" accept="image/*" required onchange="triggerBlurModal('dokumen_ktp', 'ktp')">
                                            <small class="text-muted mt-2 d-block">Wajib diunggah untuk verifikasi</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-center border-start">
                                        <label class="form-label small fw-bold text-muted d-block mb-3">UPLOAD FOTO SIM A</label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" name="dokumen_sim" id="dokumen_sim" class="form-control border-0 bg-light p-2" style="border-radius: 12px;" accept="image/*" required onchange="triggerBlurModal('dokumen_sim', 'sim')">
                                            <small class="text-muted mt-2 d-block">Wajib diunggah untuk verifikasi</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="p-4 bg-primary text-white mb-4 shadow-sm" style="border-radius: 20px; background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Durasi Sewa:</span>
                                        <span class="fw-bold"><span id="display-durasi">0</span> Hari</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3">
                                        <span>Total Biaya:</span>
                                        <h4 class="fw-bold mb-0">Rp <span id="display-total">0</span></h4>
                                    </div>
                                    <small class="opacity-75">* Total biaya belum termasuk denda jika terlambat mengembalikan.</small>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-3 fw-bold shadow" style="border-radius: 12px; background: #2563eb;">Ajukan Sewa Sekarang</button>
                            </form>
                        </div>

                        <!-- Right Side: Car Info -->
                        <div class="col-lg-5 bg-light p-4 p-md-5 d-flex flex-column align-items-center justify-content-center text-center">
                            <div id="car-preview-area">
                                <img id="car-img" src="<?= getCarImage() ?>" class="img-fluid mb-4 rounded-4 shadow-sm" style="max-height: 250px; width: 100%; object-fit: cover;">
                                <h4 id="car-name" class="fw-bold mb-2">Pilih Mobil</h4>
                                <p id="car-price" class="text-primary fw-bold fs-5 mb-4">Rp 0 / Hari</p>
                                
                                <div class="text-start bg-white p-3 rounded-4 shadow-sm">
                                    <h6 class="fw-bold small mb-3 border-bottom pb-2">Informasi Penting:</h6>
                                    <ul class="small text-muted ps-3 mb-0">
                                        <li class="mb-2">Pastikan dokumen KTP & SIM terbaca jelas.</li>
                                        <li class="mb-2">Admin akan memverifikasi pengajuan Anda maksimal dalam 1x24 jam.</li>
                                        <li>Pembayaran dilakukan setelah pengajuan disetujui.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?= $this->include('pelanggan/upload_dokumen') ?>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<script>
    const idMobil = document.getElementById('id_mobil');
    const tglSewa = document.getElementById('tgl_sewa');
    const tglKembali = document.getElementById('tgl_kembali');
    
    const displayDurasi = document.getElementById('display-durasi');
    const displayTotal = document.getElementById('display-total');
    
    const carImg = document.getElementById('car-img');
    const carName = document.getElementById('car-name');
    const carPrice = document.getElementById('car-price');

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function calculate() {
        const option = idMobil.options[idMobil.selectedIndex];
        const tarif = parseInt(option.getAttribute('data-tarif')) || 0;
        const foto = option.getAttribute('data-foto');
        const name = option.text.split(' - ')[0];

        // Update preview
        carImg.src = foto;
        if (option.value) {
            carName.innerText = name;
            carPrice.innerText = 'Rp ' + formatRupiah(tarif) + ' / Hari';
        } else {
            carName.innerText = 'Pilih Mobil';
            carPrice.innerText = 'Rp 0 / Hari';
        }

        const start = new Date(tglSewa.value);
        const end = new Date(tglKembali.value);

        if (tglSewa.value && tglKembali.value) {
            if (end < start) {
                alert('Tanggal kembali tidak boleh sebelum tanggal sewa');
                tglKembali.value = '';
                return;
            }

            const diffTime = Math.abs(end - start);
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) || 1;
            
            displayDurasi.innerText = diffDays;
            displayTotal.innerText = formatRupiah(diffDays * tarif);
        }
    }

    idMobil.addEventListener('change', calculate);
    tglSewa.addEventListener('change', calculate);
    tglKembali.addEventListener('change', calculate);

    // Initial calculation if editing or pre-selected
    if (idMobil.value) calculate();

    document.getElementById('formSewa').addEventListener('submit', function(e) {
        const start = new Date(tglSewa.value);
        const end = new Date(tglKembali.value);
        if (end < start) {
            alert('Tanggal kembali tidak valid');
            e.preventDefault();
        }
    });
</script>
<?= $this->endSection(); ?>
