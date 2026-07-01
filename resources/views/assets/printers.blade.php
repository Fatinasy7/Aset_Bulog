@extends('layouts.app')

@section('title', 'Data Printer - Lumina Asset')
@section('topbar-meta', 'Printer fleet status and ink monitoring')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/printers.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Printer Fleet Status</h1>
        <p class="page-lead">Real-time monitoring of Lumina Asset Management's global printing infrastructure.</p>
    </div>
    <div class="page-actions">
        <input class="form-control-ui" type="search" placeholder="Search assets, nodes, or locations...">
        <div class="button-group">
            <button class="btn-ui btn-secondary-ui" type="button">Filter</button>
            <button class="btn-ui btn-primary-ui" type="button">Provision Node</button>
        </div>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card">
        <p class="stat-label">Total Printers</p>
        <p class="stat-value">{{ $summary['total_assets'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Online / Active</p>
        <p class="stat-value">{{ $summary['total_online'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Low Consumables</p>
        <p class="stat-value">{{ $summary['maintenance_required'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Maintenance Req.</p>
        <p class="stat-value">{{ $summary['alerts'] }}</p>
    </article>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <div class="table-responsive">
            <table class="table-ui">
                <thead>
                    <tr>
                        <th>Node Name</th>
                        <th>Status</th>
                        <th>Ink Levels</th>
                        <th>Paper Supply</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($assets as $asset)
                    <tr>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ $asset->kondisi }}</td>
                        <td><span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></td>
                        <td>{{ $asset->lokasi }}</td>
                        <td>
                            <div class="action-row action-row--compact">
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.show', $asset) }}">View</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state-card">
                                <div class="empty-state-card__icon">🖨️</div>
                                <div class="empty-state-card__title">Tidak ada data printer</div>
                                <div class="empty-state-card__message">Inventaris printer kosong. Tambahkan printer baru atau cek kembali sumber data backend.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</section>
@endsection
