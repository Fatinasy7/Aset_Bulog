<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReportController extends Controller
{
    public function assets(Request $request)
    ini_set('memory_limit', '256M');
        set_time_limit(300);
        $query = Asset::query();
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        if ($request->filled('pic_id')) {
            $query->where('pic_id', $request->pic_id);
        }

        $assets = $query->with('pic')->orderBy('created_at', 'desc')->get();
        $payload = $assets->map(function (Asset $asset) {
            return $this->formatAssetPayload($asset);
        });

        if ($request->input('format') === 'excel') {
            $filename = 'asset-report-' . now()->format('YmdHis') . '.xlsx';
            $path = storage_path('app/public/' . $filename);
            $directory = dirname($path);

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $content = $this->buildExcelContent($payload);
            file_put_contents($path, $content);

            return response()->download($path, $filename)->deleteFileAfterSend(true);
        }

        if ($request->input('format') === 'pdf') {
            $filename = 'asset-report-' . now()->format('YmdHis') . '.pdf';
            $path = storage_path('app/public/' . $filename);
            $directory = dirname($path);

            if (!is_dir($directory)) {
                mkdir($directory, 0777, true);
            }

            $content = $this->buildPdfContent($payload);
            file_put_contents($path, $content);

            return response()->download($path, $filename)->deleteFileAfterSend(true);
        }

        return response()->json($payload);
    }

    protected function formatAssetPayload(Asset $asset): array
    {
        return [
            'id' => $asset->id,
            'kodeAset' => $asset->kode_aset,
            'namaAset' => $asset->nama_aset,
            'merkType' => $asset->merk_type,
            'serialNumber' => $asset->serial_number,
            'lokasi' => $asset->lokasi,
            'koordinat' => [
                'lat' => $asset->koordinat_lat,
                'lng' => $asset->koordinat_lng,
            ],
            'kondisi' => $asset->kondisi,
            'tglPerolehan' => $asset->tgl_perolehan?->toDateString(),
            'harga' => $asset->harga,
            'keterangan' => $asset->keterangan,
            'jenis' => $asset->jenis,
            'qrCodePath' => null,
            'picId' => $asset->pic_id,
            'pic' => $asset->pic ? [
                'id' => $asset->pic->id,
                'nama' => $asset->pic->name,
                'email' => $asset->pic->email,
                'jabatan' => $asset->pic->role,
            ] : null,
            'createdAt' => $asset->created_at?->toISOString(),
            'updatedAt' => $asset->updated_at?->toISOString(),
        ];
    }

    protected function buildExcelContent($payload): string
    {
        $headers = [
            'ID', 'Kode Aset', 'Nama Aset', 'Merk/Type', 'Serial Number', 'Lokasi', 'Kondisi', 'Jenis', 'PIC', 'Created At',
        ];

        $rows = [$headers];
        foreach ($payload as $asset) {
            $rows[] = [
                $asset['id'],
                $asset['kodeAset'],
                $asset['namaAset'],
                $asset['merkType'],
                $asset['serialNumber'],
                $asset['lokasi'],
                $asset['kondisi'],
                $asset['jenis'],
                $asset['pic']['nama'] ?? '',
                $asset['createdAt'],
            ];
        }

        $lines = [];
        foreach ($rows as $row) {
            $lines[] = implode(',', array_map(function ($value) {
                $escaped = str_replace('"', '""', (string) $value);
                return Str::contains((string) $value, [',', '"', "\n"]) ? '"' . $escaped . '"' : $escaped;
            }, $row));
        }

        return implode(PHP_EOL, $lines) . PHP_EOL;
    }

    protected function buildPdfContent($payload): string
    {
        $lines = [
            '%PDF-1.4',
            '1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj',
            '2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj',
            '3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj',
            '4 0 obj << /Length 0 >> stream',
            'endstream endobj',
            '5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj',
            'xref',
            '0 6',
            '0000000000 65535 f ',
            '0000000010 00000 n ',
            '0000000062 00000 n ',
            '0000000119 00000 n ',
            '0000000207 00000 n ',
            '0000000300 00000 n ',
            'trailer << /Root 1 0 R /Size 6 >>',
            'startxref',
            '0',
            '%%EOF',
        ];

        $content = "Asset Report\n\n";
        foreach ($payload as $asset) {
            $content .= $asset['kodeAset'] . ' - ' . $asset['namaAset'] . ' (' . $asset['kondisi'] . ")\n";
        }

        return implode(PHP_EOL, $lines) . PHP_EOL . $content;
    }
}
