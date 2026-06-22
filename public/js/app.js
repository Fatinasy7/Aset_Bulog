/**
 * Sistem Manajemen Aset Perkantoran
 * Main Application JavaScript
 */

// Global Variables
let assets = [];
let users = [];
let notifications = [];
let currentUser = null;
let currentPage = 'dashboard';
let editingId = null;
let qrCodeInstance = null;
let kondisiChart = null;
let lokasiChart = null;
let locationWatcher = null;
let currentCoordinates = null;

// Pagination & Filter State
let currentSearchTerm = '';
let currentFilterKondisi = '';
let currentFilterJenis = '';
let currentFilterLokasi = '';
let currentPageNum = 1;
let itemsPerPage = 10;
let totalItems = 0;
let searchDebounceTimer = null;

// Debounce Search
function debounceSearch(term) {
    clearTimeout(searchDebounceTimer);
    searchDebounceTimer = setTimeout(async () => {
        currentSearchTerm = term;
        currentPageNum = 1;
        if (currentPage === 'laporan') {
            await loadAndRenderAssets();
        }
    }, 300);
}

// Build Query Params for API
function buildAssetQueryParams() {
    const params = {};
    if (currentSearchTerm) params.search = currentSearchTerm;
    if (currentFilterKondisi) params.kondisi = currentFilterKondisi;
    if (currentFilterJenis) params.jenis = currentFilterJenis;
    if (currentFilterLokasi) params.lokasi = currentFilterLokasi;
    params.page = currentPageNum;
    params.per_page = itemsPerPage;
    return params;
}

// Load and Render Assets with Pagination
async function loadAndRenderAssets() {
    try {
        const params = buildAssetQueryParams();
        if (window.assetsAPI?.fetchAssets) {
            const response = await window.assetsAPI.fetchAssets(params);
            // Expect API to return { data: [...], pagination: { total, page, per_page } }
            if (response?.data) {
                assets = Array.isArray(response.data) ? response.data : [];
                totalItems = response.pagination?.total || assets.length;
            } else if (Array.isArray(response)) {
                assets = response;
            }
        }
    } catch (e) {
        console.warn('API fetchAssets gagal, fallback localStorage', e);
    }
    renderLaporan();
    renderPagination();
}

// Render Pagination Controls
function renderPagination() {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const paginationContainer = document.getElementById('paginationControls');
    
    if (!paginationContainer || totalPages <= 1) {
        if (paginationContainer) paginationContainer.innerHTML = '';
        return;
    }
    
    let html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    // Previous button
    if (currentPageNum > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${currentPageNum - 1})">Sebelumnya</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">Sebelumnya</span></li>`;
    }
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPageNum) {
            html += `<li class="page-item active"><span class="page-link">${i}</span></li>`;
        } else {
            html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${i})">${i}</a></li>`;
        }
    }
    
    // Next button
    if (currentPageNum < totalPages) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${currentPageNum + 1})">Berikutnya</a></li>`;
    } else {
        html += `<li class="page-item disabled"><span class="page-link">Berikutnya</span></li>`;
    }
    
    html += '</ul></nav>';
    paginationContainer.innerHTML = html;
}

// Go to Page
async function goToPage(pageNum) {
    currentPageNum = pageNum;
    await loadAndRenderAssets();
    window.scrollTo(0, 0);
}

// Initialize Application
document.addEventListener('DOMContentLoaded', async function() {
    const authenticated = checkLogin();
    setupEventListeners();

    if (!authenticated) {
        return;
    }

    loadUsers();
    loadNotifications();
    await loadAssets();
    initSidebar();
    showMainApp();
});

// Load Users from LocalStorage
function loadUsers() {
    const stored = localStorage.getItem('users');
    if (stored) {
        users = JSON.parse(stored);
    } else {
        // Default users
        users = [
            { username: 'admin', password: 'admin123', role: 'admin' },
            { username: 'pic', password: 'pic123', role: 'pic' }
        ];
        saveUsers();
    }
}

// Save Users to LocalStorage
function saveUsers() {
    localStorage.setItem('users', JSON.stringify(users));
}

// Check Login Status
function checkLogin() {
    if (window.auth?.isAuthenticated && window.auth?.getCurrentUser) {
        const storedUser = window.auth.getCurrentUser();
        if (window.auth.isAuthenticated() && storedUser) {
            currentUser = storedUser;
            return true;
        }
    }

    showLoginPage();
    return false;
}

// Show Login Page
function showLoginPage() {
    document.getElementById('loginPage').classList.remove('d-none');
    document.getElementById('mainApp').classList.add('d-none');
}

// Show Main App
function showMainApp() {
    document.getElementById('loginPage').classList.add('d-none');
    document.getElementById('mainApp').classList.remove('d-none');
    
    // Update user info
    document.getElementById('userName').textContent = currentUser.username;
    document.getElementById('userRoleDisplay').textContent = currentUser.role === 'admin' ? 'Administrator' : 'PIC';
    
    // Apply role-based access
    applyRoleAccess();
    updateNotificationBadge();
    
    // Initialize location tracking
    initLocationTracking();
    
    // Show dashboard
    showPage('dashboard');
}

