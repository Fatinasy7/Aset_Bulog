<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
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
            'pic_name' => 'nullable|string|max:255',
        ]);

        $asset = Asset::create($validated);

        $this->recordAudit($asset, 'created', null, null, 'System');

        return response()->json($asset, Response::HTTP_CREATED);
    }

    public function show(Asset $asset)
    {
        return $asset;
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
            'pic_name' => 'nullable|string|max:255',
        ]);

        $originalValues = $asset->getOriginal();
        $asset->update($validated);

        foreach ($validated as $field => $newValue) {
            $oldValue = $originalValues[$field] ?? null;
            if ($oldValue !== $newValue) {
                $this->recordAudit($asset, 'updated', $field, (string) $oldValue, (string) $newValue, 'System');
            }
        }

        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        $this->recordAudit($asset, 'deleted', null, null, null, 'System');
        $asset->delete();

        return response()->json([ 'message' => 'Asset deleted successfully.' ]);
    }

    public function qrcode(Asset $asset)
    {
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

    public function storeWeb(Request $request)
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
            'pic' => 'nullable|exists:users,id',
            'pic_name' => 'nullable|string|max:255',
        ]);

        if ($request->filled('pic')) {
            $pic = User::find($request->input('pic'));
            if ($pic) {
                $validated['pic_name'] = $pic->name;
            }
        }

        $asset = Asset::create($validated);
        $this->recordAudit($asset, 'created', null, null, null, 'System');

        return redirect()->route('frontend.assets.index')->with('success', 'Aset berhasil ditambahkan.');
    }

    public function updateWeb(Request $request, Asset $asset)
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
            'pic' => 'nullable|exists:users,id',
            'pic_name' => 'nullable|string|max:255',
        ]);

        if ($request->filled('pic')) {
            $pic = User::find($request->input('pic'));
            if ($pic) {
                $validated['pic_name'] = $pic->name;
            }
        }

        $originalValues = $asset->getOriginal();
        $asset->update($validated);

        foreach ($validated as $field => $newValue) {
            $oldValue = $originalValues[$field] ?? null;
            if ($oldValue !== $newValue) {
                $this->recordAudit($asset, 'updated', $field, (string) $oldValue, (string) $newValue, 'System');
            }
        }

        return redirect()->route('frontend.assets.index')->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroyWeb(Asset $asset)
    {
        $this->recordAudit($asset, 'deleted', null, null, null, 'System');
        $asset->delete();

        return redirect()->route('frontend.assets.index')->with('success', 'Aset berhasil dihapus.');
    }

    private function recordAudit(Asset $asset, string $action, ?string $fieldName, ?string $oldValue, ?string $newValue, string $changedBy = 'System'): void
    {
        AuditLog::create([
            'asset_id' => $asset->id,
            'asset_code' => $asset->kode_aset,
            'action' => $action,
            'field_name' => $fieldName,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'changed_by' => $changedBy,
        ]);
    }
}
