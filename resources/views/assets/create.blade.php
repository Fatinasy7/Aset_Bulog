@extends('layouts.app')

@section('title', 'Form Aset - Frontend BULOG')
@section('topbar-meta', 'Form tambah dan edit aset operasional')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Form Tambah / Edit Aset</h1>
        <p class="page-lead">Form ini dirancang untuk mengisi kode aset, jenis, merek, kondisi, lokasi, dan PIC secara terstruktur.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.index') }}">Kembali ke Daftar</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <form class="stack">
            <div class="component-grid">
                <div>
                    <label class="form-label-ui" for="kode_aset">Kode Aset</label>
                    <input class="form-control-ui" id="kode_aset" type="text" placeholder="AST-001" required>
                </div>
                <div>
                    <label class="form-label-ui" for="jenis">Jenis</label>
                    <select class="form-select-ui" id="jenis" required>
                        <option value="">Pilih jenis</option>
                        <option>Laptop</option>
                        <option>Printer</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="merek">Merek</label>
                    <input class="form-control-ui" id="merek" type="text" placeholder="Contoh: Lenovo" required>
                </div>
                <div>
                    <label class="form-label-ui" for="model">Model</label>
                    <input class="form-control-ui" id="model" type="text" placeholder="Contoh: ThinkPad X1" required>
                </div>
                <div>
                    <label class="form-label-ui" for="serial">Nomor Seri</label>
                    <input class="form-control-ui" id="serial" type="text" placeholder="Serial number">
                </div>
                <div>
                    <label class="form-label-ui" for="kondisi">Kondisi</label>
                    <select class="form-select-ui" id="kondisi" required>
                        <option value="">Pilih kondisi</option>
                        <option>BAIK</option>
                        <option>RUSAK RINGAN</option>
                        <option>RUSAK BERAT</option>
                        <option>DALAM_PERBAIKAN</option>
                        <option>TIDAK_AKTIF</option>
                    </select>
                </div>
                <div>
                    <label class="form-label-ui" for="lokasi">Lokasi</label>
                    <input class="form-control-ui" id="lokasi" type="text" placeholder="Ruang kerja / unit" required>
                </div>
                <div>
                    <label class="form-label-ui" for="pic">PIC</label>
                    <select class="form-select-ui" id="pic" required>
                        <option value="">Pilih PIC</option>
                        <option>Andi</option>
                        <option>Sari</option>
                        <option>Rudi</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="form-label-ui" for="keterangan">Keterangan</label>
                <textarea class="form-control-ui" id="keterangan" rows="4" placeholder="Catatan kondisi atau detail tambahan"></textarea>
            </div>

            <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
                <button class="btn-ui btn-primary-ui" type="submit">Simpan</button>
                <button class="btn-ui btn-secondary-ui" type="reset">Batal</button>
            </div>
        </form>
    </div>
</section>
@endsection