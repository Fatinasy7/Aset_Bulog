#  PANDUAN FRONTEND (CORE LOGIC) — Khansa Mufidah

> **Peran:** Programmer Bidang Frontend — Core Logic & API Integration
> **Proyek:** Sistem Manajemen Aset BULOG
> **Stack:** JavaScript (Axios / Fetch API), HTML5 Geolocation API, HTML5-QRCode / Instascan

---

##  CARA MENGGUNAKAN PANDUAN INI

Gunakan prompt berikut saat membuka VS Code dan memulai sesi kerja:

##  Ringkasan Backend API untuk Frontend
- Backend sudah menyediakan `auth`, `assets`, `pics`, `notifications`, `reports`, dan `backups`.
- Semua route API penting dilindungi oleh `auth:sanctum` kecuali `auth/register` dan `auth/login`.
- Response utama memakai format `camelCase` untuk output, namun request body create/update asset/pic tetap menggunakan `snake_case`.
- Endpoint `GET /api/reports/assets` hanya bisa diakses oleh role `admin_it` atau `manajemen`.
- Pastikan setiap request terautentikasi dengan header `Authorization: Bearer <token>`.

### Contoh ringkas Axios
```javascript
import axios from 'axios';

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
});

api.interceptors.request.use(config => {
  const token = localStorage.getItem('auth_token');
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

export default api;
```

### Contoh penggunaan Axios
```javascript
// Login
const loginResponse = await api.post('/auth/login', {
  email: 'admin-baru-banget@example.com',
  password: 'Password123!',
});
localStorage.setItem('auth_token', loginResponse.data.token);

// Ambil user saat ini
const userResponse = await api.get('/user');
console.log(userResponse.data);

// Ambil daftar aset
const assetsResponse = await api.get('/assets');
console.log(assetsResponse.data);
```

Gunakan prompt berikut saat membuka VS Code dan memulai sesi kerja:

```
Disini saya mendapati tugas sebagai Frontend (2) (Khansa Mufidah),
maka dari itu tolong analisa terlebih dahulu agar saya dapat melihat apa saja
yang belum dan apa saja yang sudah dikerjakan dalam proyek ini. Dalam pengerjaan
diusahakan membuat pull request dan branch dengan nama branch-nya disesuaikan
dengan apa yang dikerjakan, dan jika pekerjaan sudah selesai buatkan file yang
berisi laporan yang telah dikerjakan sebagai bentuk laporan kepada PM.
```

---

##  Tanggung Jawab Utama

- Setup **arsitektur frontend** & konfigurasi HTTP Client (Axios/Fetch API)
- Mengintegrasikan semua **form & halaman dengan API Backend (Fatin)**
- Implementasi **Route Guarding** (proteksi halaman tanpa login)
- Menyimpan & mengelola **token autentikasi (JWT/Session)** di browser
- [**TUGAS KRUSIAL**] Integrasi **library QR Scanner** (kamera smartphone/laptop)
- Implementasi **HTML5 Geolocation API** bersamaan dengan proses scan QR Code
- Integrasi **Dashboard** dengan data real-time dari backend
- Integrasi **tombol ekspor laporan** dengan file yang digenerate backend

---

## � Catatan Penting untuk Khansa

- Semua request API selain `auth/register` dan `auth/login` wajib memakai header `Authorization: Bearer <token>`.
- Field respon utama sudah menggunakan format `camelCase`.
- Request body untuk create/update asset dan PIC tetap memakai field `snake_case` karena controller validasi menerima input database.
- Endpoint `GET /api/user` sudah disesuaikan agar responsnya juga `camelCase`.
- Jika diperlukan, berikutnya bisa bantu buat contoh kode axios untuk auth dan panggilan endpoint, atau dokumentasi endpoint dalam bentuk tabel.

---

##  CHECKLIST TUGAS LENGKAP

### 🔷 MINGGU 1 — Setup Arsitektur & Autentikasi

#### Setup HTTP Client & Struktur File JS
- [ ] Inisiasi struktur folder JavaScript (lihat referensi di bawah)
- [ ] Install/import **Axios** via CDN atau npm:
  ```html
  <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
  ```
