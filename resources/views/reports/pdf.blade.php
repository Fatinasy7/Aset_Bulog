<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Aset PDF</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 0.15in;
        }

        body {
            font-family: Arial, sans-serif;
            color: #111;
            margin: 0;
            padding: 0.15in;
            line-height: 1.15;
            font-size: 9.5px;
        }

        .report-header {
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .report-title {
            font-size: 1rem;
            margin-bottom: 0.15rem;
        }

        .report-subtitle {
            font-size: 0.78rem;
            color: #555;
            margin: 0;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 0.35rem;
            margin-bottom: 0.6rem;
        }

        .summary-card {
            border: 1px solid #d1d5db;
            padding: 0.4rem;
            border-radius: 5px;
            background: #fbfbfb;
        }

        .summary-label {
            margin: 0 0 0.15rem;
            font-size: 0.72rem;
            color: #475569;
        }

        .summary-value {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin-top: 0.35rem;
        }

        thead th {
            border-bottom: 1px solid #111;
            padding: 0.28rem 0.28rem;
            text-align: left;
            background: #f7f7f7;
            font-weight: 700;
        }

        tbody td {
            padding: 0.28rem 0.28rem;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: top;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        th:nth-child(1), td:nth-child(1) {
            width: 3%;
        }

        th:nth-child(2), td:nth-child(2) {
            width: 10%;
        }

        th:nth-child(3), td:nth-child(3) {
            width: 9%;
        }

        th:nth-child(4), td:nth-child(4) {
            width: 25%;
        }

        th:nth-child(5), td:nth-child(5) {
            width: 12%;
        }

        th:nth-child(6), td:nth-child(6) {
            width: 13%;
        }

        th:nth-child(7), td:nth-child(7) {
            width: 13%;
        }

        th:nth-child(8), td:nth-child(8) {
            width: 15%;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 0.5rem;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .empty-state-card {
            display: grid;
            place-items: center;
            gap: 0.5rem;
            padding: 1rem 1.2rem;
            border-radius: 0.75rem;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #374151;
            text-align: center;
        }

        .empty-state-card__icon {
            font-size: 1.5rem;
        }

        .empty-state-card__title {
            margin: 0;
            font-size: 0.95rem;
            font-weight: 700;
            color: #111827;
        }

        .empty-state-card__message {
            margin: 0;
            font-size: 0.8rem;
            line-height: 1.5;
            color: #4b5563;
            max-width: 24rem;
        }

        @media print {
            body {
                margin: 0.15in;
            }

            .summary-grid,
            table {
                page-break-inside: avoid;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
    </style>
</head>
<body>
    <header class="report-header">
        <h1 class="report-title">Laporan Aset BULOG</h1>
        <p class="report-subtitle">Ringkasan aset dan kondisi inventaris berdasarkan filter saat ini</p>
    </header>

    <section class="summary-grid">
        <div class="summary-card">
            <p class="summary-label">Total Asset Value</p>
            <p class="summary-value">Rp {{ number_format($summary['total_asset_value'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <p class="summary-label">Active Assets</p>
            <p class="summary-value">{{ $summary['active_assets'] }}</p>
        </div>
        <div class="summary-card">
            <p class="summary-label">Maintenance Required</p>
            <p class="summary-value">{{ $summary['maintenance_required'] }}</p>
        </div>
        <div class="summary-card">
            <p class="summary-label">Average Depreciation</p>
            <p class="summary-value">{{ $summary['avg_depreciation'] }}%</p>
        </div>
    </section>

    <section>
        <table>
            <thead>
                <tr>
                    <th style="width: 4%;">No</th>
                    <th style="width: 13%;">Kode Aset</th>
                    <th style="width: 12%;">Jenis</th>
                    <th style="width: 22%;">Nama Aset</th>
                    <th style="width: 12%;">Kondisi</th>
                    <th style="width: 13%;">PIC</th>
                    <th style="width: 12%;">Lokasi</th>
                    <th style="width: 12%;">Tanggal Perolehan</th>
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
                        <td colspan="8">
                            <div class="empty-state-card">
                                <div class="empty-state-card__icon">⚠️</div>
                                <div class="empty-state-card__title">Tidak ada data aset untuk filter ini</div>
                                <div class="empty-state-card__message">Filter yang dipilih tidak menghasilkan data. Ubah filter atau tambahkan aset baru agar laporan PDF menampilkan informasi.</div>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('Y-m-d H:i') }}</p>
    </div>
</body>
</html>
