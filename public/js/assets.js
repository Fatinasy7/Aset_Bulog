function safeJsonParse(raw) {
    try {
        return raw ? JSON.parse(raw) : null;
    } catch (e) {
        return null;
    }
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
        if (Array.isArray(data)) {
            saveLocalAssets(data);
            return data;
        }
        return getLocalAssets();
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
        return response.data?.data || response.data;
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

    const response = await window.api.post('/assets', assetData);
    return response.data?.data || response.data;
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

    const response = await window.api.put(`/assets/${id}`, assetData);
    return response.data?.data || response.data;
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

    const response = await window.api.post(`/assets/${id}/scan`, {
        latitude,
        longitude,
        scanned_at: new Date().toISOString()
    });
    return response.data?.data || response.data;
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