- [ ] Buat **`api.js`** — file konfigurasi base URL dan interceptor:
  ```javascript
  // resources/js/api.js
  const api = axios.create({
    baseURL: '/api',
    headers: { 'Content-Type': 'application/json' }
  });

  // Auto-attach token ke setiap request
  api.interceptors.request.use(config => {
    const token = localStorage.getItem('auth_token');
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
  });

  // Redirect ke login jika token expired (401)
  api.interceptors.response.use(
    response => response,
    error => {
      if (error.response?.status === 401) {
        localStorage.removeItem('auth_token');
        window.location.href = '/login';
      }
      return Promise.reject(error);
    }
  );
  ```
- [ ] **Branch:** `feature/frontend-setup`

#### Integrasi Autentikasi (FR-01, FR-02, FR-03, FR-04)
- [ ] Hubungkan form login (buatan Wahyu) ke `POST /api/auth/login`
- [ ] Simpan token JWT / session ID di `localStorage` atau `sessionStorage` setelah login berhasil
- [ ] Simpan data user (nama, role) di `localStorage` untuk keperluan tampilan
- [ ] Implementasi **Route Guarding:**
  ```javascript
  // Cek di awal setiap halaman (kecuali login)
  function guardRoute() {
    const token = localStorage.getItem('auth_token');
    if (!token) window.location.href = '/login';
  }
  guardRoute();
  ```
- [ ] Sembunyikan/tampilkan elemen UI berdasarkan **role** (Admin IT vs User/PIC vs Manajemen)
- [ ] Hubungkan tombol Logout ke `POST /api/auth/logout` + hapus token dari storage
- [ ] **Branch:** `feature/auth-integration`

---

### 🔷 MINGGU 2 — Integrasi Data Dinamis

#### Integrasi Halaman Daftar Aset (FR-05–FR-08)
- [ ] Fetch data dari `GET /api/assets` dan render ke tabel HTML dinamis
- [ ] Implementasi **filter real-time:** kondisi, jenis, lokasi (kirim sebagai query param)
- [ ] Implementasi **search bar** dengan debounce (delay 300ms sebelum hit API)
- [ ] Implementasi **pagination** (tampilkan halaman 1, 2, 3, dst.)
- [ ] Tombol **Tambah Aset** → arahkan ke form tambah aset
- [ ] Tombol **Edit** → fetch data aset via `GET /api/assets/{id}` → isi form edit
- [ ] Tombol **Hapus** → konfirmasi modal → `DELETE /api/assets/{id}`
- [ ] **Branch:** `feature/asset-list-integration`

#### Integrasi Form Aset (FR-05, FR-06)
- [ ] Kirim data form tambah aset via `POST /api/assets`
- [ ] Kirim data form edit aset via `PUT /api/assets/{id}`
- [ ] Tampilkan **pesan sukses** atau **pesan error** dari response API
- [ ] Validasi sisi klien sebelum submit (field wajib tidak boleh kosong)
- [ ] Jika API mengembalikan error validasi (422), tampilkan error di bawah field terkait
- [ ] **Branch:** `feature/asset-form-integration`

#### Integrasi Manajemen PIC (FR-09–FR-12)
- [ ] Fetch daftar PIC dari `GET /api/pics` dan tampilkan ke tabel
- [ ] Form tambah PIC → `POST /api/pics`
- [ ] Form edit PIC → `PUT /api/pics/{id}`
- [ ] Hapus PIC → `DELETE /api/pics/{id}` dengan konfirmasi
- [ ] Dropdown PIC di form aset → diisi dari data `GET /api/pics`
- [ ] Assign PIC ke aset → `POST /api/assets/{id}/assign-pic`
- [ ] **Branch:** `feature/pic-integration`

---

### 🔷 MINGGU 2 — TUGAS KRUSIAL: QR Scanner + Geotagging

#### Implementasi QR Code Scanner (FR-15, FR-16 | NFR-03)
>  **Ini adalah fitur paling kritis dalam proyek ini. Target: scan → data tampil dalam < 3 detik.**

- [ ] Import library HTML5-QRCode via CDN:
  ```html
  <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
  ```
- [ ] Buat elemen container kamera di halaman scan:
  ```html
  <div id="qr-reader" style="width:300px"></div>
  <div id="qr-reader-results"></div>
  ```
- [ ] Implementasi inisiasi kamera dan scan:
  ```javascript
  const html5QrCode = new Html5Qrcode("qr-reader");

  html5QrCode.start(
    { facingMode: "environment" }, // kamera belakang di smartphone
    { fps: 10, qrbox: { width: 250, height: 250 } },
    (decodedText, decodedResult) => {
      // decodedText = asset_id atau kode unik aset
      onScanSuccess(decodedText);
    },
    (errorMessage) => { /* abaikan error sementara */ }
  );
  ```
