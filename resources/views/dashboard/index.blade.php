@extends('layouts.app')

@section('title', 'Dashboard Utama - Frontend BULOG')
@section('topbar-meta', 'Ringkasan aset, kondisi, dan aktivitas operasional')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Dashboard Utama</h1>
        <p class="page-lead">Ringkasan operasional aset yang dipakai untuk memantau total aset, kondisi, dan daftar aset bermasalah.</p>
    </div>
    <div>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.create') }}">Tambah Aset</a>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card">
        <p class="stat-label">Total Aset</p>
        <p class="stat-value">128</p>
        <p class="surface-note">Seluruh laptop dan printer aktif di sistem.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Laptop</p>
        <p class="stat-value">84</p>
        <p class="surface-note">Termasuk unit operasional dan administrasi.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Printer</p>
        <p class="stat-value">44</p>
        <p class="surface-note">Mencakup printer aktif per unit kerja.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">PIC Aktif</p>
        <p class="stat-value">27</p>
        <p class="surface-note">PIC yang sudah terhubung ke aset penanggung jawab.</p>
    </article>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Grafik Kondisi Aset</strong>
        </div>
        <div class="card-surface__body">
            <div style="height: 260px; display:grid; place-items:center; border:1px dashed var(--color-border); border-radius: 16px; background: linear-gradient(180deg, rgba(31,94,154,0.03), rgba(215,38,56,0.02));">
                <div style="text-align:center; color: var(--color-muted);">
                    <div style="font-size:3rem; line-height:1;">◔</div>
                    <strong>Pie / Bar Chart Placeholder</strong>
                    <p class="surface-note">Siap diisi Chart.js atau ApexCharts pada tahap visualisasi data.</p>
                </div>
            </div>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Grafik Jenis Aset</strong>
        </div>
        <div class="card-surface__body">
            <div style="height: 260px; display:grid; place-items:center; border:1px dashed var(--color-border); border-radius: 16px; background: linear-gradient(180deg, rgba(31,94,154,0.03), rgba(215,38,56,0.02));">
                <div style="text-align:center; color: var(--color-muted);">
                    <div style="font-size:3rem; line-height:1;">▤</div>
                    <strong>Laptop vs Printer</strong>
                    <p class="surface-note">Area visual siap dipakai untuk ringkasan komposisi aset.</p>
                </div>
            </div>
        </div>
    </article>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Aset Bermasalah / Terbaru</strong>
    </div>
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Aset</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                    <th>PIC</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>AST-008</td>
                    <td>Printer Ruang Arsip</td>
                    <td><span class="badge-ui badge-rusak-ringan">Rusak Ringan</span></td>
                    <td>Ruang Arsip</td>
                    <td>Dewi</td>
                </tr>
                <tr>
                    <td>AST-014</td>
                    <td>Laptop Akunting</td>
                    <td><span class="badge-ui badge-dalam-perbaikan">Dalam Perbaikan</span></td>
                    <td>Ruang Akunting</td>
                    <td>Rina</td>
                </tr>
                <tr>
                    <td>AST-031</td>
                    <td>Printer Gudang</td>
                    <td><span class="badge-ui badge-baik">Baik</span></td>
                    <td>Gudang Utama</td>
                    <td>Fajar</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection