@extends('layouts.app')

@section('title', 'Data Printer - Lumina Asset')
@section('topbar-meta', 'Printer fleet status and ink monitoring')

@push('styles')
    @vite(['resources/css/printers.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Aset Printer</h1>
    </div>
    <div class="page-actions">
        <div>
            <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.create', ['jenis' => 'printer']) }}">Tambah Aset</a>
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
        <table class="table-ui">
            <thead>
                <tr>
                    <th class="col-model">Node Name</th>
                    <th class="col-status">Status</th>
                    <th class="col-ink">Ink Levels</th>
                    <th class="col-paper">Paper Supply</th>
                    <th class="col-lokasi">Lokasi</th>
                    <th class="col-actions">Actions</th>
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
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.edit', $asset) }}">Edit</a>
                                <form method="POST" action="{{ route('frontend.assets.destroy', $asset) }}" onsubmit="return confirm('Hapus aset ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="redirect_to" value="{{ route('frontend.assets.printers') }}">
                                    <button type="submit" class="btn-ui btn-danger-ui">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No printer data available.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
