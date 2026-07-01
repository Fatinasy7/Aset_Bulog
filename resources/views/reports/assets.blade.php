<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Aset</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { border: 1px solid #333; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
        caption { font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .empty-state-card {
            display: grid;
            place-items: center;
            gap: 0.35rem;
            padding: 12px 14px;
            border-radius: 10px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            color: #334155;
            text-align: center;
            font-size: 12px;
        }
        .empty-state-card__icon {
            font-size: 1.15rem;
        }
        .empty-state-card__title {
            margin: 0;
            font-size: 13px;
            font-weight: 700;
            color: #111827;
        }
        .empty-state-card__message {
            margin: 0;
            line-height: 1.4;
            color: #475569;
        }
    </style>
</head>
<body>
    <h1>Laporan Aset</h1>
    <p>Dicetak pada: {{ now()->format('Y-m-d H:i:s') }}</p>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Kode Aset</th>
                <th>Nama Aset</th>
                <th>Merk / Tipe</th>
                <th>Lokasi</th>
                <th>Kondisi</th>
                <th>Tanggal Perolehan</th>
                <th>Jenis</th>
                <th>Harga</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($assets as $index => $asset)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $asset->kode_aset }}</td>
                    <td>{{ $asset->nama_aset }}</td>
                    <td>{{ $asset->merk_type }}</td>
                    <td>{{ $asset->lokasi }}</td>
                    <td>{{ $asset->kondisi }}</td>
                    <td>{{ optional($asset->tgl_perolehan)->format('Y-m-d') }}</td>
                    <td>{{ $asset->jenis }}</td>
                    <td>{{ $asset->harga }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state-card">
                            <div class="empty-state-card__icon">📦</div>
                            <div class="empty-state-card__title">Tidak ada aset untuk ditampilkan</div>
                            <div class="empty-state-card__message">Data aset kosong. Tambahkan aset atau periksa kembali filter dan parameter laporan.</div>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
