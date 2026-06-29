@extends('layouts.app')

@section('title', 'Form PIC - Frontend BULOG')
@section('topbar-meta', 'Form tambah dan edit penanggung jawab aset')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Form PIC</h1>
        <p class="page-lead">Form ini dipakai untuk menambahkan atau memperbarui data PIC yang bertanggung jawab atas aset.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.pics.index') }}">Kembali ke Daftar</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <form class="stack">
            <div class="component-grid">
                <div>
                    <label class="form-label-ui" for="nama">Nama</label>
                    <input class="form-control-ui" id="nama" type="text" placeholder="Nama PIC" required>
                </div>
                <div>
                    <label class="form-label-ui" for="jabatan">Jabatan</label>
                    <input class="form-control-ui" id="jabatan" type="text" placeholder="Jabatan PIC" required>
                </div>
                <div>
                    <label class="form-label-ui" for="email">Email</label>
                    <input class="form-control-ui" id="email" type="email" placeholder="nama@bulog.co.id" required>
                </div>
                <div>
                    <label class="form-label-ui" for="telepon">Nomor Telepon</label>
                    <input class="form-control-ui" id="telepon" type="tel" placeholder="08xx-xxxx-xxxx" required>
                </div>
            </div>

            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <button class="btn-ui btn-primary-ui" type="submit">Simpan</button>
                <button class="btn-ui btn-secondary-ui" type="reset">Batal</button>
            </div>
        </form>

        <div class="card-surface__body" style="padding-left: 0; padding-right: 0; padding-bottom: 0;">
            <p class="surface-note">PIC yang sudah terdaftar di backend:</p>
            <ul>
                @forelse ($pics as $pic)
                    <li>{{ $pic->name }} - {{ $pic->email }}</li>
                @empty
                    <li>Belum ada PIC tersedia.</li>
                @endforelse
            </ul>
        </div>
    </div>
</section>
@endsection