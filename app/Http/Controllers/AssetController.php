<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AssetController extends Controller
{
    public function index()
    {
        return Asset::orderBy('created_at', 'desc')->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_aset' => 'required|string|unique:assets,kode_aset',
            'nama_aset' => 'required|string|max:255',
            'merk_type' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'koordinat_lat' => 'nullable|numeric',
            'koordinat_lng' => 'nullable|numeric',
            'kondisi' => 'required|string|max:100',
            'tgl_perolehan' => 'nullable|date',
            'harga' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:laptop,printer',
        ]);

        $asset = Asset::create($validated);

        return response()->json($asset, Response::HTTP_CREATED);
    }

    protected function resolveAsset($assetParam)
    {
        return Asset::where('id', $assetParam)
            ->orWhere('kode_aset', $assetParam)
            ->firstOrFail();
    }

    public function show($asset)
    {
        return $this->resolveAsset($asset);
    }

    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'kode_aset' => 'required|string|unique:assets,kode_aset,' . $asset->id,
            'nama_aset' => 'required|string|max:255',
            'merk_type' => 'required|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'lokasi' => 'required|string|max:255',
            'koordinat_lat' => 'nullable|numeric',
            'koordinat_lng' => 'nullable|numeric',
            'kondisi' => 'required|string|max:100',
            'tgl_perolehan' => 'nullable|date',
            'harga' => 'nullable|integer|min:0',
            'keterangan' => 'nullable|string',
            'jenis' => 'required|in:laptop,printer',
        ]);

        $asset->update($validated);

        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return response()->json([ 'message' => 'Asset deleted successfully.' ]);
    }

    public function scan(Request $request, $asset)
    {
        $asset = $this->resolveAsset($asset);

        $validated = $request->validate([
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'scanned_at' => 'nullable|date_format:Y-m-d\TH:i:sP'
        ]);

        if (isset($validated['latitude'])) {
            $asset->koordinat_lat = $validated['latitude'];
        }
        if (isset($validated['longitude'])) {
            $asset->koordinat_lng = $validated['longitude'];
        }
        $asset->save();

        return response()->json($asset);
    }

    public function qrcode($asset)
    {
        $asset = $this->resolveAsset($asset);

        $payload = [
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ];

        return response()->json([
            'asset' => $asset,
            'qr_text' => json_encode($payload),
            'qr_data' => base64_encode(json_encode($payload)),
        ]);
    }
}
