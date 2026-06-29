# Laporan Pengerjaan Backend — Fatin Asyifa Nurrizky JenPutri

## Ringkasan
- Periode pengerjaan: 22 Juni 2026 s.d. 29 Juni 2026
- Branch utama: `feature/auth-sanctum`, `feature/asset-crud`, dan `feature/backup-system`
- Fokus: Fondasi Keamanan, Autentikasi, Penguatan CRUD Aset, serta Backup Database
- Status: Selesai untuk langkah autentikasi, CRUD aset, dan fitur backup database

## Fitur yang Diselesaikan
| Fitur | File / Area | Keterangan |
|---|---|---|
| Auth register | `app/Http/Controllers/AuthController.php` | Endpoint `POST /api/auth/register` dengan validasi role, hashing password, dan token Sanctum |
| Auth login | `app/Http/Controllers/AuthController.php` | Endpoint `POST /api/auth/login` dengan validasi email/password dan token issuer |
| Auth logout | `app/Http/Controllers/AuthController.php` | Endpoint `POST /api/auth/logout` untuk menghapus token saat logout |
| Role-based access | `app/Http/Middleware/RoleMiddleware.php` | Middleware role guard untuk membatasi aksi hanya ke `admin_it` |
| User role field | `database/migrations/2026_06_22_000000_add_role_to_users_table.php` | Menambahkan kolom `role` ke tabel `users` |
| User model update | `app/Models/User.php` | Menambahkan `role` ke fillable dan helper role check |
| API route auth | `routes/api.php` | Menambahkan route register/login/logout dan mengamankan route assets dengan Sanctum |
| Seeder default admin | `database/seeders/DatabaseSeeder.php` | Membuat user `admin@bulog.local` dengan role `admin_it` |
| Asset soft delete | `app/Models/Asset.php` | Menambahkan `SoftDeletes` untuk penghapusan lembut |
| Asset audit trail model | `app/Models/AssetHistory.php` | Model untuk merekam perubahan asset |
| Asset audit/migration | `database/migrations/2026_06_22_010000_add_soft_deletes_and_asset_history.php` | Menambahkan soft deletes ke `assets` dan tabel `asset_histories` |
| Asset controller enhancements | `app/Http/Controllers/AssetController.php` | Filter assets, pencatatan audit trail untuk create/update/delete |
| PIC list | `app/Http/Controllers/PicController.php` | Endpoint `GET /api/pics` untuk daftar PIC |
| PIC CRUD | `app/Http/Controllers/PicController.php` | Endpoint `POST /api/pics`, `PUT /api/pics/{pic}`, `DELETE /api/pics/{pic}` |
| PIC assignment | `app/Http/Controllers/PicController.php` | Endpoint `POST /api/assets/{asset}/assign-pic` dengan validasi BR-03 |
| PIC migrations | `database/migrations/2026_06_22_020000_create_pics_table.php` and `2026_06_22_030000_create_pic_histories_table.php` | Buat tabel PIC dan riwayat pergantian PIC |
v| QR code generator | `app/Http/Controllers/AssetController.php`, `database/migrations/2026_06_22_040000_add_qr_code_path_to_assets_table.php` | Menyimpan path QR SVG di asset, membuat file QR SVG saat asset dibuat, dan endpoint download `GET /api/assets/{asset}/qrcode` |
| QR geotagging | `app/Http/Controllers/AssetController.php`, `routes/api.php` | Endpoint `POST /api/assets/{asset}/scan` untuk scan QR + simpan lokasi, `GET /api/assets/{asset}/location` untuk lokasi terakhir aset |
| Report export | `app/Http/Controllers/ReportController.php`, `app/Exports/AssetsExport.php`, `resources/views/reports/assets.blade.php` | Ekspor aset ke format PDF dan Excel via `GET /api/reports/assets?format=pdf|excel` |
| Backup database | `app/Services/DatabaseBackupService.php`, `app/Console/Commands/CreateDatabaseBackup.php`, `app/Console/Commands/VerifyDatabaseIntegrity.php`, `app/Http/Controllers/BackupController.php`, `config/backup.php` | Membuat backup manual, jadwal harian otomatis via scheduler, endpoint backup dan verifikasi integritas database |
| Security hardening | `app/Http/Middleware/SanitizeInputMiddleware.php`, `app/Http/Middleware/EnsureJsonApiRequests.php`, `app/Http/Kernel.php`, `routes/api.php`, `config/cors.php`, `app/Http/Middleware/CsrfProtectionMiddleware.php`, `app/Http/Middleware/SecurityHeadersMiddleware.php`, `tests/Feature/SecurityHardeningTest.php` | Menambahkan sanitasi input, middleware autentikasi pada route API, enforcement Content-Type JSON untuk mutasi, konfigurasi CORS untuk frontend, perlindungan CSRF, header keamanan, rate limiting, dan pengujian keamanan |

