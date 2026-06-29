@extends('layouts.app')

@section('title', 'Dashboard Utama - Frontend BULOG')
@section('topbar-meta', 'Ringkasan aset, kondisi, dan aktivitas operasional')

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Dashboard Utama</h1>
        <p class="page-lead">Ringkasan operasional aset yang dipakai untuk memantau total aset, kondisi, dan daftar aset bermasalah.</p>
    </div>
    <div>
        <a class="btn-ui btn-primary-ui" href="{{ route('frontend.assets.create') }}">Tambah Aset</a>
    </div>
</section>

<section class="stat-grid">
    <article class="stat-card">
        <p class="stat-label">Total Aset</p>
        <p class="stat-value">{{ $summary['total_assets'] }}</p>
        <p class="surface-note">Seluruh laptop dan printer aktif di sistem.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Laptop</p>
        <p class="stat-value">{{ $summary['total_laptops'] }}</p>
        <p class="surface-note">Termasuk unit operasional dan administrasi.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">Printer</p>
        <p class="stat-value">{{ $summary['total_printers'] }}</p>
        <p class="surface-note">Mencakup printer aktif per unit kerja.</p>
    </article>
    <article class="stat-card">
        <p class="stat-label">PIC Aktif</p>
        <p class="stat-value">{{ $summary['total_pics'] }}</p>
        <p class="surface-note">PIC yang sudah terhubung ke aset penanggung jawab.</p>
    </article>
</section>

<section class="component-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Grafik Kondisi Aset</strong>
        </div>
        <div class="card-surface__body">
            <div style="height: 260px; display:grid; place-items:center; border:1px dashed var(--color-border); border-radius: 16px; background: linear-gradient(180deg, rgba(31,94,154,0.03), rgba(215,38,56,0.02));">
                <div style="text-align:center; color: var(--color-muted);">
                    <div style="font-size:3rem; line-height:1;">◔</div>
                    <strong>Distribusi Kondisi Aset</strong>
                    <div style="display:grid; gap:0.35rem; margin-top:0.75rem;">
                        @forelse ($conditionCounts as $condition => $count)
                            <span>{{ $condition }}: {{ $count }}</span>
                        @empty
                            <span>Belum ada data aset.</span>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </article>

    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Grafik Jenis Aset</strong>
        </div>
        <div class="card-surface__body">
            <div style="height: 260px; display:grid; place-items:center; border:1px dashed var(--color-border); border-radius: 16px; background: linear-gradient(180deg, rgba(31,94,154,0.03), rgba(215,38,56,0.02));">
                <div style="text-align:center; color: var(--color-muted);">
                    <div style="font-size:3rem; line-height:1;">▤</div>
                    <strong>Komposisi Jenis Aset</strong>
                    <div style="display:grid; gap:0.35rem; margin-top:0.75rem;">
                        <span>Laptop: {{ $typeCounts['laptop'] ?? 0 }}</span>
                        <span>Printer: {{ $typeCounts['printer'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>
    </article>
</section>

<section class="card-surface">
    <div class="card-surface__header">
        <strong>Aset Bermasalah / Terbaru</strong>
    </div>
    <div class="card-surface__body">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Aset</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                    <th>PIC</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($problematicAssets->isNotEmpty() ? $problematicAssets : $recentAssets as $asset)
                    <tr>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td><span class="badge-ui badge-{{ str_replace(' ', '-', strtolower($asset->kondisi)) }}">{{ $asset->kondisi }}</span></td>
                        <td>{{ $asset->lokasi }}</td>
                        <td>{{ $asset->pic_name ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">Belum ada data aset.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection