@extends('layouts.app')

@section('title', 'Data Laptop - Lumina Asset')

@push('styles')
    @vite(['resources/css/laptops.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Aset  Laptop</h1>
    </div>
    <div class="page-actions">
        <div>
            <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.create', ['jenis' => 'laptop']) }}">Tambah Aset</a>
        </div>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card">
        <p class="stat-label">Total Laptop</p>
        <p class="stat-value">{{ $summary['total_assets'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Siap Pakai</p>
        <p class="stat-value">{{ $summary['total_operational'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Dalam Perbaikan</p>
        <p class="stat-value">{{ $summary['total_maintenance'] }}</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Kondisi Parah</p>
        <p class="stat-value">{{ $summary['critical_issues'] }}</p>
    </article>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th class="col-asset-id">Asset ID</th>
                    <th class="col-model">Model</th>
                    <th class="col-specs">Specs</th>
                    <th class="col-lokasi">Lokasi</th>
                    <th class="col-assigned">Assigned To</th>
                    <th class="col-status">Status</th>
                    <th class="col-actions">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $asset)
                    <tr>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ $asset->merk_type }} / {{ $asset->serial_number ?? '-' }}</td>
                        <td>{{ $asset->lokasi ?? '-' }}</td>
                        <td>{{ $asset->pic_name ?? 'Unassigned' }}</td>
                        <td><span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></td>
                        <td>
                            <div class="action-row action-row--compact">
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.show', $asset) }}">View</a>
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.edit', $asset) }}">Edit</a>
                                <form method="POST" action="{{ route('frontend.assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ route('frontend.assets.laptops') }}">
                                    <button type="submit" class="btn-ui btn-danger-ui">Hapus</button>
                                </form>
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
