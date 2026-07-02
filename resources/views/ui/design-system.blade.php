@extends('layouts.app')

@section('title', 'Design System - Frontend BULOG')
@section('topbar-meta', 'Pondasi visual: warna, komponen, dan layout dasar')

@push('styles')
    @vite(['resources/css/design-system.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Design System</h1>
        <p class="page-lead">Komponen dasar ini menjadi acuan untuk seluruh halaman frontend agar warna, tipografi, dan pola layout konsisten.</p>
    </div>
    <div class="preview-banner">
        <strong>Scope selesai:</strong> pondasi visual dan layout dasar sudah tersedia sebagai dasar pengerjaan halaman berikutnya.
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card">
        <p class="stat-label">Brand Color</p>
        <p class="stat-value">Biru BULOG</p>
        <p class="surface-note">Dipakai untuk header, tombol utama, dan state aktif.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Accent Color</p>
        <p class="stat-value">Merah Aksen</p>
        <p class="surface-note">Dipakai untuk highlight, warning action, dan emphasis.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Typography</p>
        <p class="stat-value">Inter / UI Sans</p>
        <p class="surface-note">Mengutamakan keterbacaan di desktop dan mobile.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Layout</p>
        <p class="stat-value">Responsive</p>
        <p class="surface-note">Grid fleksibel untuk dashboard, form, dan tabel data.</p>
    </article>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Buttons</strong>
        </div>
        <div class="card-surface__body stack">
            <div>
                <button class="btn-ui btn-primary-ui" type="button">Primary</button>
                <button class="btn-ui btn-secondary-ui" type="button">Secondary</button>
                <button class="btn-ui btn-danger-ui" type="button">Danger</button>
            </div>
            <p class="surface-note">Ukuran tombol dibuat cukup besar untuk interaksi touch.</p>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Badges Kondisi</strong>
        </div>
        <div class="card-surface__body stack">
            <div>
                <span class="badge-ui badge-baik">Baik</span>
                <span class="badge-ui badge-rusak-ringan">Rusak Ringan</span>
                <span class="badge-ui badge-rusak-berat">Rusak Berat</span>
                <span class="badge-ui badge-dalam-perbaikan">Dalam Perbaikan</span>
                <span class="badge-ui badge-tidak-aktif">Tidak Aktif</span>
            </div>
            <p class="surface-note">Warna badge sudah disiapkan untuk status aset sesuai panduan.</p>
        </div>
    </article>
</section>

<section class="component-grid component-grid--full">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Forms, Table, and Alerts</strong>
        </div>
        <div class="card-surface__body component-grid">
            <form class="stack">
                <div>
                    <label class="form-label-ui" for="sample-text">Text Input</label>
                    <input class="form-control-ui" id="sample-text" type="text" placeholder="Contoh input">
                </div>
                <div>
                    <label class="form-label-ui" for="sample-select">Select Input</label>
                    <select class="form-select-ui" id="sample-select">
                        <option>Pilih opsi</option>
                        <option>Opsi 1</option>
                        <option>Opsi 2</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="sample-date">Date Input</label>
                    <input class="form-control-ui" id="sample-date" type="date">
                </div>
            </form>

            <div class="stack">
                <div class="alert-ui alert-success-ui">Alert sukses untuk notifikasi positif.</div>
                <div class="alert-ui alert-warning-ui">Alert peringatan untuk validasi atau kondisi penting.</div>
                <div class="alert-ui alert-info-ui">Alert info untuk petunjuk atau penjelasan UI.</div>
            </div>
        </div>

        <div class="card-surface__body card-surface__body--no-top">
            <table class="table-ui">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Kode Aset</th>
                        <th>Nama</th>
                        <th>Kondisi</th>
                        <th>Lokasi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>AST-001</td>
                        <td>Laptop Operasional</td>
                        <td><span class="badge-ui badge-baik">Baik</span></td>
                        <td>Ruang IT</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>AST-002</td>
                        <td>Printer Administrasi</td>
                        <td><span class="badge-ui badge-rusak-ringan">Rusak Ringan</span></td>
                        <td>Ruang Tata Usaha</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </article>
</section>
@endsection