@extends('layouts.app')

@section('title', 'Daftar Aset - Frontend BULOG')
@section('topbar-meta', 'Daftar aset, filter kondisi, dan aksi cepat')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/assets-index.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Daftar Aset</h1>
        <p class="page-lead">Halaman tabel aset untuk memantau nomor aset, kondisi, PIC, dan lokasi dengan layout yang siap dipakai untuk CRUD.</p>
    </div>
    <div>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.create') }}">Tambah Aset</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <div class="component-grid component-grid--assets">
            <input class="form-control-ui" type="search" placeholder="Cari kode, nama aset, atau PIC">
            <select class="form-select-ui">
                <option>Semua Kondisi</option>
                <option>Baik</option>
                <option>Rusak Ringan</option>
                <option>Rusak Berat</option>
            </select>
            <select class="form-select-ui">
                <option>Semua Jenis</option>
                <option>Laptop</option>
                <option>Printer</option>
            </select>
            <select class="form-select-ui">
                <option>Semua Lokasi</option>
                <option>Ruang IT</option>
                <option>Ruang Direksi</option>
                <option>Gudang Utama</option>
            </select>
        </div>
    </div>

    <div class="card-surface__body card-surface__body--no-top">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Jenis</th>
                    <th>Merek</th>
                    <th>Kondisi</th>
                    <th>PIC</th>
                    <th>Lokasi</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $index => $asset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ ucfirst($asset->jenis) }}</td>
                        <td>{{ $asset->merk_type }}</td>
                        <td><span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></td>
                        <td>{{ $asset->pic_name ?? '-' }}</td>
                        <td>{{ $asset->lokasi }}</td>
                        <td>
                        <div class="action-row action-row--compact">
                            <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.show', $asset) }}">Detail</a>
                            <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.edit', $asset) }}">Edit</a>
                            <form method="POST" action="{{ route('frontend.assets.destroy', $asset) }}" class="inline-form">
                                @csrf
                                @method('DELETE')
                                <button class="btn-ui btn-danger-ui" type="submit" onclick="return confirm('Hapus aset ini?')">Hapus</button>
                            </form>
                        </div>
                    </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Belum ada data aset dari backend.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection