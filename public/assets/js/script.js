document.addEventListener('DOMContentLoaded', function() {

    // ==========================================
    // GLOBAL FUNCTIONS
    // ==========================================
    window.formatRupiah = function(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }
    
    window.togglePassword = function(fieldId, button) {
        const field = document.getElementById(fieldId);
        const icon = button.querySelector('i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        }
    }

    // ==========================================
    // DASHBOARD
    // ==========================================
    if (document.getElementById('map')) {
        var map = L.map('map').setView([-6.2, 106.8], 11);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var armada = window.armadaData || [];
        var markers = [];
        var bounds = [];

        armada.forEach(function(item) {
            if(item.latitude && item.longitude && item.latitude != 0 && item.longitude != 0) {
                var lat = parseFloat(item.latitude);
                var lng = parseFloat(item.longitude);
                var marker = L.marker([lat, lng]).addTo(map).bindPopup(`
                        <b>🚗 ${item.merk}</b><br>
                        Plat: ${item.plat_nomor}<br>
                        📍 ${lat}, ${lng}<br>
                        <small>🕐 Update: ${item.last_update || '-'}</small>
                    `);
                markers.push(marker);
                bounds.push([lat, lng]);
            }
        });

        if(markers.length > 0) {
            var group = L.featureGroup(markers);
            map.fitBounds(group.getBounds());
        } else {
            map.setView([-6.2088, 106.8456], 12);
            L.popup().setLatLng([-6.2088, 106.8456]).setContent('⚠️ Belum ada data GPS').openOn(map);
        }
    }

    // ==========================================
    // MOBIL
    // ==========================================
    const uploadArea = document.getElementById('uploadArea');
    if (uploadArea) {
        uploadArea.addEventListener('click', function() {
            document.getElementById('uploadMobil').click();
        });

        document.getElementById('uploadMobil').addEventListener('change', function(e) {
            var preview = document.getElementById('preview');
            var fotoInfo = document.getElementById('fotoInfo');
            if (e.target.files && e.target.files[0]) {
                var reader = new FileReader();
                reader.onload = function(event) {
                    preview.src = event.target.result;
                    preview.classList.add('show');
                    if (fotoInfo) {
                        fotoInfo.innerHTML = '<i class="bi bi-upload me-1"></i> Foto baru akan menggantikan foto lama';
                        fotoInfo.style.color = '#ff9800';
                    }
                }
                reader.readAsDataURL(e.target.files[0]);
            }
        });
        
        document.getElementById('btnCancel')?.addEventListener('click', function() {
            if(window.resetToAddMode) window.resetToAddMode();
        });
    }

    // Generic Delete Confirmation (used across multiple pages)
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            let id = this.getAttribute('data-id');
            let nama = this.getAttribute('data-nama');
            let deleteUrl = this.getAttribute('data-url');
            
            // For older implementation relying on base_url in view
            if(!deleteUrl) {
                // If it's mobil:
                if(window.location.href.includes('mobil')) {
                    deleteUrl = window.baseUrl ? window.baseUrl + '/admin/mobil/hapus/' + id : '';
                } else if(window.location.href.includes('admin') && !window.location.href.includes('pelanggan')) {
                    deleteUrl = window.baseUrl ? window.baseUrl + '/admin/admin/delete/' + id : '';
                }
            }
            
            if (deleteUrl && confirm(`Yakin hapus ${nama}?`)) {
                window.location.href = deleteUrl;
            }
        });
    });

    // ==========================================
    // PENYEWAAN
    // ==========================================
    document.querySelectorAll('.btn-check-doc').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const modalBody = document.getElementById('modalDokumenBody');
            if(!modalBody || !window.baseUrl) return;
            
            modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data dokumen...</p></div>';
            
            fetch(window.baseUrl + '/admin/penyewaan/validate-documents/' + id)
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        let html = `<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i> ${data.message}</div><div class="list-group">`;
                        data.documents.forEach(doc => {
                            let icon = doc.status == 'valid' ? '<i class="bi bi-check-circle-fill text-success"></i>' : '<i class="bi bi-exclamation-triangle-fill text-warning"></i>';
                            html += `<div class="list-group-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>${icon} <strong>${doc.name}</strong></div>
                                            <div>${doc.value}</div>
                                        </div>`;
                            if (doc.image_url) html += `<div class="mt-2"><img src="${doc.image_url}" class="preview-image" onerror="this.style.display='none'"></div>`;
                            html += `</div>`;
                        });
                        html += `</div>`;
                        modalBody.innerHTML = html;
                    } else {
                        let html = `<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> ${data.message}</div><p class="fw-bold">Dokumen yang belum lengkap:</p><ul>`;
                        data.missing.forEach(m => { html += `<li><i class="bi bi-x-circle-fill text-danger me-2"></i> ${m}</li>`; });
                        html += `</ul>`;
                        modalBody.innerHTML = html;
                    }
                }).catch(error => { modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat data dokumen</div>'; });
        });
    });

    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('approveNama').innerText = this.getAttribute('data-nama');
            document.getElementById('approveMerk').innerText = this.getAttribute('data-merk');
            if(window.baseUrl) document.getElementById('formApprove').action = window.baseUrl + '/admin/penyewaan/approve/' + id;
            new bootstrap.Modal(document.getElementById('modalApprove')).show();
        });
    });

    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('rejectNama').innerText = this.getAttribute('data-nama');
            document.getElementById('rejectMerk').innerText = this.getAttribute('data-merk');
            if(window.baseUrl) document.getElementById('formReject').action = window.baseUrl + '/admin/penyewaan/reject/' + id;
            new bootstrap.Modal(document.getElementById('modalReject')).show();
        });
    });

    document.getElementById('searchHistory')?.addEventListener('keyup', function() {
        const search = this.value.toLowerCase();
        document.querySelectorAll('#historyTable tbody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(search) ? '' : 'none';
        });
    });

    // ==========================================
    // PENGEMBALIAN
    // ==========================================
    document.querySelectorAll('.select-rental-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.rental-card').forEach(card => card.classList.remove('active', 'border-primary'));
            this.classList.add('active', 'border-primary');
            if(typeof window.updateReturnForm === 'function') window.updateReturnForm({
                id: this.dataset.id, nama: this.dataset.nama, merk: this.dataset.merk, plat: this.dataset.plat,
                tgl_sewa: this.dataset.tgl_sewa, tgl_kembali: this.dataset.tgl_kembali,
                tarif: parseInt(this.dataset.tarif), sub_total: parseInt(this.dataset.sub_total), foto: this.dataset.foto
            });
            document.getElementById('returnFormSection').style.display = 'block';
            document.getElementById('noRentalSelected').style.display = 'none';
        });
    });

    document.querySelector('select[name="kondisi_mobil"]')?.addEventListener('change', function() {
        const damageInputDiv = document.getElementById('damageInput');
        const damageFeeRow = document.getElementById('damageFeeRow');
        const damageFeeSpan = document.getElementById('damageFee');
        if (this.value === 'rusak-ringan' || this.value === 'rusak-berat') {
            damageInputDiv.style.display = 'block'; damageFeeRow.style.display = 'flex';
        } else {
            damageInputDiv.style.display = 'none'; damageFeeRow.style.display = 'none';
            document.querySelector('input[name="denda_kerusakan"]').value = '0';
            damageFeeSpan.innerText = '0';
            if(typeof window.updateTotal === 'function') window.updateTotal();
        }
    });

    document.querySelector('input[name="denda_kerusakan"]')?.addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        if (value) {
            this.value = parseInt(value).toLocaleString('id-ID');
            document.getElementById('damageFee').innerText = this.value.replace(/\./g, '');
        } else {
            this.value = '0'; document.getElementById('damageFee').innerText = '0';
        }
        if(typeof window.updateTotal === 'function') window.updateTotal();
    });

    // ==========================================
    // PEMBAYARAN
    // ==========================================
    const jumlahBayar = document.getElementById('jumlahBayar');
    if (jumlahBayar) {
        window.hitungTotal = function() {
            let jumlah = parseInt(jumlahBayar.value.replace(/[^0-9]/g, '')) || 0;
            let admin = 5000;
            let pajak = Math.floor(jumlah * 0.11);
            let total = jumlah + admin + pajak;
            document.getElementById('taxAmount').innerText = window.formatRupiah(pajak);
            document.getElementById('totalAkhir').innerText = window.formatRupiah(total);
            jumlahBayar.value = window.formatRupiah(jumlah);
        }
        jumlahBayar.addEventListener('input', window.hitungTotal);
        
        document.getElementById('metodeBayar').addEventListener('change', function() {
            document.getElementById('bankGroup').style.display = this.value === 'transfer' ? 'block' : 'none';
            document.getElementById('ewalletGroup').style.display = this.value === 'ewallet' ? 'block' : 'none';
        });
        
        window.hitungTotal();
        document.getElementById('metodeBayar').dispatchEvent(new Event('change'));
    }

    // ==========================================
    // LAPORAN
    // ==========================================
    document.getElementById('btnApplyFilter')?.addEventListener('click', function() {
        let params = new URLSearchParams();
        let startDate = document.getElementById('startDate').value;
        let endDate = document.getElementById('endDate').value;
        let status = document.getElementById('statusFilter').value;
        let search = document.getElementById('searchInput').value;
        
        if (startDate) params.set('start_date', startDate);
        if (endDate) params.set('end_date', endDate);
        if (status) params.set('status', status);
        if (search) params.set('search', search);
        
        if(window.baseUrl) window.location.href = window.baseUrl + '/admin/laporan?' + params.toString();
    });

    document.getElementById('btnResetFilter')?.addEventListener('click', function() {
        if(window.baseUrl) window.location.href = window.baseUrl + '/admin/laporan';
    });
    
    document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') document.getElementById('btnApplyFilter').click();
    });
    
    document.querySelectorAll('.pg-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            let page = this.getAttribute('data-page');
            if (page && window.baseUrl) {
                let params = new URLSearchParams(window.location.search);
                params.set('page', page);
                window.location.href = window.baseUrl + '/admin/laporan?' + params.toString();
            }
        });
    });

    // ==========================================
    // ADMIN / SETTINGS
    // ==========================================
    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.checked = document.body.classList.contains('dark-mode');
        darkModeToggle.addEventListener('change', function() {
            if (typeof setDarkMode === 'function') {
                setDarkMode(this.checked);
            } else {
                if (this.checked) {
                    document.body.classList.add('dark-mode');
                    localStorage.setItem('dark_mode', 'on');
                } else {
                    document.body.classList.remove('dark-mode');
                    localStorage.setItem('dark_mode', 'off');
                }
            }
            if (typeof updateDarkModeIcon === 'function') updateDarkModeIcon();
        });
    }

    document.getElementById('searchAdmin')?.addEventListener('keyup', function() {
        let search = this.value.toLowerCase();
        document.querySelectorAll('#adminTableBody tr').forEach(row => {
            row.style.display = row.innerText.toLowerCase().includes(search) ? '' : 'none';
        });
    });

    // ==========================================
    // FORGOT PASSWORD
    // ==========================================
    const newPassword = document.getElementById('new_password');
    const confirmPassword = document.getElementById('confirm_password');
    if (newPassword && confirmPassword) {
        const errorDiv = document.getElementById('passwordMatchError');
        const checkPasswordMatch = function() {
            if (newPassword.value !== confirmPassword.value && confirmPassword.value !== '') {
                errorDiv.classList.remove('hidden');
                confirmPassword.classList.add('border-red-500', 'bg-red-50');
                confirmPassword.classList.remove('border-gray-200', 'bg-gray-50');
            } else {
                errorDiv.classList.add('hidden');
                confirmPassword.classList.remove('border-red-500', 'bg-red-50');
                confirmPassword.classList.add('border-gray-200', 'bg-gray-50');
            }
        }
        newPassword.addEventListener('keyup', checkPasswordMatch);
        confirmPassword.addEventListener('keyup', checkPasswordMatch);
    }

    // ==========================================
    // LANDING PAGE
    // ==========================================
    const header = document.querySelector('.landing-header');
    const backToTop = document.querySelector('.back-to-top');
    const mobileMenu = document.getElementById('mobileMenu');
    const mobileMenuToggle = document.getElementById('mobileMenuToggle');
    const mobileMenuOverlay = document.getElementById('mobileMenuOverlay');

    // Sticky Header & Back to Top
    window.addEventListener('scroll', function() {
        if (header) {
            header.classList.toggle('sticky', window.scrollY > 50);
        }
        if (backToTop) {
            backToTop.classList.toggle('show', window.scrollY > 300);
        }
    });

    // Mobile Menu Toggle
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.add('active');
            if (mobileMenuOverlay) mobileMenuOverlay.classList.add('active');
        });
    }

    if (mobileMenuOverlay) {
        mobileMenuOverlay.addEventListener('click', function() {
            mobileMenu.classList.remove('active');
            mobileMenuOverlay.classList.remove('active');
        });
    }

    // Back to Top functionality
    if (backToTop) {
        backToTop.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});
