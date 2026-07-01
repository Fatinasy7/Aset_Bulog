# Laporan Khansa - Frontend Finalisasi

## Status
- Login frontend kini terhubung ke endpoint backend `/api/auth/login`.
- Aplikasi menyimpan token dan data user di `localStorage`.
- Data aset diambil dari API `/api/assets` ketika token tersedia.
- Scan manual memanggil `/api/assets/{asset}/scan` dan menampilkan hasil aset.
- Export Excel dan PDF memanggil endpoint laporan backend.

## Catatan penting
- Backend sekarang mengharapkan field `email` dan `password` untuk login.
- Respons API aset menggunakan struktur `{ data: [...] }`.
- Role pengguna diterjemahkan ke UI yang sesuai (`admin_it` -> admin, `user_pic` -> pic, `manajemen` -> manager).

## Langkah verifikasi
1. Buka aplikasi di browser.
2. Login menggunakan akun admin atau PIC.
3. Pastikan dashboard dan daftar aset muncul.
4. Coba scan manual dan cek respons API pada network tab.
5. Coba export laporan Excel.