// Apply Role-based Access
function applyRoleAccess() {
    const adminOnlyElements = document.querySelectorAll('.admin-only');
    const picOnlyElements = document.querySelectorAll('.pic-only');

    if (currentUser.role === 'admin') {
        adminOnlyElements.forEach(el => el.classList.remove('d-none'));
        picOnlyElements.forEach(el => el.classList.add('d-none'));
    } else {
        adminOnlyElements.forEach(el => el.classList.add('d-none'));
        picOnlyElements.forEach(el => el.classList.remove('d-none'));
    }
}

// Initialize Location Tracking
function initLocationTracking() {
    const locationEnabled = localStorage.getItem('locationEnabled');
    if (locationEnabled === 'false') return;
    
    if (navigator.geolocation) {
        // Get initial position
        navigator.geolocation.getCurrentPosition(
            (position) => {
                currentCoordinates = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                updateLocationDisplay(currentCoordinates);
            },
            (error) => {
                document.getElementById('currentLocation').textContent = 'Tidak tersedia';
            }
        );
        
        // Watch position
        locationWatcher = navigator.geolocation.watchPosition(
            (position) => {
                currentCoordinates = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                updateLocationDisplay(currentCoordinates);
            },
            (error) => {
                console.log('Location error:', error);
            },
            { enableHighAccuracy: true, maximumAge: 10000 }
        );
    }
}

// Update Location Display
function updateLocationDisplay(coords) {
    if (coords) {
        document.getElementById('currentLocation').textContent = 
            `${coords.lat.toFixed(6)}, ${coords.lng.toFixed(6)}`;
    }
}

// Initialize Sidebar
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const content = document.getElementById('content');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            content.classList.toggle('expanded');
        });
    }
    
    // Set active nav link
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const page = this.getAttribute('data-page');
            showPage(page);
            
            // Update active state
            document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });
}

// Setup Event Listeners
function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            filterAssets(e.target.value);
        });
    }
    
    // Modal events
    const assetModal = document.getElementById('assetModal');
    if (assetModal) {
        assetModal.addEventListener('hidden.bs.modal', function() {
            resetForm();
        });
    }
}

// Show Page
function showPage(page) {
    stopScanPage();
    currentPage = page;
    
    // Hide all pages
    document.querySelectorAll('.page').forEach(p => p.classList.add('d-none'));
    
    // Guard page access
    if (window.auth?.guardRoute && !window.auth.guardRoute()) {
        return;
    }

    // Show selected page
    const pageElement = document.getElementById(page + 'Page');
    if (pageElement) {
        pageElement.classList.remove('d-none');
    }
    
    // Update content based on page
    switch(page) {
        case 'dashboard':
            updateDashboard();
            renderDashboardNotifications();
            break;
        case 'laptop':
            renderTable('laptop');
            break;
        case 'printer':
            renderTable('printer');
            break;
        case 'notifikasi':
            renderNotifications();
            break;
        case 'scan':
            initScanPage();
            break;
        case 'laporan':
            currentPageNum = 1;
            loadAndRenderAssets();
            break;
        case 'pengaturan':
            renderUserTable();
            break;
    }
}

function initScanPage() {
    const resultContainer = document.getElementById('scanResult');
    resultContainer.innerHTML = '<p class="text-muted">Scan QR Code untuk melihat detail aset</p>';

    if (window.qrScanner?.startQrScanner) {
        window.qrScanner.startQrScanner('qr-reader', onScanSuccess, onScanError, {
            fps: 10,
            qrbox: { width: 250, height: 250 }
        });
    } else {
        resultContainer.innerHTML = '<div class="alert alert-warning">QR scanner tidak tersedia. Pastikan library HTML5-QRCode dimuat.</div>';
    }
}

function stopScanPage() {
    if (window.qrScanner?.stopQrScanner) {
        window.qrScanner.stopQrScanner();
    }
}

function onScanSuccess(decodedText, coords) {
    const assetId = parseScannedCode(decodedText);
    if (!assetId) {
        onScanError(new Error('Kode QR tidak valid'));
        return;
    }

    if (window.assetsAPI?.scanAsset) {
        window.assetsAPI.scanAsset(assetId, coords?.latitude, coords?.longitude)
            .then(asset => {
                if (asset) {
                    displayScanResult(asset);
                } else {
                    onScanError(new Error('Aset tidak ditemukan'));
                }
            })
            .catch(err => {
                console.warn('scanAsset API gagal', err);
                onScanError(err);
            });
    } else {
        const asset = assets.find(a => a.kodeAset === assetId || a.id === assetId);
        if (asset) {
            displayScanResult(asset);
        } else {
            onScanError(new Error('Aset tidak ditemukan'));
        }
    }
}

function onScanError(error) {
    document.getElementById('scanResult').innerHTML = `
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i> ${error.message || 'Gagal memindai QR Code.'}
        </div>
    `;
}

