// File: public/assets/js/blur_document.js

let currentImageMat = null;
let blurredImageMat = null;
let currentRegions = [];

/**
 * Inisialisasi OpenCV.js
 */
function onOpenCvReady() {
    console.log('OpenCV.js is ready');
    // Load cascades or other initializations if needed
}

/**
 * Load image to canvas and process
 */
function processDocument(fileInputId, canvasId, blurIntensity, type = 'ktp') {
    const fileInput = document.getElementById(fileInputId);
    if (!fileInput.files || !fileInput.files[0]) return;

    const img = new Image();
    const reader = new FileReader();
    
    reader.onload = function(e) {
        img.src = e.target.result;
    };
    
    img.onload = function() {
        const canvas = document.getElementById(canvasId);
        const ctx = canvas.getContext('2d');
        
        // Resize for performance if needed, but we keep original for quality
        // Scale to max width 800
        const maxWidth = 800;
        let width = img.width;
        let height = img.height;
        
        if (width > maxWidth) {
            height = Math.round((height * maxWidth) / width);
            width = maxWidth;
        }

        canvas.width = width;
        canvas.height = height;
        ctx.drawImage(img, 0, 0, width, height);

        // Process with OpenCV
        if (typeof cv !== 'undefined') {
            try {
                let src = cv.imread(canvas);
                currentImageMat = src.clone();
                
                // Detect faces
                let faces = detectFaces(src, type);
                
                // Detect text regions (simulated logic for specific IDs like NIK/SIM)
                let texts = detectTextRegions(src, type);
                
                currentRegions = [...faces, ...texts];
                
                applyBlur(canvasId, blurIntensity);
                
                src.delete();
            } catch (err) {
                console.error("OpenCV Processing Error: ", err);
            }
        }
    };
    
    reader.readAsDataURL(fileInput.files[0]);
}

function detectFaces(src, type) {
    let regions = [];
    let w = src.cols;
    let h = src.rows;
    if (type === 'ktp') {
        // Face is generally on the right side
        regions.push({ x: Math.round(w * 0.65), y: Math.round(h * 0.15), width: Math.round(w * 0.28), height: Math.round(h * 0.5) });
    } else {
        // For SIM, face is generally on the left side
        regions.push({ x: Math.round(w * 0.05), y: Math.round(h * 0.35), width: Math.round(w * 0.3), height: Math.round(h * 0.55) });
    }
    return regions;
}

function detectTextRegions(src, type) {
    let regions = [];
    let w = src.cols;
    let h = src.rows;
    
    if (type === 'ktp') {
        // NIK Region
        regions.push({ x: Math.round(w * 0.2), y: Math.round(h * 0.18), width: Math.round(w * 0.45), height: Math.round(h * 0.12) });
        // Personal Details Region (Name, TTL, Gender, Address, Religion, Status, Job)
        regions.push({ x: Math.round(w * 0.25), y: Math.round(h * 0.35), width: Math.round(w * 0.45), height: Math.round(h * 0.5) });
    } else {
        // SIM Number Region (Top Center)
        regions.push({ x: Math.round(w * 0.3), y: Math.round(h * 0.12), width: Math.round(w * 0.5), height: Math.round(h * 0.15) });
        // Personal Details Region (Name, TTL, Address)
        regions.push({ x: Math.round(w * 0.35), y: Math.round(h * 0.3), width: Math.round(w * 0.5), height: Math.round(h * 0.5) });
    }
    return regions;
}

function applyBlur(canvasId, intensity) {
    if (!currentImageMat || currentRegions.length === 0) return;
    
    let dst = currentImageMat.clone();
    
    // Pastikan ksize ganjil
    let ksize = intensity * 2 + 1;
    if (ksize < 3) ksize = 3;

    for (let r of currentRegions) {
        // Pengecekan batas
        let x = Math.max(0, r.x);
        let y = Math.max(0, r.y);
        let w = Math.min(dst.cols - x, r.width);
        let h = Math.min(dst.rows - y, r.height);
        
        if (w <= 0 || h <= 0) continue;

        let roi = dst.roi(new cv.Rect(x, y, w, h));
        let blurred = new cv.Mat();
        
        cv.GaussianBlur(roi, blurred, new cv.Size(ksize, ksize), 0, 0, cv.BORDER_DEFAULT);
        blurred.copyTo(roi);
        
        roi.delete();
        blurred.delete();
    }
    
    cv.imshow(canvasId, dst);
    blurredImageMat = dst.clone();
    dst.delete();
}

function resetBlur(canvasId) {
    if (currentImageMat) {
        cv.imshow(canvasId, currentImageMat);
        if (blurredImageMat) blurredImageMat.delete();
        blurredImageMat = null;
    }
}

function getBlurredBase64(canvasId) {
    const canvas = document.getElementById(canvasId);
    return canvas.toDataURL('image/jpeg', 0.8);
}
