@extends('layouts.app')

@section('title', 'Detail Aset - Frontend BULOG')
@section('topbar-meta', 'Informasi lengkap aset, QR code, dan riwayat singkat')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Detail Aset</h1>
        <p class="page-lead">Halaman detail menampilkan informasi lengkap aset, lokasi terakhir, QR code, serta riwayat singkat aset.</p>
    </div>
    <div style="display:flex; gap:0.75rem; flex-wrap:wrap;">
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.index') }}">Kembali</a>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.create') }}">Edit Aset</a>
    </div>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Informasi Aset</strong>
        </div>
        <div class="card-surface__body stack">
            <div class="component-grid component-grid--full" style="gap:0.75rem;">
                <div><strong>Kode Aset:</strong> AST-001</div>
                <div><strong>Nama Aset:</strong> Laptop Operasional</div>
                <div><strong>Jenis:</strong> Laptop</div>
                <div><strong>Merek / Model:</strong> Lenovo ThinkPad X1</div>
                <div><strong>Nomor Seri:</strong> PF2K4R8J</div>
                <div><strong>Kondisi:</strong> <span class="badge-ui badge-baik">Baik</span></div>
                <div><strong>Lokasi:</strong> Ruang IT</div>
                <div><strong>PIC:</strong> Andi</div>
            </div>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>QR Code</strong>
        </div>
        <div class="card-surface__body" style="display:grid; place-items:center; min-height: 320px;">
            <div style="width:220px; height:220px; border-radius:18px; border:2px dashed var(--color-border); background:#fff; display:grid; place-items:center; color: var(--color-muted); text-align:center; padding:1rem;">
                <div>
                    <div style="font-size:3rem; line-height:1;">▣</div>
                    <strong>QR Placeholder</strong>
                    <p class="surface-note">Area ini siap diganti generator QR ketika integrasi backend atau library dihubungkan.</p>
                </div>
            </div>
        </div>
    </article>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Riwayat Singkat</strong>
        </div>
        <div class="card-surface__body">
            <table class="table-ui">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                        <th>Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>2026-06-20</td><td>Update kondisi aset</td><td>Andi</td></tr>
                    <tr><td>2026-06-18</td><td>Perubahan lokasi</td><td>Rina</td></tr>
                    <tr><td>2026-06-12</td><td>Penetapan PIC</td><td>Admin IT</td></tr>
                </tbody>
            </table>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Lokasi Terakhir</strong>
        </div>
        <div class="card-surface__body">
            <div style="min-height: 260px; border-radius: 16px; border:1px dashed var(--color-border); background: linear-gradient(135deg, rgba(31,94,154,0.04), rgba(215,38,56,0.02)); display:grid; place-items:center; color: var(--color-muted); text-align:center; padding:1rem;">
                <div>
                    <strong>Map / Koordinat Placeholder</strong>
                    <p class="surface-note">Area ini disediakan untuk peta atau koordinat lokasi terakhir aset.</p>
                </div>
            </div>
        </div>
    </article>
</section>
@endsection