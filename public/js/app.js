/**
 * Sistem Manajemen Aset Perkantoran
 * Main Application JavaScript
 */

// Global Variables
let assets = [];
let users = [];
let pics = [];
let notifications = [];
let currentUser = null;
let currentPage = 'dashboard';
let editingId = null;
let qrCodeInstance = null;
let kondisiChart = null;
let lokasiChart = null;
let locationWatcher = null;
let currentCoordinates = null;
let qrScanner = null;
let qrScannerActive = false;

function getStoredApiToken() {
    const token = localStorage.getItem('apiToken');
    if (!token || token === 'null' || token === 'undefined') {
        return null;
    }
    return token;
}

// Initialize Application
document.addEventListener('DOMContentLoaded', function() {
    attachApiDefaults();
    loadUsers();
    loadNotifications();
    checkLogin();
    initSidebar();
    setupEventListeners();
});

function attachApiDefaults() {
    if (!window.axios) return;

    const configuredBaseUrl = window.__API_BASE_URL__ || '';
    window.axios.defaults.baseURL = configuredBaseUrl || window.location.origin;

    window.axios.defaults.headers.common['Accept'] = 'application/json';
    window.axios.defaults.headers.common['Content-Type'] = 'application/json';
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

    const token = getStoredApiToken();
    if (token) {
        window.axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    } else {
        delete window.axios.defaults.headers.common['Authorization'];
    }

    window.axios.interceptors.response.use(
        (response) => response,
        (error) => {
            if (error.response?.status === 401) {
                localStorage.removeItem('apiToken');
                localStorage.removeItem('currentUser');
                delete window.axios.defaults.headers.common['Authorization'];
                window.location.href = '/';
            }
            return Promise.reject(error);
        }
    );
}

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
async function checkLogin() {
    const storedUser = localStorage.getItem('currentUser');
    const apiToken = getStoredApiToken();

    if (storedUser && apiToken) {
        currentUser = JSON.parse(storedUser);
        attachApiDefaults();
        try {
            await loadProfile();
            await loadAssets();
            showMainApp();
            return;
        } catch (error) {
            console.warn('Autentikasi gagal saat memeriksa profil atau aset:', error);
        }
    }

    localStorage.removeItem('currentUser');
    localStorage.removeItem('apiToken');
    showLoginPage();
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
    document.getElementById('userName').textContent = currentUser.username || currentUser.name || 'User';
    document.getElementById('userRoleDisplay').textContent = currentUser.role === 'admin' ? 'Administrator' : currentUser.role === 'pic' ? 'PIC' : 'Manajemen';
    
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

    if (currentUser?.role === 'admin') {
        adminOnlyElements.forEach(el => el.classList.remove('d-none'));
        picOnlyElements.forEach(el => el.classList.add('d-none'));
    } else if (currentUser?.role === 'pic') {
        adminOnlyElements.forEach(el => el.classList.add('d-none'));
        picOnlyElements.forEach(el => el.classList.remove('d-none'));
    } else {
        adminOnlyElements.forEach(el => el.classList.add('d-none'));
        picOnlyElements.forEach(el => el.classList.add('d-none'));
    }
}

async function loadProfile() {
    if (!localStorage.getItem('apiToken')) return;

    try {
        const response = await window.axios.get('/api/user');
        const payload = response.data?.data || response.data;
        const user = {
            ...payload,
            username: payload.name,
            role: payload.role === 'admin_it' ? 'admin' : payload.role === 'user_pic' ? 'pic' : payload.role === 'manajemen' ? 'manager' : payload.role
        };

        currentUser = user;
        localStorage.setItem('currentUser', JSON.stringify(user));
    } catch (error) {
        console.warn('Profile fetch failed:', error);
    }
}

