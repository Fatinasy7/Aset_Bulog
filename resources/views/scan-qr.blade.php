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
        <div class="qr-hero">
            <div class="qr-hero__content">
                <h2>Asset QR Scanner</h2>
                <p class="surface-note">Search assets, nodes, or locations with quick QR scanning.</p>
            </div>
            <button class="btn-ui btn-primary-ui">Scan Now</button>
        </div>

        <div class="card-surface__body card-surface__body--centered">
            <div class="placeholder-box placeholder-box--qr placeholder-box--wide">
                <div class="text-center-muted">
                    <div class="placeholder-icon">▣</div>
                    <p class="surface-note">Mock QR scanner preview</p>
                </div>
            </div>
        </div>

        <div class="card-surface__body">
            <div class="component-grid component-grid--full component-grid--compact">
                <div><strong>Kode Aset:</strong> AST-001</div>
                <div><strong>Nama Aset:</strong> Laptop Operasional</div>
                <div><strong>Kondisi:</strong> <span class="badge-ui badge-baik">Baik</span></div>
                <div><strong>Lokasi:</strong> Ruang IT</div>
                <div><strong>PIC:</strong> Andi Saputra</div>
                <div><strong>Jenis:</strong> Laptop</div>
            </div>
            <p class="surface-note mt-1">Logika scan belum terhubung ke kamera atau API. Halaman ini berfungsi sebagai mockup awal.</p>
        </div>
    </div>
</section>
@endsection
