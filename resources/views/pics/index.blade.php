@extends('layouts.app')

@section('title', 'Manajemen PIC - Frontend BULOG')
@section('topbar-meta', 'Daftar PIC dan akses ke form tambah/edit')

@push('styles')
    @vite(['resources/css/pics-index.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Manajemen PIC</h1>
        <p class="page-lead">Halaman ini digunakan untuk mengelola penanggung jawab aset, termasuk daftar, jabatan, dan kontak.</p>
    </div>
    <div>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.pics.create') }}">Tambah PIC</a>
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
                @forelse ($pics as $index => $pic)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $pic->name }}</td>
                        <td>{{ $pic->role === 'pic' ? 'PIC' : ucfirst($pic->role) }}</td>
                        <td>{{ $pic->email }}</td>
                        <td>{{ $pic->phone ?? '-' }}</td>
                        <td>
                            <div class="action-row action-row--compact">
                                <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.pics.edit', $pic) }}">Edit</a>
                                <form method="POST" action="{{ route('frontend.pics.destroy', $pic) }}" class="inline-form">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-ui btn-danger-ui" type="submit" onclick="return confirm('Hapus PIC ini?')">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">Belum ada PIC dari backend.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection