@extends('layouts.app')

@section('title', 'Daftar Aset - Frontend BULOG')
@section('topbar-meta', 'Daftar aset, filter kondisi, dan aksi cepat')

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
        <div class="component-grid" style="grid-template-columns: 2fr 1fr 1fr 1fr;">
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

    <div class="card-surface__body" style="padding-top: 0;">
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
                <tr>
                    <td>1</td>
                    <td>AST-001</td>
                    <td>Laptop</td>
                    <td>Lenovo ThinkPad</td>
                    <td><span class="badge-ui badge-baik">Baik</span></td>
                    <td>Andi</td>
                    <td>Ruang IT</td>
                    <td>
                        <a class="btn-ui btn-secondary-ui" href="#">Detail</a>
                        <a class="btn-ui btn-secondary-ui" href="#">Edit</a>
                    </td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>AST-002</td>
                    <td>Printer</td>
                    <td>HP LaserJet</td>
                    <td><span class="badge-ui badge-rusak-ringan">Rusak Ringan</span></td>
                    <td>Sari</td>
                    <td>Ruang TU</td>
                    <td>
                        <a class="btn-ui btn-secondary-ui" href="#">Detail</a>
                        <a class="btn-ui btn-secondary-ui" href="#">Edit</a>
                    </td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>AST-003</td>
                    <td>Laptop</td>
                    <td>Asus VivoBook</td>
                    <td><span class="badge-ui badge-dalam-perbaikan">Dalam Perbaikan</span></td>
                    <td>Rudi</td>
                    <td>Ruang Akunting</td>
                    <td>
                        <a class="btn-ui btn-secondary-ui" href="#">Detail</a>
                        <a class="btn-ui btn-secondary-ui" href="#">Edit</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection