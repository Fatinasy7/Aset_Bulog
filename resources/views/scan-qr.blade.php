@extends('layouts.app')

@section('title', 'Scan QR Code - Frontend BULOG')
@section('topbar-meta', 'Halaman Scan QR Code untuk aset BULOG')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Scan QR Code</h1>
        <p class="page-lead">Sistem scan QR untuk menampilkan detail aset dengan cepat.</p>
    </div>
    <div>
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.dashboard') }}">Kembali ke Dashboard</a>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body">
        <div class="stack" style="align-items:center; gap:1.5rem;">
            <div style="max-width:540px; width:100%;">
                <div class="card-surface__header">
                    <strong>Preview Scan QR</strong>
                </div>
                <div class="card-surface__body" style="text-align:center;">
                    <div style="margin:0 auto; width:260px; height:260px; border-radius:20px; border:2px dashed var(--color-border); display:grid; place-items:center; background:#f9fafb;">
                        <div style="color: var(--color-muted);">
                            <div style="font-size:3rem; line-height:1;">▣</div>
                            <p class="surface-note">Tampilan kamera / area scanning</p>
                        </div>
                    </div>
                    <p class="surface-note" style="margin-top:1rem;">Halaman ini akan menjadi tempat scan QR Code untuk mencari aset secara cepat.</p>
                </div>
            </div>

            <div style="max-width:540px; width:100%;">
                <div class="card-surface__header">
                    <strong>Hasil Scan</strong>
                </div>
                <div class="card-surface__body">
                    <div class="component-grid component-grid--full" style="gap:1rem;">
                        <div><strong>Kode Aset:</strong> AST-001</div>
                        <div><strong>Nama Aset:</strong> Laptop Operasional</div>
                        <div><strong>Kondisi:</strong> <span class="badge-ui badge-baik">Baik</span></div>
                        <div><strong>Lokasi:</strong> Ruang IT</div>
                        <div><strong>PIC:</strong> Andi Saputra</div>
                        <div><strong>Jenis:</strong> Laptop</div>
                    </div>
                    <p class="surface-note" style="margin-top:1rem;">Logika scan belum terhubung ke kamera atau API. Halaman ini berfungsi sebagai mockup awal.</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
