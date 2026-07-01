# ⚙️ PANDUAN BACKEND — Fatin Asyifa Nurrizky JenPutri

> **Peran:** Programmer Bidang Backend
> **Proyek:** Sistem Manajemen Aset BULOG
> **Framework:** Laravel (PHP) | **Database:** MySQL / PostgreSQL

---

## 🚀 CARA MENGGUNAKAN PANDUAN INI

Gunakan prompt berikut saat membuka VS Code dan memulai sesi kerja:

```
Disini saya mendapati tugas sebagai Backend (Fatin Asyifa Nurrizky JenPutri),
maka dari itu tolong analisa terlebih dahulu agar saya dapat melihat apa saja
yang belum dan apa saja yang sudah dikerjakan dalam proyek ini. Dalam pengerjaan
diusahakan membuat pull request dan branch dengan nama branch-nya disesuaikan
dengan apa yang dikerjakan, dan jika pekerjaan sudah selesai buatkan file yang
berisi laporan yang telah dikerjakan sebagai bentuk laporan kepada PM.
```

---

## 🎯 Tanggung Jawab Utama

- Membangun arsitektur server & logika bisnis inti menggunakan **Laravel**
- Mentranslasi ERD dari System Analyst menjadi **Database Migrations**
- Menyediakan **RESTful API Endpoint** yang aman dan cepat
- Mengintegrasikan library **QR Code Generator** (PHP)
- Mengimplementasikan **Geotagging** saat scan QR Code
- Membangun **Report Engine** (ekspor PDF & Excel)
- Menerapkan keamanan sistem (HTTPS, bcrypt, CSRF, XSS, SQL Injection protection)

---

## 📋 CHECKLIST TUGAS LENGKAP

### 🔷 MINGGU 1 — Setup & Fondasi

#### Environment & Project Setup
- [ ] Install & konfigurasi project base Laravel
- [ ] Setup `.env` (koneksi database, APP_KEY, APP_URL)
- [ ] Konfigurasi database MySQL/PostgreSQL
- [ ] Install dependency: `composer install`

#### Database Migrations (berdasarkan ERD dari Caryksha)
- [ ] Migration tabel `users` (id, nama, email, password, role, timestamps)
- [ ] Migration tabel `assets` (id, kode_aset, jenis, merek, model, nomor_seri, kondisi, lokasi, qr_code_path, pic_id, timestamps)
- [ ] Migration tabel `pics` (id, nama, jabatan, email, telepon, timestamps)
- [ ] Migration tabel `asset_histories` / audit trail (id, asset_id, user_id, field_changed, old_value, new_value, timestamps)
- [ ] Migration tabel `pic_histories` (id, asset_id, pic_lama_id, pic_baru_id, alasan, timestamps)
- [ ] Jalankan `php artisan migrate`
- [ ] Buat Seeder untuk data dummy awal (opsional tapi sangat membantu testing)

#### Modul Autentikasi (FR-01, FR-02, FR-03, FR-04 | NFR-07, NFR-08)
- [ ] Implementasi **Register** dengan validasi role (admin_it, user_pic, manajemen)
- [ ] Implementasi **Login** dengan hashing bcrypt
- [ ] Implementasi **Logout** (invalidate session/token)
- [ ] Implementasi **Role-Based Access Control (RBAC)** via middleware Laravel
- [ ] Endpoint: `POST /api/auth/register`
- [ ] Endpoint: `POST /api/auth/login`
- [ ] Endpoint: `POST /api/auth/logout`
- [ ] **Branch:** `feature/auth-login`

---

### 🔷 MINGGU 1–2 — API Inti

#### CRUD Manajemen Aset (FR-05, FR-06, FR-07, FR-08)
- [ ] Endpoint: `GET /api/assets` — daftar semua aset (dengan filter kondisi, jenis, lokasi)
- [ ] Endpoint: `GET /api/assets/{id}` — detail satu aset
- [ ] Endpoint: `POST /api/assets` — tambah aset baru
- [ ] Endpoint: `PUT /api/assets/{id}` — edit aset
- [ ] Endpoint: `DELETE /api/assets/{id}` — hapus aset (soft delete, hanya Admin IT)
- [ ] Auto-record **Audit Trail** setiap kali data aset berubah (FR-08)
- [ ] **Branch:** `feature/asset-crud`

#### CRUD Manajemen PIC (FR-09, FR-10, FR-11, FR-12 | BR-01, BR-03)
- [ ] Endpoint: `GET /api/pics` — daftar PIC
- [ ] Endpoint: `POST /api/pics` — tambah PIC baru
- [ ] Endpoint: `PUT /api/pics/{id}` — edit data PIC
- [ ] Endpoint: `DELETE /api/pics/{id}` — hapus PIC
- [ ] Endpoint: `POST /api/assets/{id}/assign-pic` — tetapkan PIC ke aset
- [ ] Validasi: **satu aset hanya boleh punya satu PIC aktif** (BR-01)
- [ ] Validasi: **aset RUSAK BERAT tidak boleh ganti PIC** (BR-03)
- [ ] Record riwayat pergantian PIC (FR-12)
- [ ] **Branch:** `feature/pic-management`

