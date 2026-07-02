# 📋 DOKUMENTASI AKUN LOGIN SISTEM ASET BULOG

## 🔐 Akun Default Seeder

Sistem Manajemen Aset BULOG memiliki 3 role dengan hak akses berbeda. Berikut adalah akun default yang tersedia setelah menjalankan seeder:

---

## **1. ADMIN IT (Role: admin_it)**

| Field | Nilai |
|-------|-------|
| **Email** | `admin@bulog.co.id` |
| **Password** | `password` |
| **Nama** | Admin IT |
| **Telepon** | 081200000001 |

### Hak Akses:
✅ **Full CRUD Access**
- ✅ Lihat daftar aset (`GET /api/assets`)
- ✅ Lihat detail aset (`GET /api/assets/{id}`)
- ✅ **Tambah aset baru** (`POST /api/assets`)
- ✅ **Edit aset** (`PUT /api/assets/{id}`)
- ✅ **Hapus aset** (`DELETE /api/assets/{id}`)
- ✅ Scan QR aset (`POST /api/assets/{id}/scan`)
- ✅ Lihat lokasi aset (`GET /api/assets/{id}/location`)
- ✅ Download QR Code (`GET /api/assets/{id}/qrcode`)

✅ **PIC Management**
- ✅ Lihat daftar PIC (`GET /api/pics`)
- ✅ **Tambah PIC** (`POST /api/pics`)
- ✅ **Edit PIC** (`PUT /api/pics/{id}`)
- ✅ **Hapus PIC** (`DELETE /api/pics/{id}`)
- ✅ **Assign PIC ke aset** (`POST /api/assets/{id}/assign-pic`)

✅ **Laporan & Export**
- ✅ Preview laporan (`GET /api/reports/assets`)
- ✅ **Export PDF** (`GET /api/reports/assets/download`)
- ✅ **Export Excel** (`GET /api/reports/assets/export`)

✅ **Backup System**
- ✅ **Buat backup manual** (`POST /api/backups`)
- ✅ **Lihat daftar backup** (`GET /api/backups`)
- ✅ **Verifikasi integritas** (`GET /api/backups/verify`)

✅ **Dashboard**
- ✅ Lihat dashboard summary (`GET /api/dashboard/summary`)

---

## **2. PIC (Person In Charge) - Role: pic**

| Email | Nama | Telepon |
|-------|------|---------|
| `andi@bulog.co.id` | Andi Saputra | 081200000002 |
| `sari@bulog.co.id` | Sari Wulandari | 081200000003 |
| `rudi@bulog.co.id` | Rudi Hartono | 081200000004 |

### Password untuk semua akun PIC:
`password`

### Hak Akses:
✅ **Read-Only + Scan**
- ✅ Lihat daftar aset (`GET /api/assets`)
- ✅ Lihat detail aset (`GET /api/assets/{id}`)
- ✅ **Scan QR aset** (`POST /api/assets/{id}/scan`) ⭐ Utama
- ✅ Lihat lokasi aset (`GET /api/assets/{id}/location`)
- ✅ Download QR Code (`GET /api/assets/{id}/qrcode`)
- ✅ Lihat daftar PIC (`GET /api/pics`)
- ✅ Lihat dashboard summary (`GET /api/dashboard/summary`)

❌ **Tidak Bisa**
- ❌ Tambah/edit/hapus aset
- ❌ Manage PIC
- ❌ Export laporan
- ❌ Backup database

---

## **3. MANAJEMEN (Role: manajemen)** ⭐ BARU

| Field | Nilai |
|-------|-------|
| **Email** | `manager@bulog.co.id` |
| **Password** | `password` |
| **Nama** | Direktur Operasional |
| **Telepon** | 081200000005 |

### Hak Akses:
✅ **Read-Only + Report**
- ✅ Lihat daftar aset (`GET /api/assets`)
- ✅ Lihat detail aset (`GET /api/assets/{id}`)
- ✅ Download QR Code (`GET /api/assets/{id}/qrcode`)
- ✅ Lihat daftar PIC (`GET /api/pics`)
- ✅ Lihat dashboard summary (`GET /api/dashboard/summary`)
- ✅ Preview laporan aset (`GET /api/reports/assets`) ⭐ Utama
- ✅ **Export PDF laporan** (`GET /api/reports/assets/download`) ⭐ Utama
- ✅ **Export Excel laporan** (`GET /api/reports/assets/export`) ⭐ Utama

