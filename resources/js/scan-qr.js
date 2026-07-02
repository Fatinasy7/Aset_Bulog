import jsQR from 'jsqr';

// Initialize DOM elements - declare with let so they can be assigned later
let startCameraButton;
let stopCameraButton;
let videoElement;
let canvasElement;
let qrInput;
let qrStatus;
let qrPreview;
let previewNama;
let previewKode;
let previewJenis;
let qrResult;
let resultKode;
let resultNama;
let resultKondisi;
let resultLokasi;
let resultPic;
let resultJenis;
let resultCode;
let resultDetailLink;
let lookupForm;
let resetButton;
let cameraPreview;

// Function to initialize DOM elements
const initializeDOMElements = () => {
    startCameraButton = document.getElementById('start-camera');
    stopCameraButton = document.getElementById('stop-camera');
    videoElement = document.getElementById('qr-video');
    canvasElement = document.getElementById('qr-canvas');
    qrInput = document.getElementById('qr_input');
    qrStatus = document.getElementById('qr-status');
    qrPreview = document.getElementById('qr-preview');
    previewNama = document.getElementById('preview-nama');
    previewKode = document.getElementById('preview-kode');
    previewJenis = document.getElementById('preview-jenis');
    qrResult = document.getElementById('qr-result');
    resultKode = document.getElementById('result-kode');
    resultNama = document.getElementById('result-nama');
    resultKondisi = document.getElementById('result-kondisi');
    resultLokasi = document.getElementById('result-lokasi');
    resultPic = document.getElementById('result-pic');
    resultJenis = document.getElementById('result-jenis');
    resultCode = document.getElementById('qr-result-code');
    resultDetailLink = document.getElementById('result-detail-link');
    lookupForm = document.getElementById('qr-lookup-form');
    resetButton = document.getElementById('reset-qr');
    cameraPreview = document.getElementById('camera-preview');
};

let mediaStream = null;
let barcodeDetector = null;
let scanFrameId = null;
let useJsQR = false;

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

const showStatus = (message, variant = 'note') => {
    qrStatus.textContent = message;
    qrStatus.classList.remove('text-success', 'text-danger');
    if (variant === 'success') {
        qrStatus.classList.add('text-success');
    } else if (variant === 'error') {
        qrStatus.classList.add('text-danger');
    }
};

// jsQR is bundled via npm and imported above; no dynamic loader needed.

const showResult = (asset, query) => {
    resultKode.textContent = asset.kode_aset;
    resultNama.textContent = asset.nama_aset;
    resultKondisi.textContent = asset.kondisi;
    resultLokasi.textContent = asset.lokasi;
    resultPic.textContent = asset.pic ?? '-';
    resultJenis.textContent = asset.jenis;
    resultCode.textContent = query;
    resultDetailLink.href = asset.detail_url;
    qrResult.classList.remove('visually-hidden');
    showPreview(asset);
};

const showPreview = (asset) => {
    previewNama.textContent = asset.nama_aset;
    previewKode.textContent = asset.kode_aset;
    previewJenis.textContent = asset.jenis;
    qrPreview.classList.remove('visually-hidden');
};

const hidePreview = () => {
    qrPreview.classList.add('visually-hidden');
};

const resetScan = () => {
    qrInput.value = '';
    showStatus('Hasil dibersihkan. Masukkan kode aset baru atau mulai kamera lagi.', 'note');
    hideResult();
    hidePreview();
};

const hideResult = () => {
    qrResult.classList.add('visually-hidden');
    hidePreview();
};

const stopCamera = () => {
    if (scanFrameId) {
        cancelAnimationFrame(scanFrameId);
        scanFrameId = null;
    }

    if (mediaStream) {
        mediaStream.getTracks().forEach((track) => track.stop());
        mediaStream = null;
    }

    videoElement.srcObject = null;
    cameraPreview.classList.add('visually-hidden');
    stopCameraButton.disabled = true;
    startCameraButton.disabled = false;
    showStatus('Kamera dihentikan. Masukkan kode aset secara manual jika perlu.', 'note');
};

