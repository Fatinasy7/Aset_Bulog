<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Aset - Asset Bulog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen Aset</h2>
        <div class="d-flex gap-2">
            <button id="logoutBtn" class="btn btn-outline-danger d-none">Logout</button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAsset">Tambah Aset</button>
        </div>
    </div>

    <div id="authPanel" class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Login ke sistem</h5>
            <p class="text-muted">Masukkan email dan password untuk mengakses data aset.</p>
            <form id="loginForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="loginEmail" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="loginPassword" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Masuk</button>
                    </div>
                </div>
            </form>
            <div id="authMessage" class="mt-3"></div>
        </div>
    </div>

    <div id="appContent" class="d-none">
        <div class="card shadow-sm">
            <div class="card-body">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Nama Aset</th>
                            <th>Jenis</th>
                            <th>Kondisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="table-asset-body">
                        <tr><td colspan="6" class="text-center text-muted">Memuat data aset...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalAsset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="assetForm">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="assetId">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Aset</label>
                            <input type="text" class="form-control" id="kodeAset" name="kode_aset" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Aset</label>
                            <input type="text" class="form-control" id="namaAset" name="nama_aset" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis</label>
                            <select class="form-select" id="jenis" name="jenis" required>
                                <option value="laptop">Laptop</option>
                                <option value="printer">Printer</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Merk/Type</label>
                            <input type="text" class="form-control" id="merkType" name="merk_type" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Serial Number</label>
                            <input type="text" class="form-control" id="serialNumber" name="serial_number">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lokasi</label>
                            <input type="text" class="form-control" id="lokasi" name="lokasi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Kondisi</label>
                            <select class="form-select" id="kondisi" name="kondisi" required>
                                <option value="Baik">Baik</option>
                                <option value="Rusak Ringan">Rusak Ringan</option>
                                <option value="Rusak Berat">Rusak Berat</option>
                                <option value="Dalam Perbaikan">Dalam Perbaikan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Perolehan</label>
                            <input type="date" class="form-control" id="tglPerolehan" name="tgl_perolehan">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Harga</label>
                            <input type="number" class="form-control" id="harga" name="harga" value="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
let assets = [];
let editingId = null;

function getAuthHeaders() {
    const token = localStorage.getItem('apiToken');
    return {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };
}

