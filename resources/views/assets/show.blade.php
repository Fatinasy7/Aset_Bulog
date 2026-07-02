@extends('layouts.app')

@section('title', 'Detail Aset - Frontend BULOG')
@section('topbar-meta', 'Informasi lengkap aset, QR code, dan riwayat singkat')

@push('styles')
    @vite(['resources/css/assets-show.css'])
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Detail Aset</h1>
    </div>
    <div class="action-row">
        <a class="btn-ui btn-secondary-ui" href="{{ route($asset->jenis === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops') }}">Kembali</a>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.edit', $asset) }}">Edit Aset</a>
        <form method="POST" action="{{ route('frontend.assets.destroy', $asset) }}" class="inline-form">
            @csrf
            @method('DELETE')
            <input type="hidden" name="redirect_to" value="{{ route($asset->jenis === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops') }}">
            <button class="btn-ui btn-danger-ui" type="submit" onclick="return confirm('Hapus aset ini?')">Hapus Aset</button>
        </form>
    </div>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Informasi Aset</strong>
        </div>
        <div class="card-surface__body stack">
            <div class="component-grid component-grid--full component-grid--compact">
                <div><strong>Kode Aset:</strong> {{ $asset->kode_aset }}</div>
                <div><strong>Nama Aset:</strong> {{ $asset->nama_aset }}</div>
                <div><strong>Jenis:</strong> {{ ucfirst($asset->jenis) }}</div>
                <div><strong>Merek / Model:</strong> {{ $asset->merk_type }}</div>
                <div><strong>Kondisi:</strong> <span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></div>
                <div><strong>Lokasi:</strong> {{ $asset->lokasi }}</div>
                <div><strong>PIC:</strong> {{ $asset->pic_name ?? '-' }}</div>
            </div>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>QR Code</strong>
        </div>
        <div class="card-surface__body card-surface__body--centered">
            @if($asset->qr_code_path)
                <div>
                    <img src="{{ route('frontend.assets.qrcode', $asset) }}" alt="QR {{ $asset->kode_aset }}" style="max-width:220px; height:auto; display:block; margin:0 auto;"/>
                    <p class="surface-note mt-2">QR untuk {{ $asset->nama_aset }} ({{ $asset->kode_aset }}).</p>
                </div>
            @else
                <div class="placeholder-box placeholder-box--qr">
                    <div>
                        <div class="placeholder-icon">▣</div>
                        <strong>{{ $asset->kode_aset }}</strong>
                        <p class="surface-note">QR preview untuk {{ $asset->nama_aset }}.</p>
                    </div>
                </div>
            @endif
        </div>
    </article>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Lokasi Terakhir</strong>
        </div>
            <div class="card-surface__body">
                @if($asset->koordinat_lat && $asset->koordinat_lng)
                    <div class="stack">
                        <div><strong>Koordinat:</strong> {{ $asset->koordinat_lat }}, {{ $asset->koordinat_lng }}</div>
                        @php
                            $lastScan = $asset->histories()->where('field_changed', 'scan')->latest('created_at')->first();
                            $lastScanData = $lastScan ? json_decode($lastScan->new_value, true) : null;
                        @endphp
                        @if($lastScanData)
                            <div><strong>Terakhir dipindai pada:</strong> {{ $lastScanData['scanned_at'] ?? $lastScan->created_at }}</div>
                        @else
                            <div class="surface-note">Belum ada riwayat pemindaian lokasi.</div>
                        @endif

                        <div id="asset-map" data-lat="{{ $asset->koordinat_lat }}" data-lng="{{ $asset->koordinat_lng }}" data-popup="{{ e($asset->nama_aset . ' (' . $asset->kode_aset . ')') }}" style="height:320px; margin-top:12px; border-radius:6px; overflow:hidden;"></div>
                    </div>
                @else
                    <div class="placeholder-box placeholder-box--chart">
                        <div>
                            <strong>Map / Koordinat Placeholder</strong>
                            <p class="surface-note">Area ini disediakan untuk peta atau koordinat lokasi terakhir aset.</p>
                        </div>
                    </div>
                @endif
            </div>
    </article>
</section>
@push('scripts')
    @vite(['resources/js/asset-map.js'])
@endpush

@endsection