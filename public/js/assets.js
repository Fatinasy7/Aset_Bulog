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
        // Handle 404 specifically
        if (error.response?.status === 404) {
            throw new Error(`Aset dengan ID "${id}" tidak ditemukan dalam database backend.`);
        }
        // Handle validation errors (422)
        if (error.response?.status === 422) {
            const messages = error.response.data?.errors || error.response.data?.message || 'Data tidak valid';
            throw new Error(`Validasi gagal: ${typeof messages === 'string' ? messages : JSON.stringify(messages)}`);
        }
        // Fallback jika endpoint scan belum tersedia atau error lainnya
        console.warn('Endpoint scan tidak tersedia, mencoba fallback ke getAsset', error);
        return getAsset(id);
    }
}

async function getDashboardSummary() {
    if (!hasApiAccess()) {
        // Fallback: compute from local assets
        const laptops = JSON.parse(localStorage.getItem('assets') || '[]').filter(a => a.jenis === 'laptop');
        const printers = JSON.parse(localStorage.getItem('assets') || '[]').filter(a => a.jenis === 'printer');
        const allAssets = JSON.parse(localStorage.getItem('assets') || '[]');
        const needsRepair = allAssets.filter(a => ['Rusak Ringan', 'Rusak Berat', 'Dalam Perbaikan'].includes(a.kondisi));
        
        const kondisiCounts = {};
        allAssets.forEach(a => {
            kondisiCounts[a.kondisi] = (kondisiCounts[a.kondisi] || 0) + 1;
        });
        
        const lokasiCounts = {};
        allAssets.forEach(a => {
            lokasiCounts[a.lokasi] = (lokasiCounts[a.lokasi] || 0) + 1;
        });
        
        return {
            total_assets: allAssets.length,
            total_laptops: laptops.length,
            total_printers: printers.length,
            needs_repair: needsRepair.length,
            kondisi_breakdown: kondisiCounts,
            lokasi_breakdown: lokasiCounts
        };
    }

    try {
        const response = await window.api.get('/dashboard/summary');
        const data = response.data?.data || response.data;
        
        // Ensure proper structure
        return {
            total_assets: data.total_assets || 0,
            total_laptops: data.total_laptops || 0,
            total_printers: data.total_printers || 0,
            needs_repair: data.needs_repair || 0,
            kondisi_breakdown: data.kondisi_breakdown || {},
            lokasi_breakdown: data.lokasi_breakdown || {}
        };
    } catch (error) {
        console.warn('getDashboardSummary API gagal', error);
        // Fallback to localStorage
        return getDashboardSummary(); // recursive fallback
    }
}

window.assetsAPI = {
    fetchAssets,
    getAsset,
    createAsset,
    updateAsset,
    deleteAsset: deleteAssetById,
    scanAsset,
    getDashboardSummary,
    hasApiAccess
};