#### Sistem QR Code (FR-13, FR-14 | NFR-03)
- [ ] Install library PHP QR Code (contoh: `bacon/bacon-qr-code` atau `simplesoftwareio/simple-qrcode`)
- [ ] Auto-generate QR Code unik setiap kali aset baru dibuat
- [ ] Simpan file QR Code (PNG) di storage Laravel
- [ ] Endpoint: `GET /api/assets/{id}/qrcode` — tampilkan/download QR Code aset
- [ ] Endpoint untuk cetak label QR Code
- [ ] **Branch:** `feature/qr-generator`

---

### 🔷 MINGGU 2 — Fitur Lanjutan

#### Geotagging saat Scan QR Code (FR-15, FR-16, FR-20)
- [ ] Endpoint: `POST /api/assets/{id}/scan` — terima data scan dari frontend
  - Input: `{ asset_id, latitude, longitude, scanned_by, scanned_at }`
  - Output: detail aset + konfirmasi lokasi tersimpan
- [ ] Simpan koordinat lokasi ke tabel asset histories / kolom lokasi terakhir
- [ ] Endpoint: `GET /api/assets/{id}/location` — lokasi terakhir aset
- [ ] **Branch:** `feature/qr-geotagging`

#### Notifikasi Otomatis (FR-22, FR-23, FR-24)
- [ ] Setup Laravel Mail (atau log sistem internal sebagai alternatif)
- [ ] Notifikasi ke PIC: pengingat pemeriksaan berkala
- [ ] Notifikasi ke Admin IT: jika ada laporan kerusakan aset
- [ ] Gunakan Laravel Scheduler (`php artisan schedule:run`) untuk notifikasi terjadwal
- [ ] **Branch:** `feature/notification-system`

#### Report Engine — Ekspor PDF & Excel (FR-25, FR-26, FR-27)
- [ ] Install package: `barryvdh/laravel-dompdf` (PDF) dan `maatwebsite/excel` (Excel)
- [ ] Endpoint: `GET /api/reports/assets?filter=...&format=pdf` — ekspor PDF
- [ ] Endpoint: `GET /api/reports/assets?filter=...&format=excel` — ekspor Excel
- [ ] Support filter: kondisi, lokasi, jenis aset, PIC, rentang tanggal
- [ ] **Branch:** `feature/report-export`

#### Backup Database (FR-28, FR-29 | NFR-05)
- [ ] Implementasi backup database manual via artisan command
- [ ] Setup backup terjadwal (minimal sekali sehari) menggunakan Laravel Scheduler
- [ ] Endpoint/command verifikasi integritas data
- [ ] **Branch:** `feature/backup-system`

#### Keamanan Sistem (NFR-06, NFR-07, NFR-08, NFR-09)
- [ ] Pastikan semua endpoint dilindungi middleware autentikasi
- [ ] Implementasi CSRF protection (bawaan Laravel)
- [ ] Implementasi proteksi XSS (sanitasi input)
- [ ] Pastikan tidak ada raw SQL query (gunakan Eloquent ORM)
- [ ] Konfigurasi CORS untuk akses dari frontend
- [ ] **Branch:** `feature/security-hardening`

---

### 🔷 MINGGU 3 — Optimasi & Finalisasi

#### Performa & Code Quality (NFR-01, NFR-02)
- [ ] Pastikan semua response API **< 2 detik**
- [ ] Tambahkan database indexing pada kolom yang sering di-query
- [ ] Code refactoring: pastikan mengikuti struktur MVC Laravel yang bersih
- [ ] Uji endpoint dengan minimal 50 concurrent users (gunakan tools seperti Apache Bench atau Postman Collection Runner)

#### Integrasi & Bug Fixing
- [ ] Koordinasi dengan Khansa untuk penyelesaian semua integrasi API
- [ ] Fix bug CORS, parsing JSON, atau parameter yang salah
- [ ] Uji semua endpoint sekali lagi setelah integrasi penuh
- [ ] Finalisasi: **Build Final Backend**

---

