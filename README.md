# Aset Bulog

Sistem manajemen aset BULOG untuk pencatatan, pemantauan, dan pelaporan aset kantor seperti laptop dan printer.

## Ringkasan Proyek

Aplikasi ini terdiri dari:
- Backend Laravel dengan API RESTful untuk aset, PIC, autentikasi, laporan, backup, dan security hardening.
- Frontend statis / aplikasi web berbasis HTML/CSS/JavaScript yang dapat dijalankan dari `public/index.html`.
- Dokumentasi tim, seeder data, dan tes fitur.

## Apa yang Termasuk

- `app/` : backend Laravel, model, controller, middleware
- `database/` : migrasi, seeder, factory
- `public/` : halaman web frontend utama `index.html`, asset publik
- `resources/` : sumber frontend Vite (`js`, `css`, `views`)
- `routes/` : route web dan API
- `tests/` : unit/feature tests
- `docs/` : panduan per anggota dan overview proyek

## Stack Teknologi

- PHP 8+ / Laravel
- MySQL / MariaDB
- Laravel Sanctum untuk autentikasi API
- Bootstrap + FontAwesome untuk UI frontend
- Vite untuk bundling frontend (`resources/js/app.js`, `resources/css/app.css`)
- Axios / Fetch API untuk integrasi JavaScript (jika dikembangkan lebih lanjut)

## Persiapan Lingkungan

### Backend

1. Install dependency PHP
   ```powershell
composer install
```

2. Salin file konfigurasi environment
   ```powershell
copy .env.example .env
```

3. Sesuaikan koneksi database di `.env`
   ```text
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=aset_bulog
DB_USERNAME=root
DB_PASSWORD=
```

4. Generate key aplikasi
   ```powershell
php artisan key:generate
```

5. Migrasi dan seed database
   ```powershell
php artisan migrate --force
php artisan db:seed
```

### Frontend

Frontend utama saat ini disajikan dari `public/index.html`.
Jika Anda ingin mengembangkan asset Vite dari `resources/`, jalankan:

```powershell
npm install
npm run dev
```

Untuk build produksi frontend:

```powershell
npm run build
```

> Catatan: `package.json` sudah berisi skrip Vite dasar.

## Menjalankan Aplikasi

1. Jalankan backend Laravel
   ```powershell
php artisan serve
```

2. Buka browser ke
   - `http://127.0.0.1:8000` untuk halaman frontend utama
   - `http://127.0.0.1:8000/api` sebagai base API

3. Untuk development frontend Vite, jalankan juga
   ```powershell
npm run dev
```

## Akun Default & Login

### Akun seeder default
- Email: `admin@bulog.local`
- Password: `password123`
- Role: `admin_it`

Aplikasi backend menggunakan endpoint login API:

```bash
POST /api/auth/login
Content-Type: application/json
{
  "email": "admin@bulog.local",
  "password": "password123"
}
```

Jika frontend statis di `public/index.html` belum terhubung sepenuhnya ke backend, gunakan API langsung untuk pengujian.

## API Utama

### Autentikasi
- `POST /api/auth/login`
- `POST /api/auth/logout`
- `POST /api/auth/register`

### Aset
- `GET /api/assets`
- `GET /api/assets/{asset}`
- `POST /api/assets`
- `PUT /api/assets/{asset}`
- `DELETE /api/assets/{asset}`
- `GET /api/assets/{asset}/qrcode`
- `POST /api/assets/{asset}/scan`
- `GET /api/assets/{asset}/location`

### PIC
- `GET /api/pics`
- `POST /api/pics`
- `PUT /api/pics/{pic}`
- `DELETE /api/pics/{pic}`
- `POST /api/assets/{asset}/assign-pic`

### Laporan dan Backup
- `GET /api/reports/assets`
- `GET /api/backups`
- `POST /api/backups`
- `GET /api/backups/verify`

### Notifikasi
- `GET /api/notifications`
- `PATCH /api/notifications/{notification}/read`

## Frontend Saat Ini

Frontend saat ini beroperasi sebagai:
- `public/index.html` : halaman login dan layout aplikasi
- `public/js/app.js` : JavaScript publik yang dapat digunakan untuk logika UI
- `resources/js/app.js` : entry point Vite untuk pengembangan frontend
- `resources/css/app.css` : source CSS untuk frontend

Jika Anda mengembangkan frontend baru, simpan perubahan di `resources/` lalu gunakan Vite untuk build.

## Struktur Web Route

- `routes/web.php` mengarahkan `/` dan `/app` ke `public/index.html`
- Semua API berada di `routes/api.php`

## Pengembangan & Testing

### Menjalankan test

```powershell
php artisan test
```

Jika ingin menjalankan subset test:

```powershell
php artisan test --filter="SecurityHardeningTest"
```

### Dokumentasi Tim

- `docs/00_PROJECT_OVERVIEW.md` : overview proyek dan tim
- `docs/01_BACKEND_FATIN.md` : panduan backend
- `docs/02_FRONTEND_WAHYU.md` : panduan frontend UI/UX
- `docs/03_FRONTEND_KHANSA.md` : panduan frontend logic

## Troubleshooting Umum

- Pastikan `.env` diisi dengan benar dan database terhubung
- Pastikan `php artisan migrate --force` berjalan tanpa error
- Jika login gagal, cek seed data dan token API
- Jika frontend tidak muncul, pastikan route `web.php` menampilkan `public/index.html`
- Periksa `storage/logs/laravel.log` untuk error runtime

## Konvensi Branch & Commit

- `feature/<fitur>` : fitur baru
- `fix/<bug>` : perbaikan bug
- `docs/<topik>` : perubahan dokumentasi
- `test/<nama>` : penambahan atau perbaikan tes

Contoh commit:

```text
feat: tambah endpoint scan QR asset
fix: perbaiki validasi form login frontend
docs: update README proyek
```

## Lisensi

MIT
