@extends('layouts.app')

@section('title', 'Pengaturan - Lumina Asset')
@section('topbar-meta', 'System configuration dan manajemen pengguna')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/settings-index.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Pengaturan</h1>
        <p class="page-lead">Kelola pengguna, lokasi, dan parameter sistem untuk infrastruktur aset perusahaan.</p>
    </div>
    <div class="button-group">
        <button class="btn-ui btn-primary-ui" type="button">Tambah Admin</button>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Manajemen Pengguna</strong>
    </div>
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>Administrator</th>
                    <th>Role</th>
                    <th>Last Access</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->name }}<br><span class="surface-note">{{ $user->email }}</span></td>
                        <td>{{ ucfirst($user->role) }}</td>
                        <td>2 mins ago</td>
                        <td><button class="btn-ui btn-secondary-ui" type="button">Detail</button></td>
                    </tr>
                @empty
                    <tr><td colspan="4">Tidak ada pengguna terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Pengaturan Lokasi</strong>
    </div>
    <div class="card-surface__body">
        <div class="component-grid component-grid--full component-grid--compact">
            <div>
                <label class="form-label-ui">Nama Kantor Cabang</label>
                <input class="form-control-ui" type="text" value="Sudirman Central Hub">
            </div>
            <div>
                <label class="form-label-ui">Koordinat Geografis</label>
                <div class="component-grid component-grid--compact component-grid--full">
                    <input class="form-control-ui" type="text" value="-6.2247">
                    <input class="form-control-ui" type="text" value="106.8077">
                </div>
            </div>
            <div>
                <label class="form-label-ui">Radius Geofence (50km)</label>
                <input class="form-control-ui" type="range" min="0" max="100" value="50">
            </div>
        </div>
        <button class="btn-ui btn-primary-ui mt-1" type="button">Perbarui Data Geospasial</button>
    </div>
</section>
@endsection
