@extends('layouts.app')

@section('title', 'Detail Aset - Frontend BULOG')
@section('topbar-meta', 'Informasi lengkap aset, QR code, dan riwayat singkat')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Detail Aset</h1>
        <p class="page-lead">Halaman detail menampilkan informasi lengkap aset, lokasi terakhir, QR code, serta riwayat singkat aset.</p>
    </div>
    <div class="action-row">
        <a class="btn-ui btn-secondary-ui" href="{{ route('frontend.assets.index') }}">Kembali</a>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.edit', $asset) }}">Edit Aset</a>
        <form method="POST" action="{{ route('frontend.assets.destroy', $asset) }}" class="inline-form">
            @csrf
            @method('DELETE')
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
                <div><strong>Nomor Seri:</strong> {{ $asset->serial_number ?? '-' }}</div>
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
            <div class="placeholder-box placeholder-box--qr">
                <div>
                    <div class="placeholder-icon">▣</div>
                    <strong>{{ $asset->kode_aset }}</strong>
                    <p class="surface-note">QR preview untuk {{ $asset->nama_aset }}.</p>
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
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d') }}</td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->changed_by }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">Belum ada riwayat perubahan untuk aset ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Lokasi Terakhir</strong>
        </div>
        <div class="card-surface__body">
            <div class="placeholder-box placeholder-box--chart">
                <div>
                    <strong>Map / Koordinat Placeholder</strong>
                    <p class="surface-note">Area ini disediakan untuk peta atau koordinat lokasi terakhir aset.</p>
                </div>
            </div>
        </div>
    </article>
</section>
@endsection