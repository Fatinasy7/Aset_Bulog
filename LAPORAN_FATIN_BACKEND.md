# Laporan Pengerjaan Backend — Fatin Asyifa Nurrizky JenPutri

## Ringkasan
- Periode pengerjaan: 22 Juni 2026
- Branch utama: `feature/auth-sanctum` dan `feature/asset-crud`
- Fokus: Fondasi Keamanan, Autentikasi, dan Penguatan CRUD Aset
- Status: Selesai untuk langkah pertama dan dua (autentikasi, role-based access, asset CRUD, soft delete, audit trail)

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

## Implementasi Keamanan
- Semua route sensitif sekarang berada di dalam middleware `auth:sanctum`
- Aksi mutasi asset (`store`, `update`, `destroy`) dibatasi untuk role `admin_it`
- Role valid pada registrasi hanya menerima `admin_it`, `user_pic`, atau `manajemen`
- Token akses dibuat menggunakan Laravel Sanctum di endpoint login/register

## Catatan Tambahan
- Saat ini fitur CRUD aset masih menggunakan controller `AssetController` dan hanya dapat dimodifikasi oleh `admin_it`
- Fitur lanjutannya seperti PIC management, QR code generator, dan QR geotagging sudah dikerjakan; laporan PDF/Excel masih belum dikerjakan di langkah ini
- Untuk pengujian awal, gunakan `php artisan route:list --path=api` dan migrasi + seeder tersedia untuk memulai data admin

## Rekomendasi Tindak Lanjut
1. Jalankan `php artisan migrate` lalu `php artisan db:seed`
2. Uji `POST /api/auth/register`, `POST /api/auth/login`, `POST /api/auth/logout`
3. Uji `GET /api/assets/{asset}/qrcode` untuk mengunduh file QR SVG
4. Lanjutan berikutnya: bangun `feature/asset-crud` dan `feature/pic-management`