async function login(email, password) {
    try {
        const response = await window.axios.post('/api/auth/login', { email, password });
        const payload = response.data;
        console.log('Respon server (login):', payload);

        const authToken = payload.auth_token || payload.token || payload.access_token;
        const user = {
            ...payload.user,
            username: payload.user.name,
            role: payload.user.role === 'admin_it' ? 'admin' : payload.user.role === 'user_pic' ? 'pic' : payload.user.role === 'manajemen' ? 'manager' : payload.user.role
        };

        if (!authToken) {
            throw new Error('Token autentikasi tidak ditemukan dalam response login.');
        }

        currentUser = user;
        localStorage.setItem('currentUser', JSON.stringify(user));
        localStorage.setItem('apiToken', authToken);
        if (window.axios) {
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
        }
        console.log('Login sukses, token disimpan:', authToken);
        attachApiDefaults();
        await loadProfile();
        await loadAssets();
        await loadNotifications();
        showMainApp();
        showToast(payload.message || 'Login berhasil!', 'success');
    } catch (error) {
        console.error('Login gagal:', error.response?.data || error);
        const message = error.response?.data?.message || 'Login gagal. Cek kredensial Anda.';
        showToast(message, 'error');
    }
}

async function register(name, email, password, passwordConfirmation, role) {
    try {
        const response = await window.axios.post('/api/auth/register', {
            name,
            email,
            password,
            password_confirmation: passwordConfirmation,
            role
        });
        const payload = response.data;
        console.log('Respon server (register):', payload);
        const authToken = payload.auth_token || payload.token || payload.access_token;
        const user = {
            ...payload.user,
            username: payload.user.name,
            role: payload.user.role === 'admin_it' ? 'admin' : payload.user.role === 'user_pic' ? 'pic' : payload.user.role === 'manajemen' ? 'manager' : payload.user.role
        };

        if (!authToken) {
            throw new Error('Token autentikasi tidak ditemukan dalam response registrasi.');
        }

        currentUser = user;
        localStorage.setItem('currentUser', JSON.stringify(user));
        localStorage.setItem('apiToken', authToken);
        if (window.axios) {
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
        }
        console.log('Registrasi sukses, token disimpan:', authToken);
        attachApiDefaults();
        await loadProfile();
        await loadAssets();
        showMainApp();
        showToast(payload.message || 'Registrasi berhasil!', 'success');
    } catch (error) {
        console.error('Registrasi gagal:', error.response?.data || error);
        const message = error.response?.data?.message || 'Registrasi gagal.';
        const errors = error.response?.data?.errors;
        const validationMessage = errors ? Object.values(errors).flat().join(' ') : message;
        showToast(validationMessage, 'error');
    }
}

// Logout Function
async function logout() {
    try {
        if (localStorage.getItem('apiToken')) {
            await window.axios.post('/api/auth/logout');
        }
    } catch (error) {
        console.warn('Logout API error:', error);
    }

    currentUser = null;
    localStorage.removeItem('currentUser');
    localStorage.removeItem('apiToken');
    delete window.axios.defaults.headers.common['Authorization'];
    if (locationWatcher) {
        navigator.geolocation.clearWatch(locationWatcher);
    }
    showLoginPage();
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
    const scanPageLink = document.querySelector('.nav-link[data-page="scan"]');
    if (scanPageLink) {
        scanPageLink.addEventListener('click', function() {
            setTimeout(initQrScanner, 300);
        });
    }

    // Login form
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('loginEmail').value.trim();
            const password = document.getElementById('loginPassword').value;
            login(email, password);
        });
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const name = document.getElementById('registerName').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            const passwordConfirmation = document.getElementById('registerPasswordConfirmation').value;
            const role = document.getElementById('registerRole').value;
            register(name, email, password, passwordConfirmation, role);
        });
    }

    const picForm = document.getElementById('picForm');
    if (picForm) {
        picForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const id = document.getElementById('picId').value;
            const payload = {
                name: document.getElementById('picNama').value.trim(),
                email: document.getElementById('picEmail').value.trim(),
                role: document.getElementById('picJabatan').value,
                telepon: document.getElementById('picTelepon').value.trim(),
                password: document.getElementById('picPassword').value,
            };

            try {
                if (id) {
                    await window.axios.put(`/api/pics/${id}`, payload);
                } else {
                    await window.axios.post('/api/pics', payload);
                }
                picForm.reset();
                document.getElementById('picId').value = '';
                await loadPics();
                showToast('PIC berhasil disimpan.', 'success');
            } catch (error) {
                const message = error.response?.data?.message || 'Gagal menyimpan PIC.';
                showToast(message, 'error');
            }
        });
    }
    
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
    currentPage = page;
    
    // Hide all pages
    document.querySelectorAll('.page').forEach(p => p.classList.add('d-none'));
    
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
        case 'laporan':
            renderLaporan();
            break;
        case 'pics':
            loadPics();
            break;
        case 'pengaturan':
            renderUserTable();
            break;
    }
}