❌ **Tidak Bisa**
- ❌ Tambah/edit/hapus aset
- ❌ Manage PIC (tambah/edit/hapus)
- ❌ Assign PIC ke aset
- ❌ Scan QR aset
- ❌ Backup database

---

## 📊 TABEL PERBANDINGAN HAK AKSES

| Fitur | Admin IT | PIC | Manajemen |
|-------|----------|-----|-----------|
| **Lihat Aset** | ✅ | ✅ | ✅ |
| **Tambah Aset** | ✅ | ❌ | ❌ |
| **Edit Aset** | ✅ | ❌ | ❌ |
| **Hapus Aset** | ✅ | ❌ | ❌ |
| **Scan QR** | ✅ | ✅ | ❌ |
| **Manage PIC** | ✅ | ❌ | ❌ |
| **Preview Laporan** | ✅ | ❌ | ✅ |
| **Export PDF/Excel** | ✅ | ❌ | ✅ |
| **Backup DB** | ✅ | ❌ | ❌ |
| **Dashboard** | ✅ | ✅ | ✅ |

---

## 🔧 MENJALANKAN SEEDER

Untuk membuat akun-akun di atas, jalankan perintah:

```bash
php artisan migrate:fresh --seed
```

Atau hanya menjalankan seeder tanpa fresh migration:

```bash
php artisan db:seed
```

---

## 🧪 TESTING AKUN LOGIN

### Test dengan cURL:

#### Login Admin IT
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@bulog.co.id",
    "password": "password"
  }'
```

#### Login PIC
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "andi@bulog.co.id",
    "password": "password"
  }'
```

#### Login Manajemen (NEW)
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "manager@bulog.co.id",
    "password": "password"
  }'
```

Response akan berisi token:
```json
{
  "user": {
    "id": 1,
    "name": "Admin IT",
    "email": "admin@bulog.co.id",
    "role": "admin_it",
    "createdAt": "2026-07-02T...",
    "updatedAt": "2026-07-02T..."
  },
  "token": "1|...",
  "auth_token": "1|...",
  "token_type": "Bearer"
}
```

Gunakan token untuk akses endpoint protected:
```bash
curl -X GET http://localhost:8000/api/assets \
  -H "Authorization: Bearer {token}"
```

---

## ✨ FITUR SPESIFIK PER ROLE

### Admin IT - Workflow Utama:
1. Tambah aset baru
2. Assign PIC ke aset
3. Monitor kondisi aset
4. Export laporan untuk keperluan audit
5. Backup database secara berkala

### PIC - Workflow Utama:
1. Lihat aset yang ter-assign
2. Scan QR aset saat ditemui
3. Geotagging otomatis (GPS capture)
4. Lihat dashboard untuk ringkasan
5. Report kondisi aset

### Manajemen - Workflow Utama:
1. ✅ Lihat ringkasan aset di dashboard
2. ✅ Preview daftar aset dengan filter
3. ✅ Export laporan ke PDF untuk meeting/laporan ke atasan
4. ✅ Export data ke Excel untuk analisa lebih lanjut
5. ✅ Filter aset berdasarkan kondisi, lokasi, jenis

---

## 📝 CATATAN PENTING

- **Password Default:** Semua akun menggunakan password `password` untuk development
- **Production:** Ubah password sebelum go-live
- **Role Immutable:** Role tidak bisa berubah sendiri, hanya Admin IT yang bisa (via database manual)
- **Token Expiry:** Token tidak expire (development). Di production, set expiry time di `.env`
- **Permission Middleware:** Diimplementasikan via `role:admin_it,manajemen` middleware di routes

---

## 🔒 SECURITY NOTES

- Semua endpoint dilindungi middleware `auth:sanctum`
- Role-based access control via `role:role1,role2` middleware
- Password di-hash dengan bcrypt
- CSRF & XSS protection aktif
- Rate limiting pada endpoint auth (10 req/menit)

---

**Last Updated:** 2 Juli 2026  
**Version:** 1.0