- [ ] Setelah scan berhasil, **HENTIKAN kamera** untuk hemat baterai:
  ```javascript
  html5QrCode.stop().then(() => console.log('Kamera dimatikan'));
  ```
- [ ] **Branch:** `feature/qr-scanner`

#### Implementasi Geotagging Bersamaan Saat Scan (FR-16, FR-20)
- [ ] Setelah QR Code terbaca, langsung ambil koordinat lokasi:
  ```javascript
  function onScanSuccess(assetId) {
    if (!navigator.geolocation) {
      alert('Geolocation tidak didukung perangkat ini.');
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        sendScanData(assetId, latitude, longitude);
      },
      (error) => {
        // Jika geolocation ditolak, tetap kirim scan tanpa koordinat
        sendScanData(assetId, null, null);
      }
    );
  }

  function sendScanData(assetId, latitude, longitude) {
    api.post(`/assets/${assetId}/scan`, {
      latitude,
      longitude,
      scanned_at: new Date().toISOString()
    }).then(response => {
      // Tampilkan detail aset dari response
      displayAssetDetail(response.data);
    });
  }
  ```
- [ ] Tampilkan detail aset yang berhasil di-scan (nama, kondisi, PIC, lokasi terakhir)
- [ ] Tampilkan pesan error jika aset tidak ditemukan (404)
- [ ] **Branch:** `feature/qr-geotagging` (dikerjakan bersamaan dengan `feature/qr-scanner`)

---

### 🔷 MINGGU 2–3 — Dashboard & Laporan

#### Integrasi Dashboard Utama (FR-21)
- [x] Fetch ringkasan data dari `GET /api/dashboard/summary`
- [ ] Render **counter cards** (total aset, laptop, printer, PIC aktif) dinamis
- [ ] Render **grafik kondisi aset** menggunakan Chart.js:
  ```javascript
  // Import Chart.js via CDN dulu di blade template
  const ctx = document.getElementById('chartKondisi').getContext('2d');
  new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: ['BAIK', 'RUSAK RINGAN', 'RUSAK BERAT', 'DALAM PERBAIKAN', 'TIDAK AKTIF'],
      datasets: [{ data: [summary.baik, summary.rusak_ringan, ...], ... }]
    }
  });
  ```
- [ ] Render **tabel aset terbaru / bermasalah** dinamis dari API
- [ ] **Branch:** `feature/dashboard-integration`

#### Integrasi Laporan & Ekspor (FR-25, FR-26, FR-27)
- [ ] Form filter laporan → kirim ke `GET /api/reports/assets?kondisi=...&format=preview`
- [ ] Render hasil filter ke tabel preview
- [ ] Tombol **Export PDF** → `GET /api/reports/assets?format=pdf` → trigger download file
- [ ] Tombol **Export Excel** → `GET /api/reports/assets?format=excel` → trigger download file
  ```javascript
  function exportLaporan(format) {
    const params = new URLSearchParams(getFilterValues());
    params.append('format', format);
    // Gunakan window.location untuk trigger download
    window.location.href = `/api/reports/assets?${params.toString()}`;
  }
  ```
- [ ] **Branch:** `feature/report-integration`

#### Integrasi Audit Trail (FR-08, FR-12)
- [ ] Fetch data riwayat dari `GET /api/asset-histories`
- [ ] Render ke tabel dinamis dengan kolom: Tanggal, Aset, Perubahan, Nilai Lama, Nilai Baru, Oleh
- [ ] Implementasi filter berdasarkan tanggal
- [ ] **Branch:** `feature/audit-trail-integration`

---

### 🔷 MINGGU 3 — Pengujian & Finalisasi

#### Pengujian Performa QR Scanner (NFR-03)
- [ ] Uji scan QR Code di smartphone (Android & iOS) — pastikan kamera bisa diakses
- [ ] Ukur waktu dari scan berhasil hingga data aset tampil — **target < 3 detik**
- [ ] Uji skenario: koneksi lambat, geolocation ditolak, QR Code rusak (FR-17)
- [ ] Uji di berbagai kondisi cahaya (ruang gelap, cahaya terang, dll.)