## Implementasi Keamanan
- Semua route sensitif sekarang berada di dalam middleware `auth:sanctum`
- Aksi mutasi asset (`store`, `update`, `destroy`) dibatasi untuk role `admin_it`
- Role valid pada registrasi hanya menerima `admin_it`, `user_pic`, atau `manajemen`
- Token akses dibuat menggunakan Laravel Sanctum di endpoint login/register
- Middleware CSRF dan header keamanan diterapkan untuk melindungi request mutasi dan mengurangi risiko serangan dari browser
- Rate limiting diterapkan pada endpoint auth untuk mengurangi abuse dan brute force

## Catatan Tambahan
- Saat ini fitur CRUD aset masih menggunakan controller `AssetController` dan hanya dapat dimodifikasi oleh `admin_it`
- Fitur lanjutannya seperti PIC management, QR code generator, QR geotagging, Report Engine (PDF/Excel export), backup database, dan security hardening sudah dikerjakan
- Untuk pengujian awal, gunakan `php artisan route:list --path=api` dan migrasi + seeder tersedia untuk memulai data admin
- Fitur backup dapat diuji lewat `php artisan app:create-database-backup` dan `php artisan app:verify-database-integrity`
- Fitur keamanan dapat diuji lewat `php artisan test --filter=SecurityHardeningTest`

## 📚 Dokumentasi API Lengkap

### Authentication Endpoints

#### 1. Register User
**Endpoint:** `POST /api/auth/register`  
**Auth:** Public  
**Description:** Registrasi user baru dengan role yang valid

**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "user_pic"
}
```

**Valid Roles:** `admin_it`, `user_pic`, `manajemen`

**Response (201 Created):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user_pic",
    "created_at": "2026-06-23T10:00:00Z"
  },
  "token": "1|8eeSMLKN6oXjMcwen9zx9gVh72xLhR1bKMFnPsX44e32c21"
}
```

#### 2. Login
**Endpoint:** `POST /api/auth/login`  
**Auth:** Public  
**Description:** Login dan dapatkan bearer token

**Request Body:**
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

**Response (200 OK):**
```json
{
  "user": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "role": "user_pic"
  },
  "token": "1|8eeSMLKN6oXjMcwen9zx9gVh72xLhR1bKMFnPsX44e32c21"
}
```

#### 3. Logout
**Endpoint:** `POST /api/auth/logout`  
**Auth:** Required (Bearer Token)  
**Description:** Logout dan hapus token saat ini

**Response (200 OK):**
```json
{
  "message": "Logout berhasil."
}
```

### Asset Endpoints

#### 4. List All Assets
**Endpoint:** `GET /api/assets`  
**Auth:** Required  
**Description:** Daftar semua aset dengan filter opsional

**Query Parameters:**
- `kondisi` (optional): Filter berdasarkan kondisi (e.g., `baik`, `rusak`)
- `jenis` (optional): Filter berdasarkan jenis (e.g., `laptop`, `printer`)
- `lokasi` (optional): Filter berdasarkan lokasi (pencarian teks)

