# 🎨 PANDUAN FRONTEND (UI/UX) — Wahyu Bonita Juliana Sari

> **Peran:** Programmer Bidang Frontend — UI/UX & Template Splitting
> **Proyek:** Sistem Manajemen Aset BULOG
> **Stack:** HTML5, CSS3, Tailwind CSS / Bootstrap, JavaScript

---

## 🚀 CARA MENGGUNAKAN PANDUAN INI

Gunakan prompt berikut saat membuka VS Code dan memulai sesi kerja:

```
Disini saya mendapati tugas sebagai Frontend (1) (Wahyu Bonita Juliana Sari),
maka dari itu tolong analisa terlebih dahulu agar saya dapat melihat apa saja
yang belum dan apa saja yang sudah dikerjakan dalam proyek ini. Dalam pengerjaan
diusahakan membuat pull request dan branch dengan nama branch-nya disesuaikan
dengan apa yang dikerjakan, dan jika pekerjaan sudah selesai buatkan file yang
berisi laporan yang telah dikerjakan sebagai bentuk laporan kepada PM.
```

---

## 🎯 Tanggung Jawab Utama

- Membuat **wireframe & mockup High-Fidelity** untuk seluruh 10 halaman sistem
- Menetapkan **design system** (warna, tipografi, komponen UI) yang konsisten
- Konversi desain menjadi **komponen HTML/CSS statis** yang siap dipakai
- Membangun **layout responsif** (desktop & mobile)
- Merancang **visualisasi data dashboard** (grafik, counter, tabel)
- Merancang layout halaman Laporan & Riwayat Perubahan (Audit Trail)

---

## 📋 CHECKLIST TUGAS LENGKAP

### 🔷 MINGGU 1 — Perancangan Visual

#### Design System (Standar Visual Seluruh Aplikasi)
- [ ] Tentukan **palet warna utama** (sesuaikan dengan identitas BULOG — biru/merah/abu)
- [ ] Tentukan **tipografi** (font heading & body — gunakan Google Fonts yang web-safe)
- [ ] Dokumentasikan **komponen UI standar:**
  - Button (primary, secondary, danger)
  - Form input (text, select, date picker)
  - Card / panel
  - Badge kondisi aset (BAIK = hijau, RUSAK RINGAN = kuning, RUSAK BERAT = merah, DALAM_PERBAIKAN = biru, TIDAK_AKTIF = abu)
  - Tabel data dengan header
  - Alert / notifikasi
  - Modal / dialog konfirmasi
- [ ] **Branch:** `feature/design-system`

#### Wireframe & Mockup (10 Halaman Wajib SRS §3.1)
- [ ] Mockup: **Halaman Login** (3 role: Admin IT, User/PIC, Manajemen)
- [ ] Mockup: **Dashboard Utama** (counter total aset, grafik kondisi, daftar PIC aktif)
- [ ] Mockup: **Daftar Aset** (tabel + filter kondisi/jenis/lokasi + search bar)
- [ ] Mockup: **Form Tambah/Edit Aset** (nomor seri, merek, model, kondisi, lokasi, PIC)
- [ ] Mockup: **Detail Aset** (info lengkap + QR Code + riwayat + lokasi terakhir)
- [ ] Mockup: **Halaman Scan QR Code** (area kamera + hasil scan)
- [ ] Mockup: **Halaman Manajemen PIC** (daftar PIC + form tambah/edit)
- [ ] Mockup: **Halaman Laporan Aset** (filter + tombol ekspor PDF/Excel)
- [ ] Mockup: **Halaman Riwayat Perubahan** (Audit Trail — tabel log perubahan)
- [ ] Mockup: **Dashboard Manajemen** (tampilan read-only untuk pimpinan)
- [ ] **Branch:** `feature/mockup-all-pages`

---

### 🔷 MINGGU 1–2 — Implementasi Layout Statis

#### Halaman Login
- [ ] Form login dengan input email & password
- [ ] Dropdown atau tampilan pemilihan role
- [ ] Tombol "Masuk" dengan validasi form dasar (HTML5 required)
- [ ] Responsif untuk desktop & mobile
- [ ] **Branch:** `feature/layout-login`

#### Form Input Data Aset (FR-05)
- [ ] Field: Kode Aset (auto/manual), Jenis (Laptop/Printer), Merek, Model, Nomor Seri
- [ ] Field: Kondisi (dropdown: BAIK, RUSAK RINGAN, RUSAK BERAT, DALAM_PERBAIKAN, TIDAK_AKTIF)
- [ ] Field: Lokasi, PIC (dropdown dari daftar PIC)
- [ ] Tombol Simpan & Batal
- [ ] Validasi visual (highlight field kosong, error message)
- [ ] **Branch:** `feature/layout-asset-form`

#### Form Input Data PIC (FR-09)
- [ ] Field: Nama, Jabatan, Email, Nomor Telepon
- [ ] Tombol Simpan & Batal
- [ ] **Branch:** `feature/layout-pic-form`

#### Tabel Daftar Aset
- [ ] Kolom: No, Kode Aset, Jenis, Merek, Kondisi (badge warna), PIC, Lokasi, Aksi
- [ ] Filter: dropdown kondisi, dropdown jenis, input search
- [ ] Pagination
- [ ] Tombol Tambah Aset (hanya terlihat untuk Admin IT)
- [ ] **Branch:** `feature/layout-asset-list`

#### Detail Aset
- [ ] Panel informasi lengkap aset
- [ ] Tampilan QR Code (gambar)
- [ ] Riwayat kondisi & PIC (tabel mini)
- [ ] Peta/koordinat lokasi terakhir (opsional: embed Google Maps iframe)
- [ ] **Branch:** `feature/layout-asset-detail`

