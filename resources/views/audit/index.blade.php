@extends('layouts.app')

@section('title', 'Audit Trail - Frontend BULOG')
@section('topbar-meta', 'Riwayat perubahan aset dan aktivitas update')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Audit Trail</h1>
        <p class="page-lead">Halaman riwayat perubahan digunakan untuk menampilkan log perubahan data aset, field yang diubah, dan pengguna yang melakukannya.</p>
    </div>
    <div>
        <input class="form-control-ui" type="search" placeholder="Cari aset atau pengguna" style="min-width: 280px;">
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Aset</th>
                    <th>Field Berubah</th>
                    <th>Nilai Lama</th>
                    <th>Nilai Baru</th>
                    <th>Diubah Oleh</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>2026-06-21</td>
                    <td>AST-001</td>
                    <td>Kondisi</td>
                    <td>Baik</td>
                    <td>Rusak Ringan</td>
                    <td>Andi</td>
                </tr>
                <tr>
                    <td>2026-06-19</td>
                    <td>AST-002</td>
                    <td>Lokasi</td>
                    <td>Ruang TU</td>
                    <td>Ruang Arsip</td>
                    <td>Sari</td>
                </tr>
                <tr>
                    <td>2026-06-17</td>
                    <td>AST-003</td>
                    <td>PIC</td>
                    <td>Rudi</td>
                    <td>Fajar</td>
                    <td>Admin IT</td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection