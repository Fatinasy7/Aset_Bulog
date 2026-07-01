<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('pic')->orderBy('created_at', 'desc')->get()->map(function ($asset) {
            return $this->formatAssetPayload($asset);
        });

        return response()->json($assets);
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
        $qrCodePath = $this->generateQrCode($asset);

        $payload = $this->formatAssetPayload($asset, $qrCodePath);

        return response()->json($payload, Response::HTTP_CREATED);
    }

    public function show(Asset $asset)
    {
        return response()->json($this->formatAssetPayload($asset));
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

        return response()->json($this->formatAssetPayload($asset));
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return response()->json([ 'message' => 'Asset deleted successfully.' ]);
    }

    public function assignPic(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'pic_id' => ['required', 'exists:users,id'],
            'alasan' => ['nullable', 'string', 'max:255'],
        ]);

        $pic = User::findOrFail($validated['pic_id']);
        $asset->update(['pic_id' => $pic->id]);

        return response()->json($this->formatAssetPayload($asset->fresh('pic')));
    }

    public function qrcode(Asset $asset)
    {
        $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ]);

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="300" viewBox="0 0 300 300">' .
            '<rect width="300" height="300" fill="white"/>' .
            '<text x="150" y="140" text-anchor="middle" font-family="Arial" font-size="18">' . e($asset->kode_aset) . '</text>' .
            '<text x="150" y="170" text-anchor="middle" font-family="Arial" font-size="14">' . e($asset->nama_aset) . '</text>' .
            '<rect x="70" y="70" width="160" height="160" fill="none" stroke="black" stroke-width="2"/>' .
            '<text x="150" y="260" text-anchor="middle" font-family="Arial" font-size="12">' . e($payload) . '</text>' .
            '</svg>';

        return response($svg, Response::HTTP_OK)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="asset-' . $asset->id . '.svg"');
    }

    protected function formatAssetPayload(Asset $asset, ?string $qrCodePath = null): array
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
            'qrCodePath' => $qrCodePath,
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

    protected function generateQrCode(Asset $asset): string
    {
        $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ]);

        $filename = 'qrcodes/asset-' . $asset->id . '-' . now()->timestamp . '.svg';
        Storage::disk('public')->put($filename, $payload);

        return $filename;
    }
}
