@extends('layouts.app')

@section('title', 'Laporan Aset - Frontend BULOG')
@section('topbar-meta', 'Filter, preview, dan export laporan aset')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Laporan Aset</h1>
        <p class="page-lead">Halaman laporan menyediakan filter untuk kondisi, lokasi, jenis, PIC, dan rentang tanggal dengan area preview tabel hasil.</p>
    </div>
    <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
        <button class="btn-ui btn-secondary-ui" type="button">Export PDF</button>
        <button class="btn-ui btn-primary-ui" type="button">Export Excel</button>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <div class="component-grid" style="grid-template-columns: repeat(5, minmax(0, 1fr));">
            <select class="form-select-ui"><option>Semua Kondisi</option><option>Baik</option><option>Rusak Ringan</option><option>Rusak Berat</option></select>
            <select class="form-select-ui"><option>Semua Lokasi</option><option>Ruang IT</option><option>Ruang TU</option><option>Gudang Utama</option></select>
            <select class="form-select-ui"><option>Semua Jenis</option><option>Laptop</option><option>Printer</option></select>
            <select class="form-select-ui"><option>Semua PIC</option><option>Andi</option><option>Sari</option><option>Rudi</option></select>
            <input class="form-control-ui" type="date">
        </div>
        <div style="margin-top: 1rem;">
            <button class="btn-ui btn-primary-ui" type="button">Tampilkan Laporan</button>
        </div>
    </div>

    <div class="card-surface__body" style="padding-top: 0;">
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