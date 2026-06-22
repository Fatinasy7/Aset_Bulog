function safeJsonParse(raw) {
    try {
        return raw ? JSON.parse(raw) : null;
    } catch (e) {
        return null;
    }
}

function normalizeAsset(asset) {
    if (!asset || typeof asset !== 'object') return asset;

    return {
        id: asset.id || asset._id || asset.kode_aset || asset.kodeAset,
        kodeAset: asset.kodeAset || asset.kode_aset || asset.kode_aset || asset.kodeAset || '',
        namaAset: asset.namaAset || asset.nama_aset || asset.namaAset || '',
        merkType: asset.merkType || asset.merk_type || asset.merkType || '',
        serialNumber: asset.serialNumber || asset.serial_number || '',
        lokasi: asset.lokasi || '',
        koordinat: asset.koordinat || ((asset.koordinat_lat !== undefined || asset.koordinat_lng !== undefined) ? {
            lat: asset.koordinat_lat || null,
            lng: asset.koordinat_lng || null
        } : null),
        kondisi: asset.kondisi || '',
        tglPerolehan: asset.tglPerolehan || asset.tgl_perolehan || '',
        harga: asset.harga ?? asset.price ?? 0,
        keterangan: asset.keterangan || '',
        jenis: asset.jenis || '',
        createdAt: asset.createdAt || asset.created_at || '',
        updatedAt: asset.updatedAt || asset.updated_at || ''
    };
}

function normalizeAssetArray(items) {
    if (!Array.isArray(items)) return [];
    return items.map(normalizeAsset);
}

function buildApiPayload(assetData) {
    const payload = {
        kode_aset: assetData.kodeAset,
        nama_aset: assetData.namaAset,
        merk_type: assetData.merkType,
        serial_number: assetData.serialNumber || null,
        lokasi: assetData.lokasi,
        kondisi: assetData.kondisi,
        tgl_perolehan: assetData.tglPerolehan || null,
        harga: assetData.harga || null,
        keterangan: assetData.keterangan || null,
        jenis: assetData.jenis
    };

    if (assetData.koordinat) {
        payload.koordinat_lat = assetData.koordinat.lat;
        payload.koordinat_lng = assetData.koordinat.lng;
    }

    return payload;
}

function getLocalAssets() {
    const stored = localStorage.getItem('asetKantor');
    return safeJsonParse(stored) || [];
}

function saveLocalAssets(list) {
    localStorage.setItem('asetKantor', JSON.stringify(list));
}

function hasApiAccess() {
    return !!(window.api && localStorage.getItem('auth_token'));
}

async function fetchAssets(params = {}) {
    if (!hasApiAccess()) {
        return getLocalAssets();
    }

    try {
        const response = await window.api.get('/assets', { params });
        const data = response.data?.data || response.data;
        const normalized = Array.isArray(data) ? normalizeAssetArray(data) : getLocalAssets();
        if (Array.isArray(normalized) && normalized.length) {
            saveLocalAssets(normalized);
        }
        return normalized;
    } catch (e) {
        return getLocalAssets();
    }
}

async function getAsset(id) {
    if (!hasApiAccess()) {
        return getLocalAssets().find(a => a.id === id || a.kodeAset === id) || null;
    }

    try {
        const response = await window.api.get(`/assets/${id}`);
        return normalizeAsset(response.data?.data || response.data);
    } catch (e) {
        return getLocalAssets().find(a => a.id === id || a.kodeAset === id) || null;
    }
}

async function createAsset(assetData) {
    if (!hasApiAccess()) {
        const assets = getLocalAssets();
        assets.push(assetData);
        saveLocalAssets(assets);
        return assetData;
    }

    const response = await window.api.post('/assets', buildApiPayload(assetData));
    return normalizeAsset(response.data?.data || response.data || assetData);
}

async function updateAsset(id, assetData) {
    if (!hasApiAccess()) {
        const assets = getLocalAssets();
        const index = assets.findIndex(a => a.id === id);
        if (index !== -1) {
            assets[index] = { ...assets[index], ...assetData };
            saveLocalAssets(assets);
            return assets[index];
        }
        return null;
    }

    const response = await window.api.put(`/assets/${id}`, buildApiPayload(assetData));
    return normalizeAsset(response.data?.data || response.data || assetData);
}

async function deleteAssetById(id) {
    if (!hasApiAccess()) {
        const assets = getLocalAssets().filter(a => a.id !== id);
        saveLocalAssets(assets);
        return { success: true };
    }

    const response = await window.api.delete(`/assets/${id}`);
    return response.data || { success: true };
}

async function scanAsset(id, latitude = null, longitude = null) {
    if (!hasApiAccess()) {
        const asset = await getAsset(id);
        return asset;
    }

    try {
        const response = await window.api.post(`/assets/${id}/scan`, {
            latitude,
            longitude,
            scanned_at: new Date().toISOString()
        });
        return normalizeAsset(response.data?.data || response.data);
    } catch (error) {
        // Fallback jika endpoint scan belum tersedia di backend
        return getAsset(id);
    }
}

window.assetsAPI = {
    fetchAssets,
    getAsset,
    createAsset,
    updateAsset,
    deleteAsset: deleteAssetById,
    scanAsset,
    hasApiAccess
};
