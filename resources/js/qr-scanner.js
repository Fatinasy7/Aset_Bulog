// Minimal wrapper for HTML5-QRCode scanner and geotagging
let html5QrCodeInstance = null;

export async function startScanner(containerId, onResult, onError, options = {}) {
  if (typeof Html5Qrcode === 'undefined') {
    onError && onError(new Error('Html5Qrcode library not loaded'));
    return;
  }

  if (html5QrCodeInstance) {
    try { await html5QrCodeInstance.stop(); } catch (e) {}
    html5QrCodeInstance = null;
  }

  html5QrCodeInstance = new Html5Qrcode(containerId);
  const config = {
    fps: options.fps || 10,
    qrbox: options.qrbox || { width: 250, height: 250 }
  };

  const successCallback = async (decodedText, decodedResult) => {
    // Try to obtain geolocation, then invoke onResult
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          const coords = { latitude: pos.coords.latitude, longitude: pos.coords.longitude };
          onResult && onResult(decodedText, coords, decodedResult);
        },
        (err) => {
          onResult && onResult(decodedText, null, decodedResult);
        },
        { enableHighAccuracy: true, maximumAge: 10000, timeout: 5000 }
      );
    } else {
      onResult && onResult(decodedText, null, decodedResult);
    }

    // Stop scanner to save battery
    try { await html5QrCodeInstance.stop(); } catch (e) {}
  };

  const errorCallback = (err) => {
    onError && onError(err);
  };

  try {
    await html5QrCodeInstance.start({ facingMode: 'environment' }, config, successCallback, errorCallback);
  } catch (e) {
    onError && onError(e);
  }
}

export async function stopScanner() {
  if (!html5QrCodeInstance) return;
  try {
    await html5QrCodeInstance.stop();
  } catch (e) {}
  html5QrCodeInstance = null;
}

// Expose to non-module pages
window.startQrScanner = startScanner;
window.stopQrScanner = stopScanner;
