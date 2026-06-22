# 📦 BULOG Asset Management — Project Overview

> **Sistem Informasi Pengelolaan Aset Laptop & Printer Berbasis Web dengan QR Code**
> Perusahaan Umum (Perum) BULOG | Version 1.0 | 2026

---

## 🎯 Tujuan Sistem

Menggantikan pencatatan aset manual (spreadsheet/dokumen fisik) menjadi sistem digital terintegrasi yang mencakup:
- Pencatatan & pengelolaan aset laptop dan printer
- Manajemen PIC (Person In Charge) per aset
- Pemantauan kondisi & lokasi aset secara real-time via dashboard
- Sistem tagging QR Code + Geotagging otomatis saat scan
- Ekspor laporan ke PDF dan Excel

---

## 👥 Struktur Tim

| Nama | Peran | File Panduan |
|------|-------|--------------|
| Adel Giskatarina | Project Manager (PM) | — |
| Caryksha Aulia Putri | Data / System Analyst | — |
| Fatin Asyifa Nurrizky JenPutri | Backend Programmer | `01_BACKEND_FATIN.md` |
| Wahyu Bonita Juliana Sari | Frontend Programmer (UI/UX) | `02_FRONTEND_WAHYU.md` |
| Khansa Mufidah | Frontend Programmer (Core Logic) | `03_FRONTEND_KHANSA.md` |

---

## 🛠️ Tech Stack

| Layer | Teknologi |
|-------|-----------|
| Backend Framework | Laravel (PHP) |
| Database | MySQL atau PostgreSQL |
| Frontend | HTML5, CSS3, Tailwind CSS / Bootstrap |
| JS Logic | Vanilla JS / Axios / Fetch API |
| QR Code Generator | PHP QR Code Library |
| QR Scanner | Instascan / HTML5-QRCode |
| Geolocation | HTML5 Geolocation API |
| Autentikasi | Session-based atau JWT |
| Export | PDF & Excel (Laravel package) |
| Server | Apache / Nginx di Linux/VPS |
| Komunikasi | HTTPS + JSON |

---

## 🗂️ Halaman Wajib Sistem (SRS §3.1)

| No | Halaman |
|----|---------|
| 1 | Halaman Login (role-based) |
| 2 | Dashboard Utama (ringkasan aset, kondisi, PIC) |
| 3 | Daftar Aset (filter & pencarian) |
| 4 | Form Tambah/Edit Aset |
| 5 | Detail Aset (riwayat, PIC, lokasi, QR Code) |
| 6 | Halaman Scan QR Code |
| 7 | Halaman Manajemen PIC |
| 8 | Halaman Laporan Aset |
| 9 | Halaman Riwayat Perubahan (Audit Trail) |
| 10 | Dashboard Manajemen (read-only untuk pimpinan) |

---

## 📋 Fitur & Kode Requirement (SRS §4)

| Kode | Fitur | Prioritas |
|------|-------|-----------|
| FR-01 s.d. FR-04 | Autentikasi & Otorisasi (Login, Register, Logout, RBAC) | High |
| FR-05 s.d. FR-08 | Manajemen Aset (CRUD + Audit Trail) | High |
| FR-09 s.d. FR-12 | Manajemen PIC (CRUD + Riwayat Penugasan) | High |
| FR-13 s.d. FR-17 | Sistem Tagging QR Code (Generate, Cetak, Scan, Geotagging) | High |
| FR-18 s.d. FR-21 | Tracking & Monitoring Aset (Status real-time, Dashboard) | High |
| FR-22 s.d. FR-24 | Notifikasi & Pengingat (email / internal) | Medium |
| FR-25 s.d. FR-27 | Laporan Aset (filter, ekspor PDF/Excel) | Medium |
| FR-28 s.d. FR-29 | Backup & Verifikasi Data | Medium |

---

## ⚙️ Aturan Bisnis Kritis (SRS §5.5)

- **BR-01:** Setiap aset WAJIB memiliki satu PIC aktif.
- **BR-02:** PIC wajib verifikasi kondisi aset berkala sesuai jadwal.
- **BR-03:** Aset **RUSAK BERAT** ❌ TIDAK DAPAT dipindah PIC sebelum diperbaiki/dinonaktifkan.
- **BR-04:** Penghapusan aset hanya oleh Admin IT dan WAJIB dicatat di audit trail.

---

## 📏 Standar Non-Fungsional (SRS §5.1–5.3)

| Kode | Ketentuan |
|------|-----------|
| NFR-01 | Response API & halaman web **< 2 detik** |
| NFR-02 | Minimal **50 pengguna aktif** bersamaan |
| NFR-03 | Proses scan QR Code hingga data tampil **< 3 detik** |
| NFR-06 | Semua komunikasi via **HTTPS** |
| NFR-07 | Password di-hash dengan **bcrypt** |
| NFR-09 | Proteksi **SQL Injection, XSS, CSRF** |

---

## 📅 Timeline Pengerjaan

| Minggu | Target |
|--------|--------|
| **Minggu 1** | Blueprint DB (Analyst) + Setup Laravel & Auth API (Backend) + Mockup & Form Layout Dasar (Frontend) |
| **Minggu 2** | QR Generator & Geotagging API (Backend) + Visualisasi Dashboard (Frontend Wahyu) + Camera Scanner & Integrasi API (Frontend Khansa) |
| **Minggu 3** | Optimasi performa, UAT bersama BULOG, bug fixing final, serah terima dokumen |

---

## 🔀 Panduan Git & Kolaborasi

### Konvensi Branch
```
feature/<nama-fitur>         → fitur baru
fix/<nama-bug>               → perbaikan bug
refactor/<nama-modul>        → refactoring kode
```

**Contoh:**
```
feature/auth-login
feature/asset-crud
feature/qr-generator
feature/qr-scanner-camera
fix/geolocation-not-sending
```

### Alur Kerja
1. Buat branch baru dari `main` / `develop`
2. Kerjakan fitur di branch tersebut
3. Commit dengan pesan yang jelas: `feat: tambah endpoint CRUD aset`
4. Buat **Pull Request (PR)** ke branch `develop`
5. Minta review dari anggota tim lain sebelum merge
6. Setelah PR di-merge, hapus branch yang sudah selesai

### Konvensi Commit Message
```
feat:     fitur baru
fix:      perbaikan bug
refactor: perubahan kode tanpa mengubah fungsi
docs:     perubahan dokumentasi
style:    perubahan format/styling
test:     penambahan/perubahan test
```

---

## 📝 Laporan Akhir Per Anggota

Setiap programmer wajib membuat file laporan setelah tugasnya selesai dengan format:
```
LAPORAN_<NAMA>_<PERAN>.md
```
Contoh: `LAPORAN_FATIN_BACKEND.md`

Isi laporan mencakup:
- Fitur / endpoint yang telah dikerjakan
- Branch dan PR yang dibuat
- Kendala yang dihadapi dan solusinya
- Status akhir (selesai / perlu follow-up)

---

*Dokumen ini dibuat berdasarkan SRS v1.0 dan Laporan Distribusi Tugas Tim UAS BULOG — Kelompok 2, 2026.*