// Update Dashboard
function updateDashboard() {
    const laptops = assets.filter(a => (a.jenis || '').toLowerCase() === 'laptop');
    const printers = assets.filter(a => (a.jenis || '').toLowerCase() === 'printer');
    const perluPerbaikan = assets.filter(a => {
        const kondisi = (a.kondisi || '').toLowerCase();
        return kondisi.includes('rusak') || kondisi.includes('perbaikan');
    });

    const totalAsetEl = document.getElementById('totalAset');
    const totalLaptopEl = document.getElementById('totalLaptop');
    const totalPrinterEl = document.getElementById('totalPrinter');
    const perluPerbaikanEl = document.getElementById('perluPerbaikan');

    if (totalAsetEl) totalAsetEl.textContent = assets.length;
    if (totalLaptopEl) totalLaptopEl.textContent = laptops.length;
    if (totalPrinterEl) totalPrinterEl.textContent = printers.length;
    if (perluPerbaikanEl) perluPerbaikanEl.textContent = perluPerbaikan.length;

    updateCharts();
}

// Update Charts
function updateCharts() {
    const kondisiCounts = {};
    assets.forEach(a => {
        const kondisi = (a.kondisi || 'Tidak Diketahui').toString();
        kondisiCounts[kondisi] = (kondisiCounts[kondisi] || 0) + 1;
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
                    backgroundColor: ['#27ae60', '#f39c12', '#e74c3c', '#9b59b6', '#3498db']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    const lokasiCounts = {};
    assets.forEach(a => {
        const lokasi = (a.lokasi || 'Tidak Diketahui').toString();
        lokasiCounts[lokasi] = (lokasiCounts[lokasi] || 0) + 1;
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
        if (!filteredAssets.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="9" class="text-center text-muted">Belum ada data ${type}.</td>
                </tr>
            `;
            return;
        }

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
        if (!assets.length) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center text-muted">Belum ada data aset.</td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = assets.map((asset, index) => `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${asset.kodeAset || '-'}</strong></td>
                <td>${(asset.jenis || '').toLowerCase() === 'laptop' ? 'Laptop' : 'Printer'}</td>
                <td>${asset.namaAset || '-'}</td>
                <td>${asset.merkType || '-'}</td>
                <td>${asset.serialNumber || '-'}</td>
                <td>${asset.lokasi || '-'}</td>
                <td>${asset.koordinat ? `<small>${asset.koordinat.lat.toFixed(4)}, ${asset.koordinat.lng.toFixed(4)}</small>` : '-'}</td>
                <td><span class="badge badge-${(asset.kondisi || 'baik').toLowerCase().replace(' ', '-')}">${asset.kondisi || '-'}</span></td>
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
    const term = searchTerm.toLowerCase();
    const filtered = assets.filter(a => 
        a.kodeAset.toLowerCase().includes(term) ||
        a.namaAset.toLowerCase().includes(term) ||
        a.merkType.toLowerCase().includes(term) ||
        a.lokasi.toLowerCase().includes(term)
    );
    
    const tbody = document.querySelector('#laporanTable tbody');
    if (tbody) {
        tbody.innerHTML = filtered.map((asset, index) => `
            <tr>
                <td>${index + 1}</td>
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
    const payload = {
        kode_aset: document.getElementById('kodeAset').value,
        nama_aset: document.getElementById('namaAset').value,
        merk_type: document.getElementById('merkType').value,
        serial_number: document.getElementById('serialNumber').value,
        lokasi: document.getElementById('lokasi').value,
        kondisi: document.getElementById('kondisi').value,
        tgl_perolehan: document.getElementById('tglPerolehan').value,
        harga: parseInt(document.getElementById('harga').value) || 0,
        keterangan: document.getElementById('keterangan').value,
        jenis: assetType,
        koordinat_lat: currentCoordinates?.lat ?? null,
        koordinat_lng: currentCoordinates?.lng ?? null,
    };

    try {
        let response;
        if (editingId) {
            response = await window.axios.put(`/api/assets/${editingId}`, payload);
        } else {
            response = await window.axios.post('/api/assets', payload);
        }

        const savedAsset = response.data?.data || response.data;
        const normalized = normalizeAsset(savedAsset);

        if (editingId) {
            assets = assets.map(asset => asset.id === editingId ? normalized : asset);
        } else {
            assets.unshift(normalized);
        }

        saveAssets();
        bootstrap.Modal.getInstance(document.getElementById('assetModal')).hide();
        showToast('Data aset berhasil disimpan!', 'success');
        showPage(assetType);
        await loadAssets();
    } catch (error) {
        const message = error.response?.data?.message || 'Gagal menyimpan aset.';
        showToast(message, 'error');
    }
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
    if (!confirm('Apakah Anda yakin ingin menghapus aset ini?')) return;

    try {
        await window.axios.delete(`/api/assets/${id}`);
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
    } catch (error) {
        showToast('Gagal menghapus aset.', 'error');
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

async function initQrScanner() {
    const container = document.getElementById('qr-reader');
    if (!container) return;

    if (qrScannerActive) return;

    if (typeof window.Html5Qrcode === 'undefined') {
        container.innerHTML = '<div class="alert alert-warning">Library scanner tidak tersedia di browser ini.</div>';
        return;
    }

    container.innerHTML = '<div class="text-muted">Memulai kamera...</div>';
    qrScanner = new window.Html5Qrcode('qr-reader');
    qrScannerActive = true;

    try {
        await qrScanner.start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            async (decodedText) => {
                const normalized = decodedText.trim();
                if (!normalized) return;

                try {
                    await qrScanner.stop();
                    qrScannerActive = false;
                } catch (error) {
                    console.warn('Tidak bisa menghentikan scanner:', error);
                }

                await processScannedCode(normalized);
            },
            () => {}
        );
    } catch (error) {
        console.warn('Gagal menginisialisasi scanner:', error);
        container.innerHTML = '<div class="alert alert-warning">Kamera tidak tersedia atau izin diblokir. Anda tetap bisa memakai pencarian manual.</div>';
        qrScannerActive = false;
    }
}

async function processScannedCode(code) {
    const normalizedCode = code.trim();
    if (!normalizedCode) {
        showToast('Kode QR tidak valid.', 'warning');
        return;
    }

    try {
        const response = await window.axios.get('/api/assets');
        const payload = Array.isArray(response.data) ? response.data : response.data?.data || [];
        const asset = payload.find(item => {
            const candidate = item.kode_aset || item.kodeAset || '';
            const idCandidate = item.id?.toString();
            return candidate.toString() === normalizedCode || idCandidate === normalizedCode;
        });

        if (!asset) {
            throw new Error('Aset tidak ditemukan');
        }

        const normalized = normalizeAsset(asset);
        const scanResult = await sendScanData(normalized.id);
        const scannedAsset = scanResult?.asset ? normalizeAsset(scanResult.asset) : normalized;

        if (scannedAsset?.id) {
            assets = assets.map(item => item.id === scannedAsset.id ? scannedAsset : item);
            saveAssets();
        }

        displayScanResult(scannedAsset);
        showToast(scanResult?.message || 'Scan berhasil.', 'success');
    } catch (error) {
        console.warn('Scan process failed:', error);
        document.getElementById('scanResult').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Aset dengan kode "${normalizedCode}" tidak ditemukan atau server tidak merespons.
            </div>
        `;
    }
}

// Manual Scan
async function manualScan() {
    const code = document.getElementById('manualCodeInput').value.trim();
    if (!code) {
        showToast('Masukkan kode aset terlebih dahulu!', 'warning');
        return;
    }

    try {
        const response = await window.axios.get('/api/assets');
        const payload = Array.isArray(response.data) ? response.data : response.data?.data || [];
        const asset = payload.find(item => {
            const candidate = item.kode_aset || item.kodeAset || '';
            const idCandidate = item.id?.toString();
            return candidate.toString() === code || idCandidate === code;
        });

        if (asset) {
            const normalized = normalizeAsset(asset);
            const scanResult = await sendScanData(normalized.id);
            const scannedAsset = scanResult?.asset ? normalizeAsset(scanResult.asset) : normalized;

            if (scannedAsset?.id) {
                assets = assets.map(item => item.id === scannedAsset.id ? scannedAsset : item);
                saveAssets();
            }

            displayScanResult(scannedAsset);
            showToast(scanResult?.message || 'Scan berhasil.', 'success');
        } else {
            throw new Error('Aset tidak ditemukan');
        }
    } catch (error) {
        console.warn('Manual scan failed:', error);
        document.getElementById('scanResult').innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> Aset dengan kode "${code}" tidak ditemukan atau server tidak merespons.
            </div>
        `;
    }
}

async function sendScanData(assetId) {
    const payload = {
        latitude: currentCoordinates?.lat ?? null,
        longitude: currentCoordinates?.lng ?? null,
        scanned_by: currentUser?.name || currentUser?.username || 'anonymous',
        scanned_at: new Date().toISOString(),
    };

    try {
        const response = await window.axios.post(`/api/assets/${assetId}/scan`, payload);
        return response.data;
    } catch (error) {
        console.warn('Scan API error:', error);
        showToast('Pencatatan scan gagal, tetapi data aset tetap ditampilkan.', 'warning');
        return null;
    }
}

function stopQrScanner() {
    if (!qrScanner || !qrScannerActive) return;

    qrScanner.stop().then(() => {
        qrScannerActive = false;
    }).catch(() => {
        qrScannerActive = false;
    });
}

// Display Scan Result
function displayScanResult(asset) {
    const resultDiv = document.getElementById('scanResult');
    const assetName = asset.nama_aset || asset.namaAset || 'Aset';
    const assetCode = asset.kode_aset || asset.kodeAset || '-';
    const assetMerk = asset.merk_type || asset.merkType || '-';
    const assetLocation = asset.lokasi || '-';
    const assetCondition = asset.kondisi || '-';
    const assetCoordinates = asset.koordinat && asset.koordinat.lat != null && asset.koordinat.lng != null
        ? `${Number(asset.koordinat.lat).toFixed(6)}, ${Number(asset.koordinat.lng).toFixed(6)}`
        : (asset.koordinat_lat != null && asset.koordinat_lng != null
            ? `${Number(asset.koordinat_lat).toFixed(6)}, ${Number(asset.koordinat_lng).toFixed(6)}`
            : '-');

    resultDiv.innerHTML = `
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">${assetName}</h5>
                <p class="card-text">
                    <strong>Kode:</strong> ${assetCode}<br>
                    <strong>Merk/Type:</strong> ${assetMerk}<br>
                    <strong>Lokasi:</strong> ${assetLocation}<br>
                    <strong>Koordinat:</strong> ${assetCoordinates}<br>
                    <strong>Kondisi:</strong> <span class="badge badge-${assetCondition.toLowerCase().replace(' ', '-')}">${assetCondition}</span>
                </p>
                <button class="btn btn-primary btn-sm" onclick="viewAsset('${asset.id}')">
                    <i class="fas fa-eye"></i> Lihat Detail
                </button>
            </div>
        </div>
    `;
}

// Export Excel
async function exportExcel() {
    try {
        const response = await window.axios.get('/api/reports/assets', { params: { format: 'excel' } });
        const data = Array.isArray(response.data) ? response.data : response.data?.data || [];
        const reportData = data.length ? data : assets;
        let csv = 'No,Kode Aset,Jenis,Nama Aset,Merk/Type,Serial Number,Lokasi,Koordinat,Kondisi,Tanggal Perolehan,Harga\n';

        reportData.forEach((asset, index) => {
            const normalized = normalizeAsset(asset);
            const koordinat = normalized.koordinat ? `${normalized.koordinat.lat}, ${normalized.koordinat.lng}` : '';
            csv += `${index + 1},${normalized.kodeAset || ''},${(normalized.jenis || '').toLowerCase() === 'laptop' ? 'Laptop' : 'Printer'},${normalized.namaAset || ''},"${normalized.merkType || ''}",${normalized.serialNumber || ''},${normalized.lokasi || ''},${koordinat},${normalized.kondisi || ''},${normalized.tglPerolehan || ''},${normalized.harga || 0}\n`;
        });

        downloadFile(csv, 'laporan_aset.csv', 'text/csv');
        showToast('Laporan Excel diunduh.', 'success');
    } catch (error) {
        showToast('Gagal mengambil data laporan dari server.', 'error');
    }
}

// Export PDF
async function exportPDF() {
    try {
        await window.axios.get('/api/reports/assets', { params: { format: 'pdf' } });
        showToast('Export PDF sedang disiapkan.', 'info');
    } catch (error) {
        showToast('Gagal mengambil data laporan PDF.', 'error');
    }
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

async function loadNotifications() {
    const stored = localStorage.getItem('notifications');
    if (stored) {
        notifications = JSON.parse(stored);
    } else {
        notifications = [];
    }

    if (!localStorage.getItem('apiToken')) return;

    try {
        const response = await window.axios.get('/api/notifications');
        const payload = response.data?.data || [];
        notifications = payload.map((item) => ({
            id: item.id,
            title: item.title || 'Notifikasi',
            message: item.message || '',
            type: 'info',
            role: 'all',
            read: Boolean(item.read),
            createdAt: item.createdAt || new Date().toISOString(),
        }));
        saveNotifications();
    } catch (error) {
        console.warn('Notifications fetch failed:', error);
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

async function markNotificationRead(id) {
    try {
        await window.axios.patch(`/api/notifications/${id}/read`);
    } catch (error) {
        console.warn('Mark read failed:', error);
    }

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

function normalizePicRoleForForm(role) {
    if (!role) return 'user_pic';
    const normalized = role.toLowerCase();
    if (normalized === 'pic' || normalized === 'user_pic') return 'user_pic';
    if (normalized === 'admin' || normalized === 'admin_it') return 'admin_it';
    if (normalized === 'manajemen' || normalized === 'manager') return 'manajemen';
    return 'user_pic';
}

async function loadPics() {
    try {
        const response = await window.axios.get('/api/pics');
        const payload = response.data;
        pics = Array.isArray(payload) ? payload : (payload?.data || []);
        pics = pics.map((pic) => ({
            id: pic.id,
            nama: pic.name || pic.nama || '',
            email: pic.email || '',
            jabatan: pic.role || pic.jabatan || 'user_pic',
            telepon: pic.telepon || '',
        }));
        renderPicsPage();
    } catch (error) {
        console.warn('PIC fetch failed:', error);
        pics = [];
        renderPicsPage();
    }
}

function renderPicsPage() {
    const tbody = document.querySelector('#picsTable tbody');
    if (!tbody) return;

    if (!pics.length) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center text-muted">Belum ada data PIC.</td>
            </tr>
        `;
        return;
    }

    tbody.innerHTML = pics.map((pic) => `
        <tr>
            <td>${pic.nama || '-'}</td>
            <td>${pic.email || '-'}</td>
            <td>${pic.jabatan || '-'}</td>
            <td>${pic.telepon || '-'}</td>
            <td>
                <button class="btn btn-sm btn-outline-secondary" onclick="editPic('${pic.id}')">Edit</button>
                <button class="btn btn-sm btn-outline-danger" onclick="deletePic('${pic.id}')">Hapus</button>
            </td>
        </tr>
    `).join('');
}

function editPic(picId) {
    const pic = pics.find(p => p.id.toString() === picId.toString());
    if (!pic) return;
    document.getElementById('picId').value = pic.id;
    document.getElementById('picNama').value = pic.nama || '';
    document.getElementById('picEmail').value = pic.email || '';
    document.getElementById('picJabatan').value = normalizePicRoleForForm(pic.jabatan);
    document.getElementById('picTelepon').value = pic.telepon || '';
    document.getElementById('picPassword').value = '';
}

async function deletePic(id) {
    if (!confirm('Hapus PIC ini?')) return;

    try {
        await window.axios.delete(`/api/pics/${id}`);
        await loadPics();
        showToast('PIC berhasil dihapus.', 'success');
    } catch (error) {
        showToast('Gagal menghapus PIC.', 'error');
    }
}

function normalizeAsset(asset = {}) {
    const koordinat = asset.koordinat
        ? { lat: asset.koordinat.lat, lng: asset.koordinat.lng }
        : (asset.koordinat_lat != null || asset.koordinat_lng != null)
            ? { lat: asset.koordinat_lat, lng: asset.koordinat_lng }
            : null;

    return {
        ...asset,
        id: asset.id,
        kodeAset: asset.kode_aset || asset.kodeAset,
        namaAset: asset.nama_aset || asset.namaAset,
        merkType: asset.merk_type || asset.merkType,
        serialNumber: asset.serial_number || asset.serialNumber,
        lokasi: asset.lokasi,
        kondisi: asset.kondisi,
        tglPerolehan: asset.tgl_perolehan || asset.tglPerolehan,
        harga: asset.harga,
        keterangan: asset.keterangan,
        jenis: asset.jenis,
        koordinat,
        createdAt: asset.createdAt || asset.created_at,
        updatedAt: asset.updatedAt || asset.updated_at,
    };
}

// Load Assets from API or fallback
async function loadAssets() {
    const stored = localStorage.getItem('asetKantor');

    if (stored && !localStorage.getItem('apiToken')) {
        assets = JSON.parse(stored);
        initDefaultNotifications();
        updateDashboard();
        return;
    }

    try {
        const response = await window.axios.get('/api/assets');
        const payload = Array.isArray(response.data) ? response.data : response.data?.data || [];
        assets = payload.map(normalizeAsset);
        saveAssets();
    } catch (error) {
        const fallback = JSON.parse(stored || '[]');
        assets = fallback.length ? fallback : [];
        showToast('Gagal memuat data aset dari server. Menampilkan data lokal.', 'warning');
    }

    initDefaultNotifications();
    updateDashboard();

    if (currentPage === 'laptop' || currentPage === 'printer') {
        renderTable(currentPage);
    } else if (currentPage === 'laporan') {
        renderLaporan();
    } else if (currentPage === 'notifikasi') {
        renderNotifications();
    }
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
window.loadPics = loadPics;
window.editPic = editPic;
window.deletePic = deletePic;
window.manualScan = manualScan;
window.exportExcel = exportExcel;
window.exportPDF = exportPDF;
window.filterAssets = filterAssets;
window.logout = logout;
window.showUserModal = showUserModal;
window.saveUser = saveUser;
window.deleteUser = deleteUser;
window.updateLocationSettings = updateLocationSettings;
window.editPic = editPic;
window.deletePic = deletePic;