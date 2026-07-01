const startCameraButton = document.getElementById('start-camera');
const stopCameraButton = document.getElementById('stop-camera');
const videoElement = document.getElementById('qr-video');
const canvasElement = document.getElementById('qr-canvas');
const qrInput = document.getElementById('qr_input');
const qrStatus = document.getElementById('qr-status');
const qrPreview = document.getElementById('qr-preview');
const previewNama = document.getElementById('preview-nama');
const previewKode = document.getElementById('preview-kode');
const previewJenis = document.getElementById('preview-jenis');
const qrResult = document.getElementById('qr-result');
const resultKode = document.getElementById('result-kode');
const resultNama = document.getElementById('result-nama');
const resultKondisi = document.getElementById('result-kondisi');
const resultLokasi = document.getElementById('result-lokasi');
const resultPic = document.getElementById('result-pic');
const resultJenis = document.getElementById('result-jenis');
const resultCode = document.getElementById('qr-result-code');
const resultDetailLink = document.getElementById('result-detail-link');
const lookupForm = document.getElementById('qr-lookup-form');
const resetButton = document.getElementById('reset-qr');
const cameraPreview = document.getElementById('camera-preview');

let mediaStream = null;
let barcodeDetector = null;
let scanFrameId = null;

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
    const context = canvas.getContext('2d');
    canvas.width = videoElement.videoWidth;
    canvas.height = videoElement.videoHeight;
    context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

    try {
        const results = await barcodeDetector.detect(canvas);
        if (results.length > 0) {
            const qrText = results[0].rawValue.trim();
            if (qrText) {
                qrInput.value = qrText;
                await submitLookup(qrText);
                stopCamera();
                return;
            }
        }
    } catch (error) {
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
        } else {
            showStatus('Browser tidak mendukung deteksi QR otomatis. Gunakan input manual.', 'error');
            return;
        }

        scanFrameId = requestAnimationFrame(decodeFrame);
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

    try {
        const response = await fetch(lookupForm.action, {
            method: 'POST',
            body: payload,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
            },
        });

        if (!response.ok) {
            const error = await response.json();
            showStatus(error.message || 'Aset tidak ditemukan.', 'error');
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
}
