@extends('layouts.app')

@section('title', 'Laporan Aset - Frontend BULOG')
@section('topbar-meta', 'Filter, preview, dan export laporan aset')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/reports-index.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Laporan Aset</h1>
        <p class="page-lead">Filter data aset dan ekspor laporan berdasarkan kondisi saat ini.</p>
    </div>
    <div class="button-group">
        <a href="{{ route('frontend.reports.export', request()->query()) }}" class="btn-ui btn-primary-ui">Export CSV</a>
        <button type="button" class="btn-ui btn-secondary-ui" onclick="exportLaporan('pdf')">Download PDF</button>
    </div>
</section>

<section class="dashboard-card-grid">
    <article class="metric-card metric-card--accent">
        <p class="metric-label">Total Asset Value</p>
        <h2 class="metric-value">Rp {{ number_format($summary['total_asset_value'], 0, ',', '.') }}</h2>
    </article>
    <article class="metric-card metric-card--soft">
        <p class="metric-label">Active Assets</p>
        <h2 class="metric-value">{{ $summary['active_assets'] }}</h2>
    </article>
    <article class="metric-card metric-card--warning">
        <p class="metric-label">Maintenance Required</p>
        <h2 class="metric-value">{{ $summary['maintenance_required'] }}</h2>
    </article>
    <article class="metric-card metric-card--info">
        <p class="metric-label">Average Depreciation</p>
        <h2 class="metric-value">{{ $summary['avg_depreciation'] }}%</h2>
    </article>
</section>

<section class="card-surface chart-panel">
    <div class="card-surface__header">
        <strong>Ringkasan Kondisi Aset</strong>
        <div class="surface-note">Tren kondisi aset berdasarkan filter laporan saat ini</div>
    </div>
    <div class="card-surface__body">
        <canvas id="report-condition-chart" width="800" height="320"></canvas>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        @if ($errors->any())
            <div class="alert-ui alert-danger mb-4">
                <strong>Periksa kembali filter laporan:</strong>
                <ul class="list-unstyled mt-2 mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form id="filter-form" method="GET" action="{{ route('frontend.reports.index') }}" class="component-grid component-grid--reports" novalidate>
            <input class="form-control-ui {{ $errors->has('search') ? 'is-invalid' : '' }}" type="search" name="search" value="{{ request('search') }}" placeholder="Cari kode, nama, merk, atau serial">
            @error('search')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <select class="form-select-ui {{ $errors->has('condition') ? 'is-invalid' : '' }}" name="condition">
                <option value="">Semua Kondisi</option>
                @foreach($conditions as $condition)
                    <option value="{{ $condition }}" {{ request('condition') === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                @endforeach
            </select>
            @error('condition')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <select class="form-select-ui {{ $errors->has('location') ? 'is-invalid' : '' }}" name="location">
                <option value="">Semua Lokasi</option>
                @foreach($locations as $location)
                    <option value="{{ $location }}" {{ request('location') === $location ? 'selected' : '' }}>{{ $location }}</option>
                @endforeach
            </select>
            @error('location')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <select class="form-select-ui {{ $errors->has('type') ? 'is-invalid' : '' }}" name="type">
                <option value="">Semua Jenis</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>{{ ucfirst($type) }}</option>
                @endforeach
            </select>
            @error('type')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <select class="form-select-ui {{ $errors->has('pic') ? 'is-invalid' : '' }}" name="pic">
                <option value="">Semua PIC</option>
                @foreach($pics as $pic)
                    <option value="{{ $pic }}" {{ request('pic') === $pic ? 'selected' : '' }}>{{ $pic }}</option>
                @endforeach
            </select>
            @error('pic')
                <p class="form-error">{{ $message }}</p>
            @enderror

            <div class="component-grid component-grid--date-range">
                <input class="form-control-ui {{ $errors->has('date_from') ? 'is-invalid' : '' }}" type="date" name="date_from" value="{{ request('date_from') }}">
                <input class="form-control-ui {{ $errors->has('date_to') ? 'is-invalid' : '' }}" type="date" name="date_to" value="{{ request('date_to') }}">
            </div>
            @error('date_from')
                <p class="form-error">{{ $message }}</p>
            @enderror
            @error('date_to')
                <p class="form-error">{{ $message }}</p>

            <button class="btn-ui btn-primary-ui" type="submit">Tampilkan Laporan</button>
        </form>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Inventory Detailed Log</strong>
        <div class="surface-note">Data aset sesuai filter laporan saat ini</div>
    </div>
    <div class="card-surface__body card-surface__body--no-top">
        <div class="table-responsive">
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
                        <th>Tanggal Perolehan</th>
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
                        <td colspan="8">
                            <div class="empty-state-card">
                                <div class="empty-state-card__icon">📄</div>
                                <div class="empty-state-card__title">Tidak ada data aset untuk filter ini</div>
                                <div class="empty-state-card__message">Filter yang dipilih tidak menghasilkan hasil. Sesuaikan filter atau tambahkan lebih banyak aset untuk melihat data di laporan.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        <div class="report-pagination">
            {{ $assets->links() }}
        </div>
    </div>
</section>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function getFilterValues() {
            const form = document.getElementById('filter-form');
            return new FormData(form);
        }

        function exportLaporan(format) {
            const formData = getFilterValues();
            const params = new URLSearchParams();

            for (const [key, value] of formData.entries()) {
                if (value) {
                    params.append(key, value);
                }
            }

            if (format === 'pdf') {
                window.location.href = `{{ route('frontend.reports.download') }}?${params.toString()}`;
                return;
            }

            if (format === 'csv') {
                window.location.href = `{{ route('frontend.reports.export') }}?${params.toString()}`;
                return;
            }

            window.location.href = `{{ route('frontend.reports.index') }}?${params.toString()}`;
        }

        document.addEventListener('DOMContentLoaded', function () {
            const summaryData = @json($conditionCounts ?? []);
            const ctx = document.getElementById('report-condition-chart');

            if (!ctx || Object.keys(summaryData).length === 0) {
                return;
            }

            const labels = Object.keys(summaryData);
            const values = Object.values(summaryData);

            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Jumlah Aset',
                        data: values,
                        backgroundColor: labels.map(() => 'rgba(14, 165, 233, 0.85)'),
                        borderColor: labels.map(() => 'rgba(2, 132, 199, 0.95)'),
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                            },
                        },
                    },
                },
            });
        });
    </script>
@endpush
@endsection