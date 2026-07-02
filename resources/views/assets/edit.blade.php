@extends('layouts.app')

@section('title', 'Edit Aset - Frontend BULOG')
@section('topbar-meta', 'Form edit aset operasional')

@push('styles')
    @vite(['resources/css/assets-form.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Edit Aset</h1>
        <p class="page-lead">Ubah detail aset dan simpan perubahan ke backend.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route($asset->jenis === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops') }}">Kembali ke Daftar</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <form class="stack" method="POST" action="{{ route('frontend.assets.update', $asset) }}">
            @csrf
            @method('PUT')

            <input type="hidden" name="jenis" value="{{ old('jenis', $asset->jenis) }}">
            <input type="hidden" name="redirect_to" value="{{ old('redirect_to', $asset->jenis === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops') }}">
            <div class="component-grid">
                <div>
                    <label class="form-label-ui" for="kode_aset">ID Aset</label>
                    <input class="form-control-ui" name="kode_aset" id="kode_aset" type="text" value="{{ old('kode_aset', $asset->kode_aset) }}" placeholder="AST-001" required>
                </div>
                <div>
                    <label class="form-label-ui" for="nama_aset">Nama Aset</label>
                    <input class="form-control-ui" name="nama_aset" id="nama_aset" type="text" value="{{ old('nama_aset', $asset->nama_aset) }}" placeholder="Nama asset" required>
                </div>
                <div>
                    <label class="form-label-ui" for="merk_type">Spek Aset</label>
                    <input class="form-control-ui" name="merk_type" id="merk_type" type="text" value="{{ old('merk_type', $asset->merk_type) }}" placeholder="Merek / Type / Spesifikasi" required>
                </div>
                <div>
                    <label class="form-label-ui" for="lokasi">Lokasi</label>
                    <input class="form-control-ui" name="lokasi" id="lokasi" type="text" value="{{ old('lokasi', $asset->lokasi) }}" placeholder="Ruang kerja / unit" required>
                </div>
                <div>
                    <label class="form-label-ui" for="pic">PIC</label>
                    <select class="form-select-ui" name="pic" id="pic" required>
                        <option value="">Pilih PIC</option>
                        @forelse ($pics as $pic)
                            <option value="{{ $pic->id }}"{{ old('pic', $asset->pic_id) == $pic->id ? ' selected' : '' }}>{{ $pic->name }} - {{ $pic->email }}</option>
                        @empty
                            <option disabled>Tidak ada PIC tersedia</option>
                        @endforelse
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="kondisi">Status Aset</label>
                    <select class="form-select-ui" name="kondisi" id="kondisi" required>
                        <option value="">Pilih status</option>
                        <option value="Baik"{{ old('kondisi', $asset->kondisi) == 'Baik' ? ' selected' : '' }}>BAIK</option>
                        <option value="Rusak Ringan"{{ old('kondisi', $asset->kondisi) == 'Rusak Ringan' ? ' selected' : '' }}>RUSAK RINGAN</option>
                        <option value="Rusak Berat"{{ old('kondisi', $asset->kondisi) == 'Rusak Berat' ? ' selected' : '' }}>RUSAK BERAT</option>
                        <option value="Dalam Perbaikan"{{ old('kondisi', $asset->kondisi) == 'Dalam Perbaikan' ? ' selected' : '' }}>DALAM PERBAIKAN</option>
                        <option value="Tidak Aktif"{{ old('kondisi', $asset->kondisi) == 'Tidak Aktif' ? ' selected' : '' }}>TIDAK AKTIF</option>
                    </select>
                </div>
            </div>

            <div class="button-group">
                <button class="btn-ui btn-primary-ui" type="submit">Simpan Perubahan</button>
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