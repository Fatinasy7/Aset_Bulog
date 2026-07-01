<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management PIC - Asset Bulog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Manajemen PIC</h2>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPic" onclick="resetForm()">Tambah PIC</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama PIC</th>
                        <th>Jabatan</th>
                        <th>Email</th>
                        <th>Telepon</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="table-pic-body">
                    <tr>
                        <td colspan="6" class="text-center text-muted">Memuat data PIC...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalPic" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="picForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalPicTitle">Tambah PIC</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="picId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" class="form-control" id="picNama" name="nama" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jabatan</label>
                        <input type="text" class="form-control" id="picJabatan" name="jabatan" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="picEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telepon</label>
                        <input type="text" class="form-control" id="picTelepon" name="telepon">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" class="form-control" id="picPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
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
let pics = [];
let editingId = null;

function getAuthHeaders() {
    const token = localStorage.getItem('apiToken');
    return {
        'Authorization': 'Bearer ' + token,
        'Accept': 'application/json',
        'Content-Type': 'application/json'
    };
}

function resetForm() {
    document.getElementById('picForm').reset();
    editingId = null;
    document.getElementById('modalPicTitle').textContent = 'Tambah PIC';
    document.getElementById('picId').value = '';
}

function renderPics() {
    const tbody = document.getElementById('table-pic-body');
    if (!pics.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Belum ada data PIC.</td></tr>';
        return;
    }

    tbody.innerHTML = pics.map((pic, index) => `
        <tr>
            <td>${index + 1}</td>
            <td>${pic.nama || '-'}</td>
            <td>${pic.jabatan || '-'}</td>
            <td>${pic.email || '-'}</td>
            <td>${pic.telepon || '-'}</td>
            <td>
                <button class="btn btn-sm btn-warning" onclick="editPic(${pic.id})">Edit</button>
                <button class="btn btn-sm btn-danger" onclick="deletePic(${pic.id})">Hapus</button>
            </td>
        </tr>
    `).join('');
}

function loadPics() {
    fetch('/api/pics', {
        method: 'GET',
        headers: getAuthHeaders()
    })
   .then(async response => {
    // KUNCI PERBAIKAN: Cek status response terlebih dahulu
    if (!response.ok) {
        throw new Error(`Server Error (Status: ${response.status})`);
    }
    const data = await response.json();
    pics = Array.isArray(data) ? data : (data.data || []);
    renderPics();
})
    .catch(error => {
        console.error('Error fetching data:', error);
        document.getElementById('table-pic-body').innerHTML = '<tr><td colspan="6" class="text-center text-danger">Gagal memuat data dari server.</td></tr>';
    });
}

function editPic(id) {
    const pic = pics.find(item => item.id === id);
    if (!pic) return;

    editingId = id;
    document.getElementById('picId').value = pic.id;
    document.getElementById('picNama').value = pic.nama || '';
    document.getElementById('picJabatan').value = pic.jabatan || '';
    document.getElementById('picEmail').value = pic.email || '';
    document.getElementById('picTelepon').value = pic.telepon || '';
    document.getElementById('picPassword').value = '';
    document.getElementById('modalPicTitle').textContent = 'Edit PIC';
    new bootstrap.Modal(document.getElementById('modalPic')).show();
}

function deletePic(id) {
    if (!confirm('Yakin ingin menghapus PIC ini?')) return;

    fetch(`/api/pics/${id}`, {
        method: 'DELETE',
        headers: getAuthHeaders()
    })
    .then(async response => {
        const data = await response.json();
        if (!response.ok) throw new Error(data.message || 'Gagal menghapus PIC');
        loadPics();
        alert(data.message || 'PIC berhasil dihapus');
    })
    .catch(error => {
        console.error('Error deleting PIC:', error);
        alert(error.message);
    });
}


function submitPic(event) {
    event.preventDefault();

    const payload = {
        nama: document.getElementById('picNama').value.trim(),
        jabatan: document.getElementById('picJabatan').value.trim(),
        email: document.getElementById('picEmail').value.trim(),
        telepon: document.getElementById('picTelepon').value.trim(),
        password: document.getElementById('picPassword').value.trim()
    };

    if (!payload.nama || !payload.jabatan || !payload.email) {
        alert('Nama, jabatan, dan email wajib diisi.');
        return;
    }

    const url = editingId ? `/api/pics/${editingId}` : '/api/pics';
    const method = editingId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: getAuthHeaders(),
        body: JSON.stringify(payload)
    })
    .then(async response => {
        const data = await response.json().catch(() => ({}));
        if (!response.ok) {
            throw new Error(data.message || data.errors ? Object.values(data.errors).flat().join(' ') : 'Gagal menyimpan PIC');
        }

        bootstrap.Modal.getInstance(document.getElementById('modalPic')).hide();
        resetForm();
        loadPics();
        alert(editingId ? 'PIC berhasil diperbarui.' : 'PIC berhasil ditambahkan.');
    })
    .catch(error => {
        console.error('Error saat submit:', error);
        alert(error.message);
    });
}
   
 

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('picForm').addEventListener('submit', submitPic);
    loadPics();
});
</script>
</body>
</html>