**Example:** `/api/assets?jenis=laptop&kondisi=baik&lokasi=Gudang`

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "kode_aset": "LPT-001",
    "nama_aset": "MacBook Pro",
    "merk_type": "Apple M2",
    "serial_number": "C02XG0KDJGH5",
    "lokasi": "Gudang",
    "koordinat_lat": -6.200000,
    "koordinat_lng": 106.816666,
    "kondisi": "baik",
    "tgl_perolehan": "2023-01-15",
    "harga": 25000000,
    "keterangan": "Laptop kantor",
    "jenis": "laptop",
    "qr_code_path": "qrcodes/asset-1-1782115939.svg",
    "pic_id": null,
    "created_at": "2026-06-22T08:00:00Z",
    "updated_at": "2026-06-23T10:30:00Z"
  }
]
```

#### 5. Create Asset
**Endpoint:** `POST /api/assets`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Buat aset baru dengan auto-generate QR Code

**Request Body:**
```json
{
  "kode_aset": "LPT-002",
  "nama_aset": "Dell XPS 15",
  "merk_type": "Dell",
  "serial_number": "DEL123456",
  "lokasi": "Kantor IT",
  "koordinat_lat": -6.210000,
  "koordinat_lng": 106.820000,
  "kondisi": "baik",
  "tgl_perolehan": "2023-06-01",
  "harga": 20000000,
  "keterangan": "Workstation developer",
  "jenis": "laptop"
}
```

**Response (201 Created):**
```json
{
  "id": 2,
  "kode_aset": "LPT-002",
  "nama_aset": "Dell XPS 15",
  "merk_type": "Dell",
  "serial_number": "DEL123456",
  "lokasi": "Kantor IT",
  "koordinat_lat": -6.210000,
  "koordinat_lng": 106.820000,
  "kondisi": "baik",
  "tgl_perolehan": "2023-06-01",
  "harga": 20000000,
  "keterangan": "Workstation developer",
  "jenis": "laptop",
  "qr_code_path": "qrcodes/asset-2-1782116421.svg",
  "pic_id": null,
  "created_at": "2026-06-23T10:00:00Z"
}
```

#### 6. Get Asset Detail
**Endpoint:** `GET /api/assets/{asset_id}`  
**Auth:** Required  
**Description:** Ambil detail aset spesifik

**Response (200 OK):**
```json
{
  "id": 1,
  "kode_aset": "LPT-001",
  "nama_aset": "MacBook Pro",
  "merk_type": "Apple M2",
  "serial_number": "C02XG0KDJGH5",
  "lokasi": "Gudang",
  "koordinat_lat": -6.200000,
  "koordinat_lng": 106.816666,
  "kondisi": "baik",
  "tgl_perolehan": "2023-01-15",
  "harga": 25000000,
  "keterangan": "Laptop kantor",
  "jenis": "laptop",
  "qr_code_path": "qrcodes/asset-1-1782115939.svg",
  "pic_id": null,
  "created_at": "2026-06-22T08:00:00Z",
  "updated_at": "2026-06-23T10:30:00Z"
}
```

#### 7. Update Asset
**Endpoint:** `PUT /api/assets/{asset_id}`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Update informasi aset dan catat ke history

**Request Body:** (field apa saja yang ingin diubah)
```json
{
  "kondisi": "rusak",
  "lokasi": "Bengkel Perbaikan"
}
```

**Response (200 OK):** (asset yang sudah diupdate)

#### 8. Delete Asset
**Endpoint:** `DELETE /api/assets/{asset_id}`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Soft delete asset (penghapusan lembut, tidak merusak data riwayat)

**Response (200 OK):**
```json
{
  "message": "Asset deleted successfully."
}
```

#### 9. Download QR Code
**Endpoint:** `GET /api/assets/{asset_id}/qrcode`  
**Auth:** Required  
**Description:** Unduh file QR Code (SVG) untuk aset

**Response (200 OK):** Binary file SVG
- Content-Type: `image/svg+xml`
- Filename: `asset-{id}-{timestamp}.svg`

#### 10. Scan QR & Record Location
**Endpoint:** `POST /api/assets/{asset_id}/scan`  
**Auth:** Required  
**Description:** Catat scan QR Code dengan geotagging (update koordinat lokasi terakhir)

**Request Body:**
```json
{
  "latitude": -6.250000,
  "longitude": 106.850000,
  "scanned_at": "2026-06-23T10:30:00",
  "scanned_by": 1
}
```

**Parameters:**
- `latitude` (required, numeric): Latitude koordinat dari HTML5 Geolocation API
- `longitude` (required, numeric): Longitude koordinat dari HTML5 Geolocation API
- `scanned_at` (optional, date): Timestamp scan (default: now())
- `scanned_by` (optional, integer): User ID yang melakukan scan (default: current user)

**Response (200 OK):**
```json
{
  "message": "Scan berhasil, lokasi aset diperbarui.",
  "asset": {
    "id": 1,
    "kode_aset": "LPT-001",
    "nama_aset": "MacBook Pro",
    "lokasi": "Gudang",
    "koordinat_lat": -6.250000,
    "koordinat_lng": 106.850000,
    "updated_at": "2026-06-23T10:30:00Z"
  },
  "scanned_at": "2026-06-23T10:30:00"
}
```

#### 11. Get Asset Location
**Endpoint:** `GET /api/assets/{asset_id}/location`  
**Auth:** Required  
**Description:** Ambil informasi lokasi terakhir aset dan riwayat scan terbaru

**Response (200 OK):**
```json
{
  "asset_id": 1,
  "lokasi": "Gudang",
  "latitude": -6.250000,
  "longitude": 106.850000,
  "last_scan": {
    "latitude": -6.250000,
    "longitude": 106.850000,
    "scanned_at": "2026-06-23T10:30:00"
  }
}
```

### Notification Endpoints

#### 12. List Notifications
**Endpoint:** `GET /api/notifications`  
**Auth:** Required  
**Description:** Ambil daftar notifikasi untuk user saat ini atau role terkait

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "user_id": null,
    "role": "user_pic",
    "title": "Pengingat Pemeriksaan Aset",
    "message": "Pengingat harian: Anda memiliki 3 aset untuk pemeriksaan.",
    "data": {
      "asset_ids": [1, 2, 3]
    },
    "is_read": false,
    "created_at": "2026-06-23T11:00:00Z"
  }
]
```