const decodeFrame = async () => {
    if (!videoElement || videoElement.readyState !== HTMLMediaElement.HAVE_ENOUGH_DATA) {
        scanFrameId = requestAnimationFrame(decodeFrame);
        return;
    }

    const canvas = canvasElement;
    const context = canvas.getContext('2d', { willReadFrequently: true });
    canvas.width = videoElement.videoWidth;
    canvas.height = videoElement.videoHeight;
    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

    try {
        console.debug('decodeFrame start', { hasVideo: !!videoElement, readyState: videoElement?.readyState, barcodeDetector: !!barcodeDetector, useJsQR });
        if (barcodeDetector) {
            const results = await barcodeDetector.detect(canvas);
            console.debug('BarcodeDetector results', results);
            if (results.length > 0) {
                const raw = results[0].rawValue.trim();
                console.debug('Detected raw (BarcodeDetector):', raw);
                if (raw) {
                    let lookupQuery = raw;

                    try {
                        const parsed = JSON.parse(raw);
                        if (parsed) {
                            if (parsed.kode_aset) lookupQuery = parsed.kode_aset;
                            else if (parsed.id) lookupQuery = String(parsed.id);
                        }
                    } catch (e) {}

                    qrInput.value = lookupQuery;
                    console.debug('Lookup query (BarcodeDetector):', lookupQuery);
                    await submitLookup(lookupQuery);
                    stopCamera();
                    return;
                }
            }
        } else if (useJsQR && typeof jsQR === 'function') {
            const imageData = context.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, canvas.width, canvas.height);
            console.debug('jsQR result', code);
            if (code && code.data) {
                const raw = code.data.trim();
                console.debug('Detected raw (jsQR):', raw);
                if (raw) {
                    let lookupQuery = raw;
                    try {
                        const parsed = JSON.parse(raw);
                        if (parsed) {
                            if (parsed.kode_aset) lookupQuery = parsed.kode_aset;
                            else if (parsed.id) lookupQuery = String(parsed.id);
                        }
                    } catch (e) {}

                    qrInput.value = lookupQuery;
                    console.debug('Lookup query (jsQR):', lookupQuery);
                    await submitLookup(lookupQuery);
                    stopCamera();
                    return;
                }
            }
        }
    } catch (error) {
        console.error('decodeFrame error', error);
        showStatus('Gagal mendeteksi QR secara otomatis. Silakan gunakan input manual.', 'error');
        stopCamera();
        return;
    }

    scanFrameId = requestAnimationFrame(decodeFrame);
};

const startCamera = async () => {
    if (!navigator.mediaDevices?.getUserMedia) {
        showStatus('Perangkat ini tidak mendukung kamera. Gunakan input manual.', 'error');
        return;
    }

    try {
        mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
        videoElement.srcObject = mediaStream;
        cameraPreview.classList.remove('visually-hidden');
        videoElement.play();
        startCameraButton.disabled = true;
        stopCameraButton.disabled = false;
        showStatus('Memindai... Arahkan kamera ke QR code aset.', 'success');

        if ('BarcodeDetector' in window) {
            barcodeDetector = new window.BarcodeDetector({ formats: ['qr_code'] });
            scanFrameId = requestAnimationFrame(decodeFrame);
        } else {
            // Use bundled jsQR as fallback
            useJsQR = true;
            showStatus('Memindai menggunakan fallback JS. Arahkan kamera ke QR code aset.', 'success');
            scanFrameId = requestAnimationFrame(decodeFrame);
        }
    } catch (error) {
        showStatus('Tidak dapat mengakses kamera. Periksa izin browser.', 'error');
        startCameraButton.disabled = false;
        stopCameraButton.disabled = true;
    }
};

const submitLookup = async (query) => {
    if (!query) {
        showStatus('Masukkan kode aset atau hasil scan QR terlebih dahulu.', 'error');
        return;
    }

    const payload = new FormData();
    payload.append('qr_text', query);
    payload.append('_token', getCsrfToken());
    console.debug('submitLookup payload', query, lookupForm.action);

    try {
        const response = await fetch(lookupForm.action, {
            method: 'POST',
            body: payload,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        console.debug('lookup response status', response.status);
        if (!response.ok) {
            let error = null;
            try { error = await response.json(); } catch (e) { console.error('Failed parse error json', e); }
            console.debug('lookup error body', error);
            showStatus((error && error.message) || 'Aset tidak ditemukan.', 'error');
            hideResult();
            return;
        }

        const data = await response.json();
        if (data.found) {
            showResult(data.asset, query);
            showStatus(`Aset ditemukan: ${data.asset.nama_aset} (${data.asset.kode_aset})`, 'success');
        } else {
            showStatus('Aset tidak ditemukan.', 'error');
            hideResult();
        }
    } catch (error) {
        showStatus('Gagal mencari aset. Periksa koneksi dan coba lagi.', 'error');
        hideResult();
    }
};

// Initialize and attach event listeners when DOM is ready
const attachEventListeners = () => {
    initializeDOMElements();
    
    if (startCameraButton && stopCameraButton && lookupForm) {
        startCameraButton.addEventListener('click', startCamera);
        stopCameraButton.addEventListener('click', stopCamera);
        if (resetButton) {
            resetButton.addEventListener('click', resetScan);
        }

        lookupForm.addEventListener('submit', async (event) => {
            event.preventDefault();
            const query = qrInput.value.trim();
            hideResult();
            await submitLookup(query);
        });
    } else {
        console.warn('Scan QR: Not all required DOM elements found', {
            startCameraButton: !!startCameraButton,
            stopCameraButton: !!stopCameraButton,
            lookupForm: !!lookupForm
        });
    }
};

// Attach listeners when DOM is fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', attachEventListeners);
} else {
    // DOM is already loaded
    attachEventListeners();
}