#### Sinkronisasi Final dengan Backend (Fatin)
- [ ] Koordinasi terkait format response JSON dari setiap endpoint
- [ ] Selesaikan masalah **CORS** (jika ada — Fatin yang handle di Laravel, tapi Khansa yang report)
- [ ] Selesaikan masalah **parsing JSON** yang tidak sesuai ekspektasi
- [ ] Selesaikan ketidaksesuaian nama parameter (pastikan nama field form == nama field API)
- [ ] Uji integrasi penuh: Login → Dashboard → Tambah Aset → Scan QR → Laporan

#### Bug Fixing & Build Final
- [ ] Perbaiki bug logika frontend berdasarkan feedback UAT BULOG
- [ ] Pastikan semua loading state (spinner/skeleton) tampil saat data sedang di-fetch
- [ ] Pastikan semua error dari API ditampilkan dengan pesan yang user-friendly
- [ ] **Build Final Frontend**

---

## 🗂️ Struktur File JavaScript yang Disarankan

```
resources/js/
├── api.js               ← konfigurasi Axios (base URL, interceptor)
├── auth.js              ← logika login, logout, route guard
├── assets.js            ← fetch, render, CRUD aset
├── pics.js              ← fetch, render, CRUD PIC
├── qr-scanner.js        ← logika kamera + scan QR Code
├── geolocation.js       ← logika HTML5 Geolocation API
├── dashboard.js         ← fetch & render data dashboard + Chart.js
├── report.js            ← logika filter laporan + ekspor
└── audit.js             ← fetch & render riwayat perubahan
```

---

## 🔌 Library yang Digunakan

| Library | Fungsi | CDN |
|---------|--------|-----|
| Axios | HTTP Client untuk API request | `cdn.jsdelivr.net/npm/axios` |
| HTML5-QRCode | QR Code scanner via kamera browser | `unpkg.com/html5-qrcode` |
| Chart.js | Visualisasi grafik dashboard | `cdn.jsdelivr.net/npm/chart.js` |
| HTML5 Geolocation API | Ambil koordinat lokasi (built-in browser, tidak perlu CDN) | — |

---

## ⚠️ Hal Penting yang Perlu Diperhatikan

1. **HTTPS Wajib untuk Kamera & Geolocation:** Browser modern hanya mengizinkan akses kamera dan geolocation di halaman HTTPS. Pastikan environment development sudah pakai `https://` atau gunakan `localhost` (exception khusus).

2. **Permission Kamera:** Minta izin kamera dengan pesan yang jelas kepada pengguna. Jika ditolak, tampilkan panduan cara mengaktifkannya di pengaturan browser.

3. **Geolocation Bisa Ditolak:** Selalu handle kasus geolocation ditolak. Scan tetap bisa berjalan, hanya tanpa data koordinat.

4. **CORS Error:** Jika terjadi error CORS saat development, minta Fatin untuk menambahkan origin frontend ke konfigurasi CORS Laravel.

5. **Token Storage:** Simpan token di `localStorage` untuk kemudahan, tapi waspadai XSS. Pertimbangkan `httpOnly cookie` jika tim ingin lebih aman (koordinasikan dengan Fatin).

---

## 📝 Format Laporan Akhir (Setelah Selesai)

Buat file `LAPORAN_KHANSA_FRONTEND_LOGIC.md` di root project:

```markdown
# Laporan Pengerjaan Frontend Core Logic — Khansa Mufidah

## Ringkasan
- Periode pengerjaan: [tanggal mulai] s.d. [tanggal selesai]
- Total PR yang dibuat: [jumlah]
- Fitur krusial selesai: QR Scanner + Geotagging ✅ / ❌

## Fitur yang Diselesaikan
| Fitur | Branch | Status PR | Waktu Performa |
|-------|--------|-----------|----------------|
| Setup Frontend + Auth | feature/frontend-setup | Merged | — |
| QR Scanner + Geotagging | feature/qr-scanner | Merged | ~X detik |
| Dashboard Integration | feature/dashboard-integration | Merged | — |
| ... | ... | ... | ... |

## Kendala & Solusi
- [Kendala]: [Solusi yang diterapkan]

## Hasil Uji Performa QR Scanner
- Rata-rata waktu scan → tampil data: X detik (target < 3 detik)
- Perangkat yang diuji: [sebutkan]

## Catatan untuk PM
- [Hal penting untuk diketahui PM]
```

---

*Panduan ini mengacu pada SRS v1.0 (FR-13 s.d. FR-21, NFR-01, NFR-03) dan Laporan Distribusi Tugas Tim UAS BULOG.*