function renderAssets() {
    const tbody = document.getElementById('table-asset-body');
    if (!assets.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada data aset.</td></tr>';
        return;
    }

    tbody.innerHTML = assets.map((asset, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${asset.kodeAset || asset.kode_aset || '-'}</td>
            <td>${asset.namaAset || asset.nama_aset || '-'}</td>
            <td>${asset.jenis || '-'}</td>
            <td>${asset.kondisi || '-'}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editAsset(${asset.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deleteAsset(${asset.id})">Hapus</button>
            </td>
        </tr>
    `).join('');
}

function setAuthMessage(message, type = 'danger') {
    const container = document.getElementById('authMessage');
    container.className = `mt-3 alert alert-${type}`;
    container.textContent = message;
}

function showApp() {
    document.getElementById('authPanel').classList.add('d-none');
    document.getElementById('appContent').classList.remove('d-none');
    document.getElementById('logoutBtn').classList.remove('d-none');
}

function showLogin() {
    document.getElementById('authPanel').classList.remove('d-none');
    document.getElementById('appContent').classList.add('d-none');
    document.getElementById('logoutBtn').classList.add('d-none');
    document.getElementById('loginForm').reset();
}

function loadAssets() {
    if (!localStorage.getItem('apiToken')) {
        showLogin();
        return;
    }

    fetch('/api/assets', {
        method: 'GET',
        headers: getAuthHeaders()
    })
    .then(async response => {
        if (!response.ok) throw new Error('Gagal memuat aset');
        const data = await response.json();
        assets = Array.isArray(data) ? data : (data.data || []);
        renderAssets();
        showApp();
    })
    .catch(error => {
        console.error(error);
        document.getElementById('table-asset-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data aset.</td></tr>';
    });
}

function resetForm() {
    document.getElementById('assetForm').reset();
    document.getElementById('assetId').value = '';
    editingId = null;
}

function editAsset(id) {
    const asset = assets.find(item => item.id === id);
    if (!asset) return;

    editingId = id;
    document.getElementById('assetId').value = asset.id;
    document.getElementById('kodeAset').value = asset.kodeAset || asset.kode_aset || '';
    document.getElementById('namaAset').value = asset.namaAset || asset.nama_aset || '';
    document.getElementById('jenis').value = asset.jenis || 'laptop';
    document.getElementById('merkType').value = asset.merkType || asset.merk_type || '';
    document.getElementById('serialNumber').value = asset.serialNumber || asset.serial_number || '';
    document.getElementById('lokasi').value = asset.lokasi || '';
    document.getElementById('kondisi').value = asset.kondisi || 'Baik';
    document.getElementById('tglPerolehan').value = asset.tglPerolehan || asset.tgl_perolehan || '';
    document.getElementById('harga').value = asset.harga || 0;
    document.getElementById('keterangan').value = asset.keterangan || '';
    new bootstrap.Modal(document.getElementById('modalAsset')).show();
}

function deleteAsset(id) {
    if (!confirm('Yakin ingin menghapus aset ini?')) return;

    fetch(`/api/assets/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders()
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Gagal menghapus aset');
        loadAssets();
        alert(data.message || 'Aset berhasil dihapus');
    })
    .catch(error => {
        console.error(error);
        alert(error.message);
    });
}

function submitAsset(event) {
    event.preventDefault();

    const payload = {
        kode_aset: document.getElementById('kodeAset').value.trim(),
        nama_aset: document.getElementById('namaAset').value.trim(),
        merk_type: document.getElementById('merkType').value.trim(),
        serial_number: document.getElementById('serialNumber').value.trim(),
        lokasi: document.getElementById('lokasi').value.trim(),
        kondisi: document.getElementById('kondisi').value,
        tgl_perolehan: document.getElementById('tglPerolehan').value,
        harga: parseInt(document.getElementById('harga').value) || 0,
        keterangan: document.getElementById('keterangan').value.trim(),
        jenis: document.getElementById('jenis').value
    };

    const url = editingId ? `/api/assets/${editingId}` : '/api/assets';
    const method = editingId ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: getAuthHeaders(),
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Gagal menyimpan aset');
        bootstrap.Modal.getInstance(document.getElementById('modalAsset')).hide();
        resetForm();
        loadAssets();
        alert(editingId ? 'Aset berhasil diperbarui.' : 'Aset berhasil ditambahkan.');
    })
    .catch(error => {
        console.error(error);
        alert(error.message);
    });
}

function handleLogin(event) {
    event.preventDefault();

    const payload = {
        email: document.getElementById('loginEmail').value.trim(),
        password: document.getElementById('loginPassword').value
    };

    fetch('/api/auth/login', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) throw new Error(data.message || 'Login gagal');

        const token = data.token || data.auth_token;
        if (!token) throw new Error('Token tidak diterima dari server');

        localStorage.setItem('apiToken', token);
        localStorage.setItem('currentUser', JSON.stringify(data.user || {}));
        setAuthMessage('Login berhasil.', 'success');
        loadAssets();
    })
    .catch(error => {
        console.error(error);
        setAuthMessage(error.message, 'danger');
    });
}

function handleLogout() {
    fetch('/api/auth/logout', {
        method: 'POST',
        headers: getAuthHeaders()
    }).finally(() => {
        localStorage.removeItem('apiToken');
        localStorage.removeItem('currentUser');
        showLogin();
        setAuthMessage('Anda telah logout.', 'secondary');
    });
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('assetForm').addEventListener('submit', submitAsset);
    document.getElementById('loginForm').addEventListener('submit', handleLogin);
    document.getElementById('logoutBtn').addEventListener('click', handleLogout);
    if (localStorage.getItem('apiToken')) {
        loadAssets();
    } else {
        showLogin();
    }
});
</script>
</body>
</html>
