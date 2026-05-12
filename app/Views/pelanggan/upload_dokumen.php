<!-- Auto Blur Document Modal -->
<div class="modal fade" id="blurModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 20px; border: none;">
            <div class="modal-header border-0 bg-light" style="border-radius: 20px 20px 0 0;">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="bi bi-shield-lock-fill me-2"></i>Keamanan Dokumen (Auto-Blur)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info small" style="border-radius: 10px;">
                    Sistem kami mendeteksi wajah dan nomor sensitif (seperti NIK / SIM) untuk disensor secara otomatis demi keamanan data privasi Anda.
                </div>
                
                <div class="text-center mb-4 bg-light p-3" style="border-radius: 15px; border: 2px dashed #dee2e6;">
                    <canvas id="docCanvas" style="max-width: 100%; max-height: 400px; border-radius: 10px;"></canvas>
                </div>
                
                <div class="row align-items-center mb-3">
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted mb-0">Intensitas Blur</label>
                    </div>
                    <div class="col-md-6">
                        <input type="range" class="form-range" id="blurSlider" min="5" max="35" step="2" value="15">
                    </div>
                    <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" id="btnResetBlur">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                </div>
                
                <input type="hidden" id="currentDocType" value="">
                <input type="hidden" id="currentInputId" value="">
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light fw-bold" style="border-radius: 10px;" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary fw-bold px-4" style="border-radius: 10px;" id="btnSimpanBlur">
                    <i class="bi bi-check2 me-1"></i> Gunakan Gambar Ini
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://docs.opencv.org/4.8.0/opencv.js" async onload="onOpenCvReady();"></script>
<script src="<?= base_url('assets/js/blur_document.js') ?>"></script>

<script>
    let blurModal;
    document.addEventListener('DOMContentLoaded', function() {
        blurModal = new bootstrap.Modal(document.getElementById('blurModal'));
        
        const blurSlider = document.getElementById('blurSlider');
        blurSlider.addEventListener('input', function() {
            applyBlur('docCanvas', parseInt(this.value));
        });
        
        document.getElementById('btnResetBlur').addEventListener('click', function() {
            resetBlur('docCanvas');
            blurSlider.value = 5; // minimum blur
        });
        
        document.getElementById('btnSimpanBlur').addEventListener('click', function() {
            const base64Data = getBlurredBase64('docCanvas');
            const targetInputId = document.getElementById('currentInputId').value;
            
            // Simpan base64 ke input hidden agar ikut di-submit ke form
            let hiddenInput = document.getElementById('hidden_' + targetInputId);
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'base64_' + targetInputId;
                hiddenInput.id = 'hidden_' + targetInputId;
                document.getElementById('formSewa').appendChild(hiddenInput);
            }
            hiddenInput.value = base64Data;
            
            // Ubah style input file asli untuk menandakan sudah diproses
            const fileInput = document.getElementById(targetInputId);
            fileInput.classList.add('is-valid');
            fileInput.nextElementSibling.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill"></i> Dokumen aman (telah disensor)</span>';
            
            blurModal.hide();
        });
    });

    function triggerBlurModal(inputId, docType) {
        document.getElementById('currentInputId').value = inputId;
        document.getElementById('currentDocType').value = docType;
        
        // Reset slider
        document.getElementById('blurSlider').value = 15;
        
        // Tampilkan modal dulu
        blurModal.show();
        
        // Render ke canvas
        setTimeout(() => {
            processDocument(inputId, 'docCanvas', 15, docType);
        }, 500); // beri waktu modal render
    }
</script>