#### 13. Mark Notification Read
**Endpoint:** `PATCH /api/notifications/{notification_id}/read`  
**Auth:** Required  
**Description:** Tandai notifikasi sebagai sudah dibaca

**Response (200 OK):**
```json
{
  "id": 1,
  "user_id": null,
  "role": "user_pic",
  "title": "Pengingat Pemeriksaan Aset",
  "message": "Pengingat harian: Anda memiliki 3 aset untuk pemeriksaan.",
  "data": {
    "asset_ids": [1, 2, 3]
  },
  "is_read": true,
  "created_at": "2026-06-23T11:00:00Z"
}
```

### Scheduled Notifications
- `app:send-inspection-reminders` — command scheduler daily untuk mengirim pengingat pemeriksaan ke PIC
- Notifikasi internal juga direkam ke tabel `notifications`

### PIC Endpoints

#### 12. List All PIC
**Endpoint:** `GET /api/pics`  
**Auth:** Required  
**Description:** Daftar semua Penanggung Jawab Aset (PIC)

**Response (200 OK):**
```json
[
  {
    "id": 1,
    "nama": "Budi Santoso",
    "email": "budi@example.com",
    "departemen": "IT",
    "no_telp": "08123456789",
    "created_at": "2026-06-22T08:00:00Z"
  }
]
```

#### 13. Create PIC
**Endpoint:** `POST /api/pics`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Buat PIC baru

**Request Body:**
```json
{
  "nama": "Siti Fatimah",
  "email": "siti@example.com",
  "departemen": "Operasional",
  "no_telp": "08129876543"
}
```

#### 14. Update PIC
**Endpoint:** `PUT /api/pics/{pic_id}`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Update informasi PIC

#### 15. Delete PIC
**Endpoint:** `DELETE /api/pics/{pic_id}`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Hapus PIC

#### 16. Assign PIC to Asset
**Endpoint:** `POST /api/assets/{asset_id}/assign-pic`  
**Auth:** Required  
**Role:** `admin_it`  
**Description:** Assign PIC ke aset (dengan validasi BR-03: aset rusak berat tidak boleh ganti PIC)

**Request Body:**
```json
{
  "pic_id": 1
}
```

**Response (200 OK):**
```json
{
  "message": "PIC berhasil diassign ke aset.",
  "asset": {
    "id": 1,
    "kode_aset": "LPT-001",
    "pic_id": 1
  }
}
```

## Testing

### Run Tests
```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/AssetScanTest.php

# Run with verbose output
php artisan test --verbose

# Generate coverage report
php artisan test --coverage
```

### Feature Tests Included
- **AssetScanTest**: 10+ test cases untuk scan & location endpoints
- **NotificationTest**: 3 test cases untuk notifikasi endpoint dan scheduler
- **ReportExportTest**: 3 test cases untuk laporan preview, Excel download, dan PDF download

## Rekomendasi Tindak Lanjut
1. Jalankan `php artisan migrate` lalu `php artisan db:seed`
2. Uji semua endpoint menggunakan Postman atau Insomnia dengan collection yang sudah disediakan
3. Jalankan `php artisan test` untuk memastikan semua feature test lolos
4. Lanjutan berikutnya: integrasi UI laporannya dan validasi CORS untuk download file
