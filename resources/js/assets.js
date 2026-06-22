import api from './api.js';

export async function fetchAssets(params = {}) {
  const response = await api.get('/assets', { params });
  return response.data;
}

export async function getAsset(id) {
  const response = await api.get(`/assets/${id}`);
  return response.data;
}

export async function createAsset(assetData) {
  const response = await api.post('/assets', assetData);
  return response.data;
}

export async function updateAsset(id, assetData) {
  const response = await api.put(`/assets/${id}`, assetData);
  return response.data;
}

export async function deleteAsset(id) {
  const response = await api.delete(`/assets/${id}`);
  return response.data;
}

export async function assignPic(id, picId) {
  const response = await api.post(`/assets/${id}/assign-pic`, { pic_id: picId });
  return response.data;
}

export async function scanAsset(id, latitude = null, longitude = null) {
  const response = await api.post(`/assets/${id}/scan`, {
    latitude,
    longitude,
    scanned_at: new Date().toISOString()
  });
  return response.data;
}

export default {
  fetchAssets,
  getAsset,
  createAsset,
  updateAsset,
  deleteAsset,
  assignPic,
  scanAsset
};
