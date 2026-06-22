let qrScannerInstance = null;

function isHtml5QrcodeAvailable() {
    return typeof Html5Qrcode !== 'undefined';
}

async function startQrScanner(containerId, onSuccess, onError, options = {}) {
    if (!isHtml5QrcodeAvailable()) {
        const err = new Error('HTML5-QRCode library belum dimuat');
        onError?.(err);
        return;
    }

    if (qrScannerInstance) {
        try {
            await qrScannerInstance.stop();
        } catch (e) {
            // ignore stop error
        }
    }

    qrScannerInstance = new Html5Qrcode(containerId);
    const config = {
        fps: options.fps || 10,
        qrbox: options.qrbox || { width: 250, height: 250 }
    };

    const successCallback = async (decodedText, decodedResult) => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const coords = {
                        latitude: position.coords.latitude,
                        longitude: position.coords.longitude
                    };
                    onSuccess?.(decodedText, coords, decodedResult);
                },
                () => {
                    onSuccess?.(decodedText, null, decodedResult);
                },
                { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
            );
        } else {
            onSuccess?.(decodedText, null, decodedResult);
        }

        try {
            await qrScannerInstance.stop();
        } catch (e) {
            // ignore
        }
    };

    const errorCallback = (errorMessage) => {
        onError?.(new Error(errorMessage));
    };

    try {
        await qrScannerInstance.start({ facingMode: 'environment' }, config, successCallback, errorCallback);
    } catch (err) {
        onError?.(err);
    }
}

async function stopQrScanner() {
    if (!qrScannerInstance) return;
    try {
        await qrScannerInstance.stop();
    } catch (e) {
        // ignore
    }
    qrScannerInstance = null;
}

window.qrScanner = {
    startQrScanner,
    stopQrScanner,
    isHtml5QrcodeAvailable
};
