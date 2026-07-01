@extends('layouts.app')

@section('title', 'Dashboard Manajemen - Frontend BULOG')
@section('topbar-meta', 'Dashboard read-only untuk manajemen BULOG')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/management.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Dashboard Manajemen</h1>
        <p class="page-lead">Ringkasan data aset untuk pimpinan, tanpa tombol aksi CRUD.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.dashboard') }}">Kembali ke Dashboard Utama</a>
    </div>
</section>

<section class="component-grid component-grid--management">
    <article class="card-surface">
        <div class="card-surface__body">
            <strong>Total Aset</strong>
            <p class="stat-value">{{ $summary['total_assets'] }}</p>
        </div>
    </article>
    <article class="card-surface">
        <div class="card-surface__body">
            <strong>Total Laptop</strong>
            <p class="stat-value">{{ $summary['total_laptops'] }}</p>
        </div>
    </article>
    <article class="card-surface">
        <div class="card-surface__body">
            <strong>Total Printer</strong>
            <p class="stat-value">{{ $summary['total_printers'] }}</p>
        </div>
    </article>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Ringkasan Kondisi Aset</strong>
    </div>
    <div class="card-surface__body">
        @if ($conditionCounts->isEmpty())
            <div class="empty-state-card">
                <div class="empty-state-card__icon">📊</div>
                <div class="empty-state-card__title">Tidak ada ringkasan kondisi</div>
                <div class="empty-state-card__message">Tambahkan aset untuk melihat distribusi kondisi di dashboard manajemen.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-ui">
                    <thead>
                        <tr>
                            <th>Kondisi</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($conditionCounts as $condition => $count)
                        <tr>
                            <td>{{ $condition }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Daftar Aset Terbaru</strong>
    </div>
    <div class="card-surface__body">
        @if ($assets->isEmpty())
            <div class="empty-state-card">
                <div class="empty-state-card__icon">📦</div>
                <div class="empty-state-card__title">Tidak ada aset tersedia</div>
                <div class="empty-state-card__message">Tambahkan aset baru atau periksa data backend untuk menampilkan daftar aset terbaru.</div>
            </div>
        @else
            <div class="table-responsive">
                <table class="table-ui">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Aset</th>
                            <th>Nama Aset</th>
                            <th>Kondisi</th>
                            <th>PIC</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach ($assets->take(5) as $index => $asset)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $asset->kode_aset }}</td>
                            <td>{{ $asset->nama_aset }}</td>
                            <td>{{ $asset->kondisi }}</td>
                            <td>{{ $asset->pic_name ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</section>
@endsection
