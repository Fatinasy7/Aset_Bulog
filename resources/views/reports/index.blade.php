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
                <tr>
                    <td>1</td>
                    <td>AST-001</td>
                    <td>Laptop</td>
                    <td>Laptop Operasional</td>
                    <td><span class="badge-ui badge-baik">Baik</span></td>
                    <td>Andi</td>
                    <td>Ruang IT</td>
                    <td>2026-06-20</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>AST-002</td>
                    <td>Printer</td>
                    <td>Printer Administrasi</td>
                    <td><span class="badge-ui badge-rusak-ringan">Rusak Ringan</span></td>
                    <td>Sari</td>
                    <td>Ruang TU</td>
                    <td>2026-06-18</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>AST-003</td>
                    <td>Laptop</td>
                    <td>Laptop Akunting</td>
                    <td><span class="badge-ui badge-dalam-perbaikan">Dalam Perbaikan</span></td>
                    <td>Rudi</td>
                    <td>Ruang Akunting</td>
                    <td>2026-06-12</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection