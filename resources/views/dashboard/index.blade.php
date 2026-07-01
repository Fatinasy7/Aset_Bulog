@extends('layouts.app')

@section('title', 'Dashboard Utama - Frontend BULOG')
@section('topbar-meta', 'Ringkasan aset, kondisi, dan aktivitas operasional')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/dashboard.css') }}">
@endpush

@section('content')
<section class="page-header dashboard-header">
    <div>
        <h1 class="page-title">Dashboard</h1>
    </div>
    <div class="page-actions"></div>
</section>

<section class="dashboard-stats">
    <article class="dashboard-stat-card dashboard-stat-card--blue">
        <div class="dashboard-stat-card__head">
            <div>
                <p class="stat-label">Total Aset</p>
                <p class="stat-value">{{ $summary['total_assets'] }}</p>
            </div>
            <span class="dashboard-stat-icon">📦</span>
        </div>
    </article>
    <article class="dashboard-stat-card dashboard-stat-card--green">
        <div class="dashboard-stat-card__head">
            <div>
                <p class="stat-label">Laptop</p>
                <p class="stat-value">{{ $summary['total_laptops'] }}</p>
            </div>
            <span class="dashboard-stat-icon">💻</span>
        </div>
    </article>
    <article class="dashboard-stat-card dashboard-stat-card--cyan">
        <div class="dashboard-stat-card__head">
            <div>
                <p class="stat-label">Printer</p>
                <p class="stat-value">{{ $summary['total_printers'] }}</p>
            </div>
            <span class="dashboard-stat-icon">🖨️</span>
        </div>
    </article>
    <article class="dashboard-stat-card dashboard-stat-card--orange">
        <div class="dashboard-stat-card__head">
            <div>
                <p class="stat-label">PIC Aktif</p>
                <p class="stat-value">{{ $summary['total_pics'] }}</p>
            </div>
            <span class="dashboard-stat-icon">👤</span>
        </div>
    </article>
</section>

<section class="dashboard-grid">
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Kondisi Aset</strong>
        </div>
        <div class="card-surface__body">
            @php
                $conditionColors = [
                    'Baik' => '#22c55e',
                    'Rusak Ringan' => '#f59e0b',
                    'Rusak Berat' => '#ef4444',
                    'Dalam Perbaikan' => '#3b82f6',
                    'Tidak Aktif' => '#6b7280',
                ];
                $totalConditions = $conditionCounts->sum();
                $circumference = 2 * pi() * 40;
                $offset = 0;
            @endphp

            <div class="asset-condition-chart-wrapper">
                @if ($totalConditions > 0)
                    <svg viewBox="0 0 120 120" class="asset-condition-chart" aria-label="Diagram kondisi aset">
                        <g transform="rotate(-90 60 60)">
                            @foreach ($conditionCounts as $condition => $count)
                                @php
                                    $sliceLength = $circumference * ($count / $totalConditions);
                                    $gapLength = max($circumference - $sliceLength, 0);
                                @endphp
                                <circle
                                    r="40"
                                    cx="60"
                                    cy="60"
                                    fill="transparent"
                                    stroke="{{ $conditionColors[$condition] ?? '#94a3b8' }}"
                                    stroke-width="18"
                                    stroke-dasharray="{{ $sliceLength }} {{ $gapLength }}"
                                    stroke-dashoffset="{{ $offset }}"
                                />
                                @php $offset -= $sliceLength; @endphp
                            @endforeach
                        </g>
                        <circle cx="60" cy="60" r="32" fill="#fff" />
                        <text x="60" y="58" text-anchor="middle" font-size="12" fill="var(--color-text)" font-weight="700">Kondisi</text>
                        <text x="60" y="75" text-anchor="middle" font-size="10" fill="var(--color-muted)">Aset</text>
                    </svg>
                @else
                    <div class="text-center-muted">
                        <div class="placeholder-icon">◔</div>
                        <strong>Distribusi Kondisi Aset</strong>
                        <div>Tidak ada data aset.</div>
                    </div>
                @endif

                <div class="asset-condition-legend">
                    @forelse ($conditionCounts as $condition => $count)
                        @php $percentage = $totalConditions > 0 ? round($count / $totalConditions * 100) : 0; @endphp
                        <div class="asset-condition-legend__item">
                            <span class="asset-condition-legend__marker" style="background: {{ $conditionColors[$condition] ?? '#94a3b8' }}"></span>
                            <div>
                                <strong>{{ $condition }}</strong>
                                <div class="asset-condition-legend__meta">{{ $count }} Aset • {{ $percentage }}%</div>
                            </div>
                        </div>
                    @empty
                        <div>Tidak ada data kondisi.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </article>
    <article class="card-surface">
        <div class="card-surface__header">
            <strong>Distribusi Lokasi</strong>
        </div>
        <div class="card-surface__body">
            @php
                $locations = [
                    'Headquarters - Jakarta',
                    'Branch Office - Surabaya',
                    'R&D Lab - Bandung',
                    'Logistic Hub - Tangerang',
                ];
                $locationCounts = collect();
                foreach ($locations as $loc) {
                    $locationCounts->push([
                        'label' => $loc,
                        'count' => $assets->where('lokasi', $loc)->count(),
                    ]);
                }
                $maxLocationCount = max($locationCounts->pluck('count')->max() ?? 0, 1);
                $totalLocationItems = $locationCounts->pluck('count')->sum();
            @endphp

            @if ($totalLocationItems > 0)
                <div class="dashboard-location-list">
                    @foreach ($locationCounts as $item)
                        @php
                            $widthPct = $maxLocationCount > 0 ? round($item['count'] / $maxLocationCount * 100) : 0;
                            $relativePct = $totalLocationItems > 0 ? round($item['count'] / $totalLocationItems * 100) : 0;
                        @endphp
                        <div class="dashboard-location-row">
                            <div>
                                <span class="dashboard-location-label">{{ $item['label'] }}</span>
                                <div class="location-bar">
                                    <div class="location-bar__fill" style="width: {{ $widthPct }}%"></div>
                                </div>
                            </div>
                            <div class="dashboard-location-value">{{ $item['count'] }} Items</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center-muted">
                    <div class="placeholder-icon">▯</div>
                    <strong>Distribusi Lokasi</strong>
                    <div>Tidak ada data lokasi aset.</div>
                </div>
            @endif

            <p class="surface-note" style="margin-top:1rem;">Peningkatan distribusi aset sebesar 14% di Kantor Cabang Surabaya terdeteksi minggu ini.</p>
        </div>
    </article>
</section>
@endsection