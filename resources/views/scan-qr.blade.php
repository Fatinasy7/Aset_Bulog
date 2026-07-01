@extends('layouts.app')

@section('title', 'Scan QR Code - Frontend BULOG')
@section('topbar-meta', 'Halaman Scan QR Code untuk aset BULOG')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/scan-qr.css') }}">
@endpush

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
                <p class="surface-note">Scan kode QR atau ketik kode aset untuk melihat detail aset secara cepat.</p>
            </div>
            <div class="qr-hero__actions">
                <button id="start-camera" class="btn-ui btn-primary-ui" type="button">Mulai Kamera</button>
                <button id="stop-camera" class="btn-ui btn-secondary-ui" type="button" disabled>Hentikan Kamera</button>
            </div>
        </div>

        <div id="qr-preview" class="scan-preview visually-hidden card-surface__body">
            <div class="scan-preview__content">
                <p class="surface-note">Preview aset setelah scan atau lookup manual</p>
                <h3 id="preview-nama" class="scan-preview__title"></h3>
                <div class="scan-preview__details component-grid component-grid--full component-grid--compact">
                    <div><strong>Kode:</strong> <span id="preview-kode"></span></div>
                    <div><strong>Jenis:</strong> <span id="preview-jenis"></span></div>
                </div>
            </div>
        </div>

        <div class="card-surface__body">
            <form id="qr-lookup-form" action="{{ route('frontend.scan-qr.lookup') }}" method="POST" class="component-grid component-grid--full component-grid--compact" novalidate>
                @csrf

                @if ($errors->any())
                    <div class="alert-ui alert-danger mb-4">
                        <strong>Periksa kembali input QR:</strong>
                        <ul class="list-unstyled mt-2 mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div>
                    <label class="form-label-ui" for="qr_input">Kode QR / Kode Aset</label>
                    <input id="qr_input" class="form-control-ui {{ $errors->has('qr_text') ? 'is-invalid' : '' }}" type="text" name="qr_text" value="{{ old('qr_text') }}" placeholder="Masukkan kode aset atau hasil scan QR" autocomplete="off">
                    @error('qr_text')
                        <p class="form-error">{{ $message }}</p>
                    @enderror
                    <p id="qr_error" class="form-error visually-hidden"></p>
                </div>
                <div class="qr-buttons">
                    <button id="submit-qr" class="btn-ui btn-primary-ui" type="submit">Cari Aset</button>
                    <button id="reset-qr" class="btn-ui btn-secondary-ui" type="button">Reset</button>
                </div>
            </form>

            <div id="qr-status" class="scan-status surface-note mt-1">Gunakan kamera atau masukkan kode aset secara manual.</div>

            <div id="camera-preview" class="scan-camera-preview visually-hidden">
                <video id="qr-video" autoplay muted playsinline></video>
                <canvas id="qr-canvas" class="visually-hidden"></canvas>
            </div>

            <div id="qr-result" class="scan-result visually-hidden">
                <div class="scan-result__header">
                    <h3>Hasil Scan</h3>
                    <span id="qr-result-code"></span>
                </div>
                <div class="scan-result__body component-grid component-grid--full component-grid--compact">
                    <div><strong>Kode Aset:</strong> <span id="result-kode"></span></div>
                    <div><strong>Nama Aset:</strong> <span id="result-nama"></span></div>
                    <div><strong>Kondisi:</strong> <span id="result-kondisi"></span></div>
                    <div><strong>Lokasi:</strong> <span id="result-lokasi"></span></div>
                    <div><strong>PIC:</strong> <span id="result-pic"></span></div>
                    <div><strong>Jenis:</strong> <span id="result-jenis"></span></div>
                </div>
                <div class="scan-result__footer">
                    <a id="result-detail-link" href="#" class="btn-ui btn-secondary-ui">Lihat Detail Aset</a>
                </div>
            </div>
        </div>
    </div>
</section>

@push('scripts')
    @vite(['resources/js/scan-qr.js'])
@endpush
@endsection