---

### 🔷 MINGGU 2 — Dashboard & Visualisasi Data

#### Dashboard Utama (FR-21)
- [ ] **Counter Cards:** Total Aset, Total Laptop, Total Printer, Total PIC Aktif
- [ ] **Grafik Kondisi Aset:** Chart pie/bar (BAIK / RUSAK RINGAN / RUSAK BERAT / dll)
  - Gunakan library Chart.js atau ApexCharts (CDN)
- [ ] **Grafik Jenis Aset:** Laptop vs Printer
- [ ] **Tabel Ringkas:** Daftar aset terbaru atau aset bermasalah
- [ ] **Branch:** `feature/layout-dashboard`

#### Halaman Laporan Aset (FR-25, FR-26, FR-27)
- [ ] Form filter: kondisi, lokasi, jenis, PIC, rentang tanggal
- [ ] Tombol "Tampilkan Laporan"
- [ ] Tombol "Export PDF" dan "Export Excel"
- [ ] Area preview tabel hasil filter
- [ ] **Branch:** `feature/layout-report`

#### Halaman Riwayat Perubahan / Audit Trail (FR-08, FR-12 | NFR-04)
- [ ] Tabel log: Tanggal, Aset, Field yang Berubah, Nilai Lama, Nilai Baru, Diubah Oleh
- [ ] Filter berdasarkan tanggal atau nama aset
- [ ] **Branch:** `feature/layout-audit-trail`

#### Dashboard Manajemen (Read-Only)
- [ ] Sama seperti Dashboard Utama tetapi tanpa tombol aksi (tambah/edit/hapus)
- [ ] Fokus pada ringkasan grafis untuk keperluan pengambilan keputusan
- [ ] **Branch:** `feature/layout-dashboard-management`

---

### 🔷 MINGGU 3 — Penyelarasan & Finalisasi

#### Responsivitas & Aksesibilitas
- [ ] Uji semua halaman di layar desktop (1280px+)
- [ ] Uji semua halaman di layar tablet (768px)
- [ ] Uji semua halaman di layar mobile (375px–414px)
- [ ] Pastikan ukuran font minimum 14px untuk keterbacaan di lapangan
- [ ] Pastikan tombol memiliki area klik yang cukup besar untuk layar sentuh

#### Performa & Finishing (NFR-01)
- [ ] Pastikan loading halaman **< 2 detik** (kompres gambar, minify CSS/JS jika perlu)
- [ ] Periksa konsistensi warna, spasi, dan tipografi di semua halaman
- [ ] Bantu Khansa merapikan bug minor terkait layout saat data real-time dari backend masuk
- [ ] Pastikan semua transisi/animasi micro berjalan mulus

---

## 🗂️ Struktur Folder Frontend yang Disarankan

```
resources/
├── css/
│   └── app.css              ← custom styles + import Tailwind/Bootstrap
├── js/
│   └── app.js               ← entry JS (Khansa handle logic di sini)
└── views/
    ├── layouts/
    │   ├── app.blade.php     ← layout utama (navbar, sidebar, footer)
    │   └── auth.blade.php    ← layout halaman login
    ├── auth/
    │   └── login.blade.php
    ├── dashboard/
    │   ├── index.blade.php   ← dashboard admin
    │   └── management.blade.php ← dashboard manajemen (read-only)
    ├── assets/
    │   ├── index.blade.php   ← daftar aset
    │   ├── create.blade.php  ← form tambah aset
    │   ├── edit.blade.php    ← form edit aset
    │   └── show.blade.php    ← detail aset
    ├── pics/
    │   ├── index.blade.php   ← daftar PIC
    │   └── form.blade.php    ← form tambah/edit PIC
    ├── reports/
    │   └── index.blade.php   ← halaman laporan
    └── audit/
        └── index.blade.php   ← riwayat perubahan
```

---

## 🎨 Referensi Warna Badge Kondisi Aset

```css
/* Gunakan sebagai referensi class Tailwind atau CSS custom */
.badge-baik           { background: #22c55e; color: white; } /* Green */
.badge-rusak-ringan   { background: #f59e0b; color: white; } /* Amber */
.badge-rusak-berat    { background: #ef4444; color: white; } /* Red */
.badge-dalam-perbaikan{ background: #3b82f6; color: white; } /* Blue */
.badge-tidak-aktif    { background: #9ca3af; color: white; } /* Gray */
```

---

## 📝 Format Laporan Akhir (Setelah Selesai)

Buat file `LAPORAN_WAHYU_FRONTEND_UI.md` di root project:

```markdown
# Laporan Pengerjaan Frontend UI/UX — Wahyu Bonita Juliana Sari

## Ringkasan
- Periode pengerjaan: [tanggal mulai] s.d. [tanggal selesai]
- Total halaman yang dikerjakan: [jumlah]
- Total PR yang dibuat: [jumlah]

## Halaman & Komponen yang Diselesaikan
| Halaman/Komponen | Branch | Status PR |
|------------------|--------|-----------|
| Design System | feature/design-system | Merged |
| Layout Login | feature/layout-login | Merged |
| ... | ... | ... |

## Kendala & Solusi
- [Kendala]: [Solusi]

## Catatan untuk PM
- [Hal penting untuk diketahui PM]
```

---

*Panduan ini mengacu pada SRS v1.0 (§3.1 User Interfaces, §4.1–4.8) dan Laporan Distribusi Tugas Tim UAS BULOG.*