## 🗂️ Struktur Folder Laravel yang Disarankan

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── AssetController.php
│   │   ├── PicController.php
│   │   ├── QrCodeController.php
│   │   ├── ReportController.php
│   │   └── NotificationController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php
│   └── Requests/
│       ├── StoreAssetRequest.php
│       └── StorePicRequest.php
├── Models/
│   ├── User.php
│   ├── Asset.php
│   ├── Pic.php
│   ├── AssetHistory.php
│   └── PicHistory.php
├── Services/
│   ├── QrCodeService.php
│   └── NotificationService.php
database/
├── migrations/
├── seeders/
routes/
├── api.php           ← semua route API di sini
storage/
└── app/qrcodes/      ← file PNG QR Code tersimpan di sini
```

---

## 🔌 Daftar Endpoint API (Referensi Cepat)

| Method | Endpoint | Deskripsi | Auth |
|--------|----------|-----------|------|
| POST | `/api/auth/register` | Registrasi akun baru | Public |
| POST | `/api/auth/login` | Login & dapatkan token | Public |
| POST | `/api/auth/logout` | Logout | Auth |
| GET | `/api/assets` | List semua aset | Auth |
| POST | `/api/assets` | Tambah aset baru | Admin IT |
| GET | `/api/assets/{id}` | Detail aset | Auth |
| PUT | `/api/assets/{id}` | Edit aset | Admin IT |
| DELETE | `/api/assets/{id}` | Hapus aset | Admin IT |
| GET | `/api/assets/{id}/qrcode` | Ambil QR Code | Auth |
| POST | `/api/assets/{id}/scan` | Catat scan + geotagging | Auth |
| GET | `/api/pics` | List PIC | Auth |
| POST | `/api/pics` | Tambah PIC | Admin IT |
| PUT | `/api/pics/{id}` | Edit PIC | Admin IT |
| DELETE | `/api/pics/{id}` | Hapus PIC | Admin IT |
| POST | `/api/assets/{id}/assign-pic` | Assign PIC ke aset | Admin IT |
| GET | `/api/reports/assets` | Generate laporan | Admin IT / Manajemen |
| GET | `/api/dashboard/summary` | Data ringkasan dashboard | Auth |

### Dashboard Summary
- Endpoint: `GET /api/dashboard/summary`
- Deskripsi: Menyediakan metrik ringkasan dashboard berupa total aset, total laptop, total printer, total PIC, dan distribusi kondisi aset.
- Digunakan untuk menampilkan Dashboard Utama yang memuat counter cards dan grafik kondisi aset.

---

## Prioritas perbaikan

### Prioritas 1: Fix lingkungan testing
- [x] Perbaiki environment agar php artisan test bisa berjalan.
- [x] Fokus utama: driver database/testing yang missing.
- [x] Tanpa ini, semua penilaian kualitas backend belum valid.

### Prioritas 2: Jalankan ulang test dan perbaiki fitur yang gagal
- [x] Setelah environment siap, jalankan test lagi.
- [x] Perbaiki endpoint yang sebenarnya gagal, bukan hanya yang terlihat di kode.

### Prioritas 3: Bersihkan kode controller
- [x] Perbaiki bagian yang tidak diperlukan atau unreachable di AssetController.php.
- [x] Pastikan struktur response konsisten.

### Prioritas 4: Verifikasi endpoint secara manual
- [x] Uji endpoint auth, asset, PIC, scan, report, dashboard, backup.
- [x] Pastikan response format sesuai kebutuhan frontend.

### Prioritas 5: Validasi keamanan dan akses
- [x] Pastikan role admin/user/manajemen bekerja sesuai aturan.
- [x] Cek error untuk token invalid, akses tidak sah, dan input invalid.

### Prioritas 6: Finalisasi dokumentasi
- [x] Pastikan laporan kerja dan dokumentasi API benar-benar mencerminkan hasil yang terverifikasi, bukan hanya implementasi yang ada.

## Kesimpulan akhir

Tugas backend Fatin sudah sangat maju dan banyak bagian penting sudah dikerjakan, tetapi belum bisa disebut selesai 100% karena:
- implementasi sudah ada,
- tetapi verifikasi dan pengujian belum sepenuhnya lolos,
- dan masih ada perbaikan lingkungan serta cleanup kode yang perlu diselesaikan.

> Jadi, statusnya bisa disebut: "sudah selesai secara implementasi, tetapi belum selesai 100% secara validasi dan finalisasi".

---

## ⚠️ Aturan Bisnis yang Wajib Divalidasi di Backend

```php
// BR-03: Aset RUSAK BERAT tidak boleh ganti PIC
if ($asset->kondisi === 'RUSAK_BERAT') {
    return response()->json([
        'message' => 'Aset dengan kondisi RUSAK BERAT tidak dapat dialihkan PIC-nya.'
    ], 422);
}

// BR-01: Satu aset hanya boleh satu PIC aktif
// Pastikan ketika assign PIC baru, PIC lama di-non-aktifkan dulu

// BR-04: Penghapusan hanya oleh Admin IT & wajib dicatat audit trail
// Implementasikan lewat middleware role + observer/event model
```

---

## 📝 Format Laporan Akhir (Setelah Selesai)

Buat file `LAPORAN_FATIN_BACKEND.md` di root project dengan isi:

```markdown
# Laporan Pengerjaan Backend — Fatin Asyifa Nurrizky JenPutri

## Ringkasan
- Periode pengerjaan: [tanggal mulai] s.d. [tanggal selesai]
- Total PR yang dibuat: [jumlah]

## Fitur yang Diselesaikan
| Fitur | Branch | Status PR |
|-------|--------|-----------|
| Auth (Login/Register) | feature/auth-login | Merged |
| CRUD Aset | feature/asset-crud | Merged |
| ... | ... | ... |

## Kendala & Solusi
- [Kendala 1]: [Solusi yang diterapkan]

## Catatan untuk PM
- [Hal penting yang perlu diketahui PM / hal yang masih perlu follow-up]
```

---

*Panduan ini mengacu pada SRS v1.0 (FR-01 s.d. FR-29, NFR-01 s.d. NFR-13) dan Laporan Distribusi Tugas Tim UAS BULOG.*
