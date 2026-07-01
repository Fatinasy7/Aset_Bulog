<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ScanController extends Controller
{
    public function store(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'asset_id' => ['nullable', 'exists:assets,id'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'scanned_by' => ['nullable', 'string', 'max:255'],
            'scanned_at' => ['nullable', 'date'],
        ]);

        $asset->update([
            'koordinat_lat' => $validated['latitude'] ?? $asset->koordinat_lat,
            'koordinat_lng' => $validated['longitude'] ?? $asset->koordinat_lng,
        ]);

        $scannedAtValue = $validated['scanned_at'] ?? now();
        $scannedAt = $scannedAtValue instanceof \DateTimeInterface
            ? $scannedAtValue->format('Y-m-d H:i:s')
            : \Carbon\Carbon::parse($scannedAtValue)->format('Y-m-d H:i:s');

        return response()->json([
            'message' => 'Scan berhasil, lokasi aset diperbarui.',
            'asset' => [
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
                'picId' => null,
                'pic' => null,
                'createdAt' => $asset->created_at?->toISOString(),
                'updatedAt' => $asset->updated_at?->toISOString(),
            ],
            'scannedAt' => $scannedAt,
        ], Response::HTTP_OK);
    }
}
