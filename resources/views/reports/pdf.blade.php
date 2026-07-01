@extends('layouts.app')

@section('title', 'Laporan Aset PDF - Frontend BULOG')
@section('topbar-meta', 'Tampilan cetak laporan aset')

@push('styles')
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/reports-index.css') }}">
@endpush

@section('content')
<section class="page-header">
    <div>
        <h1 class="page-title">Laporan Aset PDF</h1>
        <p class="page-lead">Cetak atau simpan laporan aset berdasarkan filter saat ini.</p>
    </div>
    <div class="button-group">
        <button class="btn-ui btn-primary-ui" onclick="window.print()">Print</button>
        <button class="btn-ui btn-secondary-ui" type="button" onclick="saveReportPdf()">Save PDF</button>
        <a href="{{ route('frontend.reports.index') }}" class="btn-ui btn-tertiary-ui">Kembali ke Laporan</a>
    </div>
</section>

<div id="report-pdf-container">
<section class="card-surface">
    <div class="card-surface__body">
        <p><strong>Total Asset Value:</strong> Rp {{ number_format($summary['total_asset_value'], 0, ',', '.') }}</p>
        <p><strong>Active Assets:</strong> {{ $summary['active_assets'] }}</p>
        <p><strong>Maintenance Required:</strong> {{ $summary['maintenance_required'] }}</p>
        <p><strong>Average Depreciation:</strong> {{ $summary['avg_depreciation'] }}%</p>
    </div>
</section>

<section class="card-surface">
    <div class="card-surface__body card-surface__body--no-top">
        <table class="table-ui">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Aset</th>
                    <th>Jenis</th>
                    <th>Nama Aset</th>
                    <th>Kondisi</th>
                    <th>PIC</th>
                    <th>Lokasi</th>
                    <th>Tanggal Perolehan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($assets as $index => $asset)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $asset->kode_aset }}</td>
                        <td>{{ ucfirst($asset->jenis) }}</td>
                        <td>{{ $asset->nama_aset }}</td>
                        <td>{{ $asset->kondisi }}</td>
                        <td>{{ $asset->pic_name ?? '-' }}</td>
                        <td>{{ $asset->lokasi }}</td>
                        <td>{{ optional($asset->tgl_perolehan)->format('Y-m-d') ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">Tidak ada data aset untuk filter ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

@push('scripts')
    <script>
        function saveReportPdf() {
            const printSettings = {
                title: document.title || 'Laporan Aset',
                margin: 0.5,
            };

            if (window.matchMedia('(print)').matches) {
                window.print();
                return;
            }

            const url = window.location.href;
            const fileName = 'laporan-aset-' + new Date().toISOString().slice(0, 10) + '.pdf';

            fetch(url, { cache: 'reload' })
                .then(response => response.text())
                .then(html => {
                    const printWindow = window.open('', '_blank');
                    printWindow.document.write(html);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => {
                        printWindow.print();
                    }, 250);
                })
                .catch(() => {
                    alert('Gagal membuat PDF. Silakan gunakan tombol Print sebagai alternatif.');
                });
        }
    </script>
@endpush
@endsection
