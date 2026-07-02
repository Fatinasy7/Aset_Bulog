@extends('layouts.app')

@section('title', isset($pic) ? 'Edit PIC - Frontend BULOG' : 'Tambah PIC - Frontend BULOG')
@section('topbar-meta', isset($pic) ? 'Form edit data PIC' : 'Form tambah data PIC')

@push('styles')
    @vite(['resources/css/pics-form.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">{{ isset($pic) ? 'Edit PIC' : 'Tambah PIC' }}</h1>
        <p class="page-lead">Form ini dipakai untuk {{ isset($pic) ? 'memperbarui' : 'menambahkan' }} data PIC yang bertanggung jawab atas aset.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.pics.index') }}">Kembali ke Daftar</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <form class="stack" method="POST" action="{{ isset($pic) ? route('frontend.pics.update', $pic) : route('frontend.pics.store') }}">
            @csrf
            @if (isset($pic))
                @method('PUT')
            @endif

            @if ($errors->any())
                <div class="alert-ui alert-danger">
                    <ul class="list-unstyled">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="component-grid">
                <div>
                    <label class="form-label-ui" for="name">Nama</label>
                    <input class="form-control-ui" name="name" id="name" type="text" value="{{ old('name', isset($pic) ? $pic->name : '') }}" placeholder="Nama PIC" required>
                </div>
                <div>
                    <label class="form-label-ui" for="email">Email</label>
                    <input class="form-control-ui" name="email" id="email" type="email" value="{{ old('email', isset($pic) ? $pic->email : '') }}" placeholder="nama@bulog.co.id" required>
                </div>
                <div>
                    <label class="form-label-ui" for="phone">Nomor Telepon</label>
                    <input class="form-control-ui" name="phone" id="phone" type="tel" value="{{ old('phone', isset($pic) ? $pic->phone : '') }}" placeholder="08xx-xxxx-xxxx">
                </div>
                <div>
                    <label class="form-label-ui" for="role">Role</label>
                    <select class="form-select-ui" name="role" id="role" required>
                        <option value="pic"{{ old('role', isset($pic) ? $pic->role : 'pic') == 'pic' ? ' selected' : '' }}>PIC</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button class="btn-ui btn-primary-ui" type="submit">Simpan</button>
                <button class="btn-ui btn-secondary-ui" type="reset">Batal</button>
            </div>
        </form>
    </div>
</section>
@endsection