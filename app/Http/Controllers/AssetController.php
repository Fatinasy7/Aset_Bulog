<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::query()->orderBy('created_at', 'desc');

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        return $query->get();
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

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'field_changed' => 'created',
            'old_value' => null,
            'new_value' => json_encode($asset->toArray()),
        ]);

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
        ]);

        $before = $asset->only(array_keys($validated));
        $asset->update($validated);
        $after = $asset->only(array_keys($validated));

        $changed = [];
        foreach ($before as $key => $value) {
            if ($after[$key] !== $value) {
                $changed[$key] = ['old' => $value, 'new' => $after[$key]];
            }
        }

        if (! empty($changed)) {
            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'field_changed' => implode(',', array_keys($changed)),
                'old_value' => json_encode(array_combine(array_keys($changed), array_column($changed, 'old'))),
                'new_value' => json_encode(array_combine(array_keys($changed), array_column($changed, 'new'))),
            ]);
        }

        return response()->json($asset);
    }

    public function destroy(Asset $asset)
    {
        $previous = $asset->toArray();
        $asset->delete();

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'field_changed' => 'deleted',
            'old_value' => json_encode($previous),
            'new_value' => null,
        ]);

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
}
