@extends('layouts.app')

@section('title', 'Manajemen PIC - Frontend BULOG')
@section('topbar-meta', 'Daftar PIC dan akses ke form tambah/edit')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Manajemen PIC</h1>
        <p class="page-lead">Halaman ini digunakan untuk mengelola penanggung jawab aset, termasuk daftar, jabatan, dan kontak.</p>
    </div>
    <div>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.pics.form') }}">Tambah PIC</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>Andi Saputra</td>
                    <td>Staff IT</td>
                    <td>andi@bulog.co.id</td>
                    <td>0812-0000-1111</td>
                    <td><a class="btn-ui btn-secondary-ui" href="{{ route('frontend.pics.form') }}">Edit</a></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Sari Wulandari</td>
                    <td>Staf TU</td>
                    <td>sari@bulog.co.id</td>
                    <td>0812-0000-2222</td>
                    <td><a class="btn-ui btn-secondary-ui" href="{{ route('frontend.pics.form') }}">Edit</a></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Rudi Hartono</td>
                    <td>Supervisor Gudang</td>
                    <td>rudi@bulog.co.id</td>
                    <td>0812-0000-3333</td>
                    <td><a class="btn-ui btn-secondary-ui" href="{{ route('frontend.pics.form') }}">Edit</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
@endsection