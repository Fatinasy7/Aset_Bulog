# Quick Start — Aset Bulog Backend

Panduan singkat untuk menjalankan proyek Laravel ini di lingkungan lokal.

## 1. Persyaratan

Pastikan perangkat sudah memiliki:
- PHP 8.2+ atau 8.3+
- Composer
- MySQL / MariaDB
- Node.js dan npm (opsional untuk frontend)

## 2. Clone dan masuk ke folder proyek

```bash
git clone <repo-url>
cd Aset_Bulog
```

## 3. Install dependency PHP

```bash
composer install
```

## 4. Siapkan file environment

Salin file contoh environment lalu sesuaikan konfigurasi database:

```bash
copy .env.example .env
```

Edit file `.env` dan atur setidaknya:
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=aset_bulog`
- `DB_USERNAME=root`
- `DB_PASSWORD=`

Buat juga aplikasi key:

```bash
php artisan key:generate
```

## 5. Jalankan migrasi dan seeder

```bash
php artisan migrate
php artisan db:seed
```

Jika ingin data awal lengkap, bisa gunakan:

```bash
php artisan migrate:fresh --seed
```

## 6. Jalankan server Laravel

```bash
php artisan serve
```

Akses aplikasi melalui:
- Backend API: http://127.0.0.1:8000

## 7. Login dan akses API

Setelah server berjalan, Anda bisa login dengan salah satu akun berikut jika seeder sudah dijalankan:

- Email: admin@bulog.local
- Password: password123
- Role: admin_it

### Contoh login

```bash
curl -X POST http://127.0.0.1:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@bulog.local","password":"password123"}'
```

Response akan mengembalikan token. Gunakan token tersebut pada request berikutnya sebagai Bearer Token:

```bash
curl -X GET http://127.0.0.1:8000/api/assets \
  -H "Authorization: Bearer <token>"
```

### Endpoint utama yang bisa dicoba

#### Auth
- POST /api/auth/register
- POST /api/auth/login
- POST /api/auth/logout

#### Aset
- GET /api/assets
- POST /api/assets
- GET /api/assets/{id}

#### PIC
- GET /api/pics
- POST /api/pics

### Aset
- GET /api/assets
- POST /api/assets
- GET /api/assets/{id}

### PIC
- GET /api/pics
- POST /api/pics

## 8. Jalankan test

```bash
php artisan test
```

## 9. Fitur tambahan yang bisa diuji

```bash
php artisan app:create-database-backup
php artisan app:verify-database-integrity
```

## 10. Jika ada masalah

Periksa hal berikut:
- MySQL sudah berjalan
- `.env` sudah benar
- `php artisan config:clear`
- `php artisan cache:clear`

Jika butuh bantuan lebih lanjut, cek file dokumentasi proyek berikut:
- [QUICK_START.md](QUICK_START.md) — panduan menjalankan proyek
- [LAPORAN_FATIN_BACKEND.md](LAPORAN_FATIN_BACKEND.md) — laporan progres backend
- [docs/01_BACKEND_FATIN.md](docs/01_BACKEND_FATIN.md) — panduan tugas backend