function parseScannedCode(decodedText) {
    try {
        const payload = JSON.parse(decodedText);
        return payload.id || payload.kode || payload.code || payload.asset_id || payload.assetId || null;
    } catch (e) {
        return decodedText;
    }
}


// Update Dashboard
function updateDashboard() {
    const laptops = assets.filter(a => a.jenis === 'laptop');
    const printers = assets.filter(a => a.jenis === 'printer');
    const perluPerbaikan = assets.filter(a => a.kondisi === 'Rusak Ringan' || a.kondisi === 'Rusak Berat' || a.kondisi === 'Dalam Perbaikan');
    
    document.getElementById('totalAset').textContent = assets.length;
    document.getElementById('totalLaptop').textContent = laptops.length;
    document.getElementById('totalPrinter').textContent = printers.length;
    document.getElementById('perluPerbaikan').textContent = perluPerbaikan.length;
    
    // Update charts
    updateCharts();
}

// Update Charts
function updateCharts() {
    // Kondisi Chart
    const kondisiCounts = {};
    assets.forEach(a => {
        kondisiCounts[a.kondisi] = (kondisiCounts[a.kondisi] || 0) + 1;
    });
    
    const kondisiCtx = document.getElementById('kondisiChart');
    if (kondisiCtx) {
        if (kondisiChart) kondisiChart.destroy();
        kondisiChart = new Chart(kondisiCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(kondisiCounts),
                datasets: [{
                    data: Object.values(kondisiCounts),
                    backgroundColor: ['#27ae60', '#f39c12', '#e74c3c', '#9b59b6']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }
    
    // Lokasi Chart
    const lokasiCounts = {};
    assets.forEach(a => {
        lokasiCounts[a.lokasi] = (lokasiCounts[a.lokasi] || 0) + 1;
    });
    
    const lokasiCtx = document.getElementById('lokasiChart');
    if (lokasiCtx) {
        if (lokasiChart) lokasiChart.destroy();
        lokasiChart = new Chart(lokasiCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(lokasiCounts),
                datasets: [{
                    label: 'Jumlah Aset',
                    data: Object.values(lokasiCounts),
                    backgroundColor: '#3498db'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
}

// Render Table
function renderTable(type) {
    const filteredAssets = assets.filter(a => a.jenis === type);
    const tbody = document.querySelector('#' + type + 'Table tbody');
    
    if (tbody) {
        tbody.innerHTML = filteredAssets.map((asset, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${asset.kodeAset}</strong></td>
                <td>${asset.namaAset}</td>
                <td>${asset.merkType}</td>
                <td>${asset.serialNumber || '-'}</td>
                <td>${asset.lokasi}</td>
                <td>${asset.koordinat ? `<small>${asset.koordinat.lat.toFixed(4)}, ${asset.koordinat.lng.toFixed(4)}</small>` : '-'}</td>
                <td><span class="badge badge-${asset.kondisi.toLowerCase().replace(' ', '-')}">${asset.kondisi}</span></td>
                <td>
                    <button type="button" class="btn btn-action btn-view" onclick="viewAsset('${asset.id}')" title="Lihat" aria-label="Lihat aset">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button type="button" class="btn btn-action btn-edit" onclick="editAsset('${asset.id}')" title="Edit" aria-label="Edit aset">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button type="button" class="btn btn-action" onclick="showQRCode('${asset.id}')" title="QR Code" aria-label="Lihat QR Code">
                        <i class="fas fa-qrcode"></i>
                    </button>
                    <button type="button" class="btn btn-action btn-delete" onclick="deleteAsset('${asset.id}')" title="Hapus" aria-label="Hapus aset">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }
}

// Render Laporan
function renderLaporan() {
    const tbody = document.querySelector('#laporanTable tbody');
    if (tbody) {
        tbody.innerHTML = assets.map((asset, index) => `
            <tr>
                <td>${(currentPageNum - 1) * itemsPerPage + index + 1}</td>
                <td><strong>${asset.kodeAset}</strong></td>
                <td>${asset.jenis === 'laptop' ? 'Laptop' : 'Printer'}</td>
                <td>${asset.namaAset}</td>
                <td>${asset.merkType}</td>
                <td>${asset.serialNumber || '-'}</td>
                <td>${asset.lokasi}</td>
                <td>${asset.koordinat ? `<small>${asset.koordinat.lat.toFixed(4)}, ${asset.koordinat.lng.toFixed(4)}</small>` : '-'}</td>
                <td><span class="badge badge-${asset.kondisi.toLowerCase().replace(' ', '-')}">${asset.kondisi}</span></td>
                <td>${asset.tglPerolehan ? formatDate(asset.tglPerolehan) : '-'}</td>
            </tr>
        `).join('');
    }
}

// Render User Table
function renderUserTable() {
    const tbody = document.querySelector('#userTable tbody');
    if (tbody) {
        tbody.innerHTML = users.map((user, index) => `
            <tr>
                <td>${user.username}</td>
                <td><span class="badge bg-${user.role === 'admin' ? 'primary' : 'info'}">${user.role === 'admin' ? 'Admin' : 'PIC'}</span></td>
                <td>
                    <button class="btn btn-action btn-delete" onclick="deleteUser('${user.username}')" title="Hapus" ${user.username === 'admin' ? 'disabled' : ''}>
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `).join('');
    }
}

// Filter Assets
function filterAssets(searchTerm) {
    debounceSearch(searchTerm);
}

// Filter by Kondisi
function setFilterKondisi(kondisi) {
    currentFilterKondisi = kondisi;
    currentPageNum = 1;
    loadAndRenderAssets();
}

// Filter by Jenis
function setFilterJenis(jenis) {
    currentFilterJenis = jenis;
    currentPageNum = 1;
    loadAndRenderAssets();
}

// Filter by Lokasi
function setFilterLokasi(lokasi) {
    currentFilterLokasi = lokasi;
    currentPageNum = 1;
    loadAndRenderAssets();
}

// Clear All Filters
function clearFilters() {
    currentSearchTerm = '';
    currentFilterKondisi = '';
    currentFilterJenis = '';
    currentFilterLokasi = '';
    currentPageNum = 1;
    document.getElementById('searchInput').value = '';
    loadAndRenderAssets();
}

// Show Modal
function showModal(type, id = null) {
    const modal = new bootstrap.Modal(document.getElementById('assetModal'));
    const modalTitle = document.getElementById('modalTitle');
    const assetType = document.getElementById('assetType');
    const assetTypeDisplay = document.getElementById('assetTypeDisplay');
    
    assetType.value = type;
    assetTypeDisplay.value = type === 'laptop' ? 'Laptop' : 'Printer';
    editingId = id;
    
    if (id) {
        const asset = assets.find(a => a.id === id);
        modalTitle.textContent = 'Edit Aset';
        fillForm(asset);
    } else {
        modalTitle.textContent = 'Tambah ' + (type === 'laptop' ? 'Laptop' : 'Printer');
        generateKodeAset(type);
    }
    
    // Update auto location
    updateAutoLocation();
    
    modal.show();
}

// Update Auto Location in Modal
function updateAutoLocation() {
    const autoLocationEl = document.getElementById('autoLocation');
    if (currentCoordinates) {
        autoLocationEl.textContent = `${currentCoordinates.lat.toFixed(6)}, ${currentCoordinates.lng.toFixed(6)}`;
    } else {
        autoLocationEl.textContent = 'Mendeteksi...';
        // Try to get location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                currentCoordinates = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };
                autoLocationEl.textContent = `${currentCoordinates.lat.toFixed(6)}, ${currentCoordinates.lng.toFixed(6)}`;
            });
        }
    }
}

// Generate Kode Aset
function generateKodeAset(type) {
    const prefix = type === 'laptop' ? 'LPT' : 'PRT';
    const filtered = assets.filter(a => a.jenis === type);
    const nextNum = filtered.length + 1;
    const kodeAset = prefix + '-' + String(nextNum).padStart(3, '0');
    document.getElementById('kodeAset').value = kodeAset;
}

// Fill Form
function fillForm(asset) {
    document.getElementById('assetId').value = asset.id;
    document.getElementById('assetType').value = asset.jenis;
    document.getElementById('assetTypeDisplay').value = asset.jenis === 'laptop' ? 'Laptop' : 'Printer';
    document.getElementById('kodeAset').value = asset.kodeAset;
    document.getElementById('namaAset').value = asset.namaAset;
    document.getElementById('merkType').value = asset.merkType;
    document.getElementById('serialNumber').value = asset.serialNumber || '';
    document.getElementById('lokasi').value = asset.lokasi;
    document.getElementById('kondisi').value = asset.kondisi;
    document.getElementById('tglPerolehan').value = asset.tglPerolehan || '';
    document.getElementById('harga').value = asset.harga || '';
    document.getElementById('keterangan').value = asset.keterangan || '';
}

// Reset Form
function resetForm() {
    document.getElementById('assetForm').reset();
    document.getElementById('assetId').value = '';
    editingId = null;
}

// Save Asset
async function saveAsset() {
    const form = document.getElementById('assetForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const assetType = document.getElementById('assetType').value;
    let assetData = {
        id: editingId || generateId(),
        kodeAset: document.getElementById('kodeAset').value,
        namaAset: document.getElementById('namaAset').value,
        merkType: document.getElementById('merkType').value,
        serialNumber: document.getElementById('serialNumber').value,
        lokasi: document.getElementById('lokasi').value,
        kondisi: document.getElementById('kondisi').value,
        tglPerolehan: document.getElementById('tglPerolehan').value,
        harga: parseInt(document.getElementById('harga').value) || 0,
        keterangan: document.getElementById('keterangan').value,
        jenis: assetType,
        koordinat: currentCoordinates ? { ...currentCoordinates } : null,
        updatedAt: new Date().toISOString()
    };
    
    try {
        if (editingId && window.assetsAPI?.updateAsset) {
            assetData = await window.assetsAPI.updateAsset(editingId, assetData) || assetData;
        } else if (!editingId && window.assetsAPI?.createAsset) {
            assetData = await window.assetsAPI.createAsset(assetData) || assetData;
        }
    } catch (error) {
        console.warn('API saveAsset gagal, menggunakan local storage', error);
    }
    
    if (editingId) {
        const index = assets.findIndex(a => a.id === editingId);
        if (index !== -1) assets[index] = { ...assets[index], ...assetData };
    } else {
        assetData.createdAt = assetData.createdAt || new Date().toISOString();
        assets.push(assetData);
    }
    
    saveAssets();
    handleAssetNotification(assetData, !editingId);
    bootstrap.Modal.getInstance(document.getElementById('assetModal')).hide();
    showToast('Data aset berhasil disimpan!', 'success');
    showPage(assetType);
}

// Generate ID
function generateId() {
    return 'AS' + Date.now();
}

// View Asset
function viewAsset(id) {
    const asset = assets.find(a => a.id === id);
    if (!asset) return;
    
    const detailContent = document.getElementById('detailContent');
    detailContent.innerHTML = `
        <div class="row g-3">
            <div class="col-md-6">
                <div class="detail-card p-3 rounded border bg-white shadow-sm">
                    <h6 class="detail-card-title">Informasi Aset</h6>
                    <dl class="row mb-0">
                        <dt class="col-5 detail-label">Kode Aset</dt>
                        <dd class="col-7 detail-value">${asset.kodeAset}</dd>

                        <dt class="col-5 detail-label">Nama Aset</dt>
                        <dd class="col-7 detail-value">${asset.namaAset}</dd>

                        <dt class="col-5 detail-label">Merk/Type</dt>
                        <dd class="col-7 detail-value">${asset.merkType}</dd>

                        <dt class="col-5 detail-label">Serial Number</dt>
                        <dd class="col-7 detail-value">${asset.serialNumber || '-'}</dd>
                    </dl>
                </div>
            </div>
            <div class="col-md-6">
                <div class="detail-card p-3 rounded border bg-white shadow-sm">
                    <h6 class="detail-card-title">Status & Lokasi</h6>
                    <dl class="row mb-0">
                        <dt class="col-5 detail-label">Lokasi</dt>
                        <dd class="col-7 detail-value">${asset.lokasi}</dd>

                        <dt class="col-5 detail-label">Koordinat</dt>
                        <dd class="col-7 detail-value">${asset.koordinat ? `${asset.koordinat.lat.toFixed(6)}, ${asset.koordinat.lng.toFixed(6)}` : '-'}</dd>

                        <dt class="col-5 detail-label">Kondisi</dt>
                        <dd class="col-7 detail-value"><span class="badge badge-${asset.kondisi.toLowerCase().replace(' ', '-')}">${asset.kondisi}</span></dd>

                        <dt class="col-5 detail-label">Tanggal Perolehan</dt>
                        <dd class="col-7 detail-value">${asset.tglPerolehan ? formatDate(asset.tglPerolehan) : '-'}</dd>
                    </dl>
                </div>
            </div>
        </div>
        ${asset.keterangan ? `
        <div class="detail-card p-3 rounded border bg-white shadow-sm mt-3">
            <h6 class="detail-card-title">Keterangan</h6>
            <p class="mb-0 detail-value">${asset.keterangan}</p>
        </div>
        ` : ''}
        <div class="mt-4 text-center">
            <button class="btn btn-primary" onclick="showQRCode('${asset.id}')">
                <i class="fas fa-qrcode"></i> Lihat QR Code
            </button>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('detailModal'));
    modal.show();
}

// Edit Asset
function editAsset(id) {
    showModal(assets.find(a => a.id === id).jenis, id);
}

// Delete Asset
async function deleteAsset(id) {
    const asset = assets.find(a => a.id === id);
    if (confirm('Apakah Anda yakin ingin menghapus aset ini?')) {
        try {
            if (window.assetsAPI?.deleteAsset) {
                await window.assetsAPI.deleteAsset(id);
            }
        } catch (error) {
            console.warn('API deleteAsset gagal, hapus lokal tetap dilakukan', error);
        }

        assets = assets.filter(a => a.id !== id);
        saveAssets();
        if (currentUser && currentUser.role === 'pic' && asset) {
            addNotification(
                'Aset dihapus',
                `Aset ${asset.kodeAset} (${asset.namaAset}) telah dihapus.`, 
                'warning',
                'pic'
            );
        }
        showToast('Aset berhasil dihapus!', 'success');
        showPage(currentPage);
    }
}

// Show QR Code
function showQRCode(id) {
    const asset = assets.find(a => a.id === id);
    if (!asset) return;
    
    const qrContainer = document.getElementById('qrcode');
    qrContainer.innerHTML = '';
    
    new QRCode(qrContainer, {
        text: JSON.stringify({ kode: asset.kodeAset, id: asset.id }),
        width: 200,
        height: 200,
        colorDark: '#000000',
        colorLight: '#ffffff',
        correctLevel: QRCode.CorrectLevel.H
    });
    
    document.getElementById('qrCodeLabel').textContent = asset.kodeAset + ' - ' + asset.namaAset;
    
    const modal = new bootstrap.Modal(document.getElementById('qrModal'));
    modal.show();
}

// Print QR Code
function printQRCode() {
    const qrContent = document.getElementById('qrcode').innerHTML;
    const label = document.getElementById('qrCodeLabel').textContent;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>QR Code - ${label}</title>
            <style>
                body { font-family: Arial, sans-serif; text-align: center; padding: 50px; }
                .qr-container { display: inline-block; padding: 20px; border: 2px solid #333; border-radius: 10px; }
                .label { margin-top: 15px; font-size: 18px; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="qr-container">
                ${qrContent}
                <div class="label">${label}</div>
            </div>
            <script>window.onload = function() { window.print(); window.close(); }<\/script>
        </body>
        </html>
    `);
    printWindow.document.close();
}

// Manual Scan
async function manualScan() {
    const code = document.getElementById('manualCodeInput').value.trim();
    if (!code) {
        showToast('Masukkan kode aset terlebih dahulu!', 'warning');
        return;
    }

    let asset = null;
    if (window.assetsAPI?.scanAsset) {
        try {
            asset = await window.assetsAPI.scanAsset(code, currentCoordinates?.lat || null, currentCoordinates?.lng || null);
        } catch (e) {
            console.warn('API manualScan gagal', e);
        }
    }

    if (!asset && window.assetsAPI?.getAsset) {
        asset = await window.assetsAPI.getAsset(code);
    }

    if (!asset) {
        asset = assets.find(a => a.kodeAset === code || a.id === code);
    }

    if (asset) {
        displayScanResult(asset);
    } else {
        document.getElementById('scanResult').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Aset dengan kode "${code}" tidak ditemukan!
            </div>
        `;
    }
}

// Display Scan Result
function displayScanResult(asset) {
    const resultDiv = document.getElementById('scanResult');
    resultDiv.innerHTML = `
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">${asset.namaAset}</h5>
                <p class="card-text">
                    <strong>Kode:</strong> ${asset.kodeAset}<br>
                    <strong>Merk/Type:</strong> ${asset.merkType}<br>
                    <strong>Lokasi:</strong> ${asset.lokasi}<br>
                    <strong>Koordinat:</strong> ${asset.koordinat ? `${asset.koordinat.lat.toFixed(6)}, ${asset.koordinat.lng.toFixed(6)}` : '-'}<br>
                    <strong>Kondisi:</strong> <span class="badge badge-${asset.kondisi.toLowerCase().replace(' ', '-')}">${asset.kondisi}</span>
                </p>
                <button class="btn btn-primary btn-sm" onclick="viewAsset('${asset.id}')">
                    <i class="fas fa-eye"></i> Lihat Detail
                </button>
            </div>
        </div>
    `;
}

// Export Excel
function exportExcel() {
    let csv = 'No,Kode Aset,Jenis,Nama Aset,Merk/Type,Serial Number,Lokasi,Koordinat,Kondisi,Tanggal Perolehan,Harga\n';
    
    assets.forEach((asset, index) => {
        const koordinat = asset.koordinat ? `${asset.koordinat.lat}, ${asset.koordinat.lng}` : '';
        csv += `${index + 1},${asset.kodeAset},${asset.jenis === 'laptop' ? 'Laptop' : 'Printer'},${asset.namaAset},"${asset.merkType}",${asset.serialNumber || ''},${asset.lokasi},${koordinat},${asset.kondisi},${asset.tglPerolehan || ''},${asset.harga || 0}\n`;
    });
    
    downloadFile(csv, 'laporan_aset.csv', 'text/csv');
}

// Export PDF
function exportPDF() {
    showToast('Fitur Export PDF dalam pengembangan!', 'info');
}

// Download File
function downloadFile(content, filename, type) {
    const blob = new Blob([content], { type: type });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Show User Modal
function showUserModal() {
    const modal = new bootstrap.Modal(document.getElementById('userModal'));
    document.getElementById('userForm').reset();
    modal.show();
}

// Save User
function saveUser() {
    const form = document.getElementById('userForm');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    const username = document.getElementById('userUsername').value;
    const password = document.getElementById('userPassword').value;
    const role = document.getElementById('userRole').value;
    
    // Check if user exists
    if (users.find(u => u.username === username)) {
        showToast('Username sudah digunakan!', 'error');
        return;
    }
    
    users.push({ username, password, role });
    saveUsers();
    bootstrap.Modal.getInstance(document.getElementById('userModal')).hide();
    showToast('User berhasil ditambahkan!', 'success');
    renderUserTable();
}

// Delete User
function deleteUser(username) {
    if (username === 'admin') {
        showToast('Tidak dapat menghapus user admin!', 'error');
        return;
    }
    
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        users = users.filter(u => u.username !== username);
        saveUsers();
        showToast('User berhasil dihapus!', 'success');
        renderUserTable();
    }
}

// Update Location Settings
function updateLocationSettings() {
    const enabled = document.getElementById('locationEnabled').checked;
    const defaultLocation = document.getElementById('defaultLocation').value;
    
    localStorage.setItem('locationEnabled', enabled);
    localStorage.setItem('defaultLocation', defaultLocation);
    
    if (enabled && !locationWatcher) {
        initLocationTracking();
    } else if (!enabled && locationWatcher) {
        navigator.geolocation.clearWatch(locationWatcher);
        locationWatcher = null;
        document.getElementById('currentLocation').textContent = 'Nonaktif';
    }
    
    showToast('Pengaturan lokasi disimpan!', 'success');
}

// Format Date
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
}

// Format Currency
function formatCurrency(amount) {
    if (!amount) return 'Rp 0';
    return 'Rp ' + amount.toLocaleString('id-ID');
}

// Show Toast
function showToast(message, type = 'info') {
    const toastContainer = document.querySelector('.toast-container') || createToastContainer();
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'warning' ? 'warning' : type === 'error' ? 'danger' : 'info'} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    setTimeout(() => toast.remove(), 3000);
}

// Create Toast Container
function createToastContainer() {
    const container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
    return container;
}

function loadNotifications() {
    const stored = localStorage.getItem('notifications');
    if (stored) {
        notifications = JSON.parse(stored);
    } else {
        notifications = [];
    }
}

function saveNotifications() {
    localStorage.setItem('notifications', JSON.stringify(notifications));
}

function generateNotificationId() {
    return 'N' + Date.now();
}

function getUnreadNotificationCount() {
    if (!currentUser) return 0;
    return notifications.filter(n => !n.read && (n.role === currentUser.role || n.role === 'all')).length;
}

function updateNotificationBadge() {
    const badge = document.getElementById('notificationBadge');
    if (!badge) return;

    const count = getUnreadNotificationCount();
    if (count > 0) {
        badge.textContent = count;
        badge.classList.remove('d-none');
    } else {
        badge.classList.add('d-none');
    }
}

function addNotification(title, message, type = 'info', role = 'pic') {
    notifications.unshift({
        id: generateNotificationId(),
        title,
        message,
        type,
        role,
        read: false,
        createdAt: new Date().toISOString()
    });
    saveNotifications();
    updateNotificationBadge();
}

function handleAssetNotification(asset, isNew = false) {
    if (!currentUser || currentUser.role !== 'pic') {
        return;
    }

    if (asset.kondisi !== 'Baik') {
        addNotification(
            `Perhatian pada aset ${asset.kodeAset}`,
            `Aset ${asset.namaAset} di ${asset.lokasi} berstatus ${asset.kondisi}.`,
            'warning',
            'pic'
        );
    } else if (isNew) {
        addNotification(
            `Aset baru ditambahkan`,
            `Aset ${asset.namaAset} (${asset.kodeAset}) berhasil ditambahkan.`,
            'success',
            'pic'
        );
    }
}

function markNotificationRead(id) {
    notifications = notifications.map(n => n.id === id ? { ...n, read: true } : n);
    saveNotifications();
    renderNotifications();
    renderDashboardNotifications();
    updateNotificationBadge();
}

function markAllNotificationsRead() {
    notifications = notifications.map(n => ({ ...n, read: true }));
    saveNotifications();
    renderNotifications();
    renderDashboardNotifications();
    updateNotificationBadge();
}

function getNotificationTypeLabel(type) {
    switch(type) {
        case 'success': return 'Berhasil';
        case 'warning': return 'Peringatan';
        case 'error': return 'Error';
        default: return 'Info';
    }
}

function renderDashboardNotifications() {
    const row = document.getElementById('dashboardNotificationRow');
    const list = document.getElementById('dashboardNotificationList');
    if (!row || !list) return;

    if (!currentUser || currentUser.role !== 'pic') {
        row.classList.add('d-none');
        return;
    }

    const relevant = notifications.filter(n => n.role === 'pic' || n.role === 'all').slice(0, 3);
    if (relevant.length === 0) {
        list.innerHTML = '<p class="text-muted mb-0">Tidak ada notifikasi baru.</p>';
    } else {
        list.innerHTML = relevant.map((notif) => `
            <div class="notification-item ${notif.read ? '' : 'unread'}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="notification-title">${notif.title}</div>
                        <div class="notification-message">${notif.message}</div>
                    </div>
                    <small class="text-muted">${new Date(notif.createdAt).toLocaleString('id-ID')}</small>
                </div>
            </div>
        `).join('');
    }

    row.classList.remove('d-none');
}

function renderNotifications() {
    const list = document.getElementById('notificationList');
    if (!list) return;

    const relevant = notifications.filter(n => n.role === 'pic' || n.role === 'all');
    if (relevant.length === 0) {
        list.innerHTML = '<p class="text-muted mb-0">Belum ada notifikasi.</p>';
        return;
    }

    list.innerHTML = relevant.map((notif) => `
        <div class="notification-item ${notif.read ? '' : 'unread'}">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h6 class="notification-title mb-1">${notif.title}</h6>
                    <p class="notification-message mb-0">${notif.message}</p>
                </div>
                <span class="badge bg-${notif.type === 'success' ? 'success' : notif.type === 'warning' ? 'warning' : notif.type === 'error' ? 'danger' : 'info'}">
                    ${getNotificationTypeLabel(notif.type)}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center notification-meta">
                <small>${new Date(notif.createdAt).toLocaleString('id-ID')}</small>
                <button class="btn btn-sm btn-outline-secondary" onclick="markNotificationRead('${notif.id}')">
                    ${notif.read ? 'Sudah Dibaca' : 'Tandai Dibaca'}
                </button>
            </div>
        </div>
    `).join('');
}

function initDefaultNotifications() {
    if (notifications.length > 0) return;
    assets.filter(a => a.kondisi !== 'Baik').forEach(asset => {
        addNotification(
            `Aset ${asset.kodeAset} perlu perhatian`,
            `Aset ${asset.namaAset} di ${asset.lokasi} berstatus ${asset.kondisi}.`,
            'warning',
            'pic'
        );
    });
}

// Load Assets from LocalStorage or API
async function loadAssets() {
    if (window.assetsAPI?.fetchAssets) {
        try {
            assets = await window.assetsAPI.fetchAssets();
        } catch (e) {
            console.warn('Gagal fetch assets dari API, fallback localStorage', e);
        }
    }

    if (!assets || assets.length === 0) {
        const stored = localStorage.getItem('asetKantor');
        if (stored) {
            assets = JSON.parse(stored);
        } else {
            assets = [
                {
                    id: 'LPT001',
                    kodeAset: 'LPT-001',
                    namaAset: 'MacBook Pro 14"',
                    merkType: 'Apple MacBook Pro M2',
                    serialNumber: 'C02XG0KDJGH5',
                    lokasi: 'Ruang Direksi',
                    kondisi: 'Baik',
                    tglPerolehan: '2024-01-15',
                    harga: 25000000,
                    keterangan: 'Untuk Direktur Utama',
                    jenis: 'laptop',
                    koordinat: { lat: -6.200000, lng: 106.816666 },
                    createdAt: new Date().toISOString()
                },
                {
                    id: 'LPT002',
                    kodeAset: 'LPT-002',
                    namaAset: 'ThinkPad X1 Carbon',
                    merkType: 'Lenovo ThinkPad X1 Carbon Gen 11',
                    serialNumber: 'PF2K4R8J',
                    lokasi: 'Ruang IT',
                    kondisi: 'Baik',
                    tglPerolehan: '2024-02-20',
                    harga: 18000000,
                    keterangan: 'Untuk Staff IT',
                    jenis: 'laptop',
                    koordinat: { lat: -6.200000, lng: 106.816666 },
                    createdAt: new Date().toISOString()
                },
                {
                    id: 'PRT001',
                    kodeAset: 'PRT-001',
                    namaAset: 'LaserJet Pro',
                    merkType: 'HP LaserJet Pro M404n',
                    serialNumber: 'PHC2345678',
                    lokasi: 'Ruang Rapat',
                    kondisi: 'Baik',
                    tglPerolehan: '2023-06-10',
                    harga: 4500000,
                    keterangan: 'Ruang Rapat Lantai 2',
                    jenis: 'printer',
                    koordinat: { lat: -6.200000, lng: 106.816666 },
                    createdAt: new Date().toISOString()
                },
                {
                    id: 'PRT002',
                    kodeAset: 'PRT-002',
                    namaAset: 'OfficeJet Pro',
                    merkType: 'HP OfficeJet Pro 9015e',
                    serialNumber: 'TH53R12345',
                    lokasi: 'Ruang HRD',
                    kondisi: 'Rusak Ringan',
                    tglPerolehan: '2023-08-15',
                    harga: 5500000,
                    keterangan: 'Perlu penggantian cartridge',
                    jenis: 'printer',
                    koordinat: { lat: -6.200000, lng: 106.816666 },
                    createdAt: new Date().toISOString()
                }
            ];
            saveAssets();
        }
    }

    initDefaultNotifications();
    updateDashboard();
}

// Save Assets to LocalStorage
function saveAssets() {
    localStorage.setItem('asetKantor', JSON.stringify(assets));
}

// Make functions globally available
window.showModal = showModal;
window.saveAsset = saveAsset;
window.viewAsset = viewAsset;
window.editAsset = editAsset;
window.deleteAsset = deleteAsset;
window.showQRCode = showQRCode;
window.printQRCode = printQRCode;
window.manualScan = manualScan;
window.exportExcel = exportExcel;
window.exportPDF = exportPDF;
window.filterAssets = filterAssets;
window.setFilterKondisi = setFilterKondisi;
window.setFilterJenis = setFilterJenis;
window.setFilterLokasi = setFilterLokasi;
window.clearFilters = clearFilters;
window.goToPage = goToPage;
window.showUserModal = showUserModal;
window.saveUser = saveUser;
window.deleteUser = deleteUser;
window.updateLocationSettings = updateLocationSettings;