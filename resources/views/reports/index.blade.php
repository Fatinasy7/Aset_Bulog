@extends('layouts.app')

@section('title', 'Laporan Aset - Frontend BULOG')
@section('topbar-meta', 'Filter, preview, dan export laporan aset')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Reports</h1>
        <p class="page-lead">Real-time monitoring of asset value, availability, maintenance, and forecast data.</p>
    </div>
    <div class="button-group">
        <button class="btn-ui btn-secondary-ui" type="button">Filter</button>
        <button class="btn-ui btn-primary-ui" type="button">Export CSV</button>
    </div>
</section>

<section class="dashboard-card-grid">
    <article class="metric-card metric-card--accent">
        <p class="metric-label">Total Asset Value</p>
        <h2 class="metric-value">Rp 4,820,500</h2>
    </article>
    <article class="metric-card metric-card--soft">
        <p class="metric-label">Active Assets</p>
        <h2 class="metric-value">942</h2>
    </article>
    <article class="metric-card metric-card--warning">
        <p class="metric-label">Maintenance Required</p>
        <h2 class="metric-value">28</h2>
    </article>
    <article class="metric-card metric-card--info">
        <p class="metric-label">Avg. Depreciation</p>
        <h2 class="metric-value">15.4%</h2>
    </article>
</section>

<section class="page-grid">
    <article class="card-surface report-panel">
        <div class="card-surface__header">
            <strong>Asset Life Expectancy</strong>
        </div>
        <div class="card-surface__body">
            <div class="placeholder-box placeholder-box--chart">
                <div class="text-center-muted">
                    <div class="placeholder-icon">▴</div>
                    <p>Fleet durability vs. usage hours</p>
                </div>
            </div>
        </div>
    </article>

    <aside class="card-surface report-summary">
        <div class="card-surface__body">
            <h2 class="metric-value">Q4 Budget Prediction</h2>
            <p class="surface-note">Automated forecast for procurement and savings.</p>
            <div class="progress-pill">
                <span>Procurement Goal</span>
                <strong>Rp 850,000</strong>
            </div>
            <div class="progress-bar">
                <span style="width: 72%;"></span>
            </div>
            <p class="surface-note">Projected savings Rp 124,300 with improved maintenance cycles.</p>
            <button class="btn-ui btn-primary-ui btn-full" type="button">Download Full Forecast</button>
        </div>
    </aside>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Inventory Detailed Log</strong>
        <div class="surface-note">Real-time status of all managed entities</div>
    </div>
    <div class="card-surface__body">
        <div class="table-toolbar">
            <input class="form-control-ui" type="search" placeholder="Search reports...">
            <div class="button-group">
                <button class="btn-ui btn-secondary-ui" type="button">Filter</button>
                <button class="btn-ui btn-primary-ui" type="button">Export CSV</button>
            </div>
        </div>
        <table class="table-ui">
    <div class="card-surface__body">
        <div class="component-grid component-grid--reports">
            <select class="form-select-ui"><option>Semua Kondisi</option><option>Baik</option><option>Rusak Ringan</option><option>Rusak Berat</option></select>
            <select class="form-select-ui"><option>Semua Lokasi</option><option>Ruang IT</option><option>Ruang TU</option><option>Gudang Utama</option></select>
            <select class="form-select-ui"><option>Semua Jenis</option><option>Laptop</option><option>Printer</option></select>
            <select class="form-select-ui"><option>Semua PIC</option><option>Andi</option><option>Sari</option><option>Rudi</option></select>
            <input class="form-control-ui" type="date">
        </div>
        <div class="mt-1">
            <button class="btn-ui btn-primary-ui" type="button">Tampilkan Laporan</button>
        </div>
    </div>

    <div class="card-surface__body card-surface__body--no-top">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Jenis</th>
                    <th>Nama Aset</th>
                    <th>Kondisi</th>
                    <th>PIC</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $index => $asset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ ucfirst($asset->jenis) }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td><span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></td>
                        <td>{{ $asset->pic_name ?? '-' }}</td>
                        <td>{{ $asset->lokasi }}</td>
                        <td>{{ optional($asset->tgl_perolehan)->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Belum ada data laporan dari backend.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection