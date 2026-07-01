@extends('layouts.app')

@section('title', 'Form Aset - Frontend BULOG')
@section('topbar-meta', 'Form tambah dan edit aset operasional')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/assets-form.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Form Tambah Aset</h1>
        <p class="page-lead">Form ini dirancang untuk mengisi kode aset, jenis, merek, kondisi, lokasi, dan PIC secara terstruktur.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.index') }}">Kembali ke Daftar</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <form class="stack" method="POST" action="{{ route('frontend.assets.store') }}">
            @csrf

            <div class="component-grid">
                <div>
                    <label class="form-label-ui" for="kode_aset">Kode Aset</label>
                    <input class="form-control-ui" name="kode_aset" id="kode_aset" type="text" value="{{ old('kode_aset') }}" placeholder="AST-001" required>
                </div>
                <div>
                    <label class="form-label-ui" for="jenis">Jenis</label>
                    <select class="form-select-ui" name="jenis" id="jenis" required>
                        <option value="">Pilih jenis</option>
                        <option value="laptop"{{ old('jenis') == 'laptop' ? ' selected' : '' }}>Laptop</option>
                        <option value="printer"{{ old('jenis') == 'printer' ? ' selected' : '' }}>Printer</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="merk_type">Merek / Model</label>
                    <input class="form-control-ui" name="merk_type" id="merk_type" type="text" value="{{ old('merk_type') }}" placeholder="Contoh: Lenovo ThinkPad X1" required>
                </div>
                <div>
                    <label class="form-label-ui" for="serial_number">Nomor Seri</label>
                    <input class="form-control-ui" name="serial_number" id="serial_number" type="text" value="{{ old('serial_number') }}" placeholder="Serial number">
                </div>
                <div>
                    <label class="form-label-ui" for="kondisi">Kondisi</label>
                    <select class="form-select-ui" name="kondisi" id="kondisi" required>
                        <option value="">Pilih kondisi</option>
                        <option value="Baik"{{ old('kondisi') == 'Baik' ? ' selected' : '' }}>BAIK</option>
                        <option value="Rusak Ringan"{{ old('kondisi') == 'Rusak Ringan' ? ' selected' : '' }}>RUSAK RINGAN</option>
                        <option value="Rusak Berat"{{ old('kondisi') == 'Rusak Berat' ? ' selected' : '' }}>RUSAK BERAT</option>
                        <option value="Dalam Perbaikan"{{ old('kondisi') == 'Dalam Perbaikan' ? ' selected' : '' }}>DALAM_PERBAIKAN</option>
                        <option value="Tidak Aktif"{{ old('kondisi') == 'Tidak Aktif' ? ' selected' : '' }}>TIDAK_AKTIF</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="lokasi">Lokasi</label>
                    <input class="form-control-ui" name="lokasi" id="lokasi" type="text" value="{{ old('lokasi') }}" placeholder="Ruang kerja / unit" required>
                </div>
                <div>
                    <label class="form-label-ui" for="pic">PIC</label>
                    <select class="form-select-ui" name="pic" id="pic">
                        <option value="">Pilih PIC</option>
                        @forelse ($pics as $pic)
                            <option value="{{ $pic->id }}"{{ old('pic') == $pic->id ? ' selected' : '' }}>{{ $pic->name }} - {{ $pic->email }}</option>
                        @empty
                            <option disabled>Tidak ada PIC tersedia</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="pic_name">Nama PIC di Aset</label>
                    <input class="form-control-ui" name="pic_name" id="pic_name" type="text" value="{{ old('pic_name') }}" placeholder="Nama PIC penanggung jawab">
                </div>
            </div>

            <div>
                <label class="form-label-ui" for="keterangan">Keterangan</label>
                <textarea class="form-control-ui" name="keterangan" id="keterangan" rows="4" placeholder="Catatan kondisi atau detail tambahan">{{ old('keterangan') }}</textarea>
            </div>

            <div class="button-group">
                <button class="btn-ui btn-primary-ui" type="submit">Simpan</button>
                <button class="btn-ui btn-secondary-ui" type="reset">Batal</button>
            </div>
        </form>

        @if ($errors->any())
            <div class="alert-ui alert-danger mt-1">
                <ul class="list-unstyled">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</section>
@endsection