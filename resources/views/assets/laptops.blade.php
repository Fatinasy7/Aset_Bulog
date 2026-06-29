@extends('layouts.app')

@section('title', 'Data Laptop - Lumina Asset')
@section('topbar-meta', 'Inventory dan status laptop asset management')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Hardware Inventory</h1>
        <p class="page-lead">Managing {{ $assets->count() }} active workstations across 4 hubs.</p>
    </div>
    <div class="page-actions">
        <input class="form-control-ui" type="search" placeholder="Search by Serial, Model, or User...">
        <div class="button-group">
            <button class="btn-ui btn-secondary-ui" type="button">Filter</button>
            <button class="btn-ui btn-primary-ui" type="button">Export</button>
        </div>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card">
        <p class="stat-label">Total Laptops</p>
        <p class="stat-value">{{ $summary['total_assets'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Operational</p>
        <p class="stat-value">{{ $summary['total_operational'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">In Maintenance</p>
        <p class="stat-value">{{ $summary['total_maintenance'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Critical Issues</p>
        <p class="stat-value">{{ $summary['critical_issues'] }}</p>
    </article>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>Asset ID</th>
                    <th>Model</th>
                    <th>Specs</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $asset)
                    <tr>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ $asset->merk_type }} / {{ $asset->serial_number ?? '-' }}</td>
                        <td>{{ $asset->pic_name ?? 'Unassigned' }}</td>
                        <td><span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></td>
                        <td>
                            <div class="action-row action-row--compact">
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.show', $asset) }}">View</a>
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.edit', $asset) }}">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No laptop data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
