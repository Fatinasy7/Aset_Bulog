<?php

namespace App\Http\Controllers;

use App\Mail\DamageReportMail;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

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

        $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ]);

        $filename = 'asset-' . $asset->id . '-' . time() . '.svg';
        $path = 'qrcodes/' . $filename;
        $svg = QrCode::format('svg')->size(400)->generate($payload);
        Storage::disk('local')->put($path, $svg);

        $asset->update(['qr_code_path' => $path]);

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

        if (isset($changed['kondisi'])) {
            $newCondition = strtolower(trim($after['kondisi']));
            if (str_contains($newCondition, 'rusak')) {
                $adminUsers = User::where('role', 'admin_it')->get();
                foreach ($adminUsers as $admin) {
                    Mail::mailer('log')->to($admin->email)->send(new DamageReportMail($asset, $before['kondisi'] ?? null, $after['kondisi']));
                    Notification::create([
                        'user_id' => $admin->id,
                        'role' => 'admin_it',
                        'title' => 'Laporan Kerusakan Aset',
                        'message' => "Aset {$asset->kode_aset} dilaporkan dengan kondisi {$after['kondisi']}.",
                        'data' => [
                            'asset_id' => $asset->id,
                            'old_condition' => $before['kondisi'] ?? null,
                            'new_condition' => $after['kondisi'],
                        ],
                    ]);
                }
            }
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
        $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ]);

        if ($asset->qr_code_path && Storage::disk('local')->exists($asset->qr_code_path)) {
            $filename = basename($asset->qr_code_path);
            $contentType = str_ends_with($filename, '.svg') ? 'image/svg+xml' : 'image/png';

            return response()->download(storage_path('app/' . $asset->qr_code_path), $filename, [
                'Content-Type' => $contentType,
            ]);
        }

        $filename = 'asset-' . $asset->id . '-' . time() . '.svg';
        $path = 'qrcodes/' . $filename;

        $svg = QrCode::format('svg')
            ->size(400)
            ->generate($payload);

        Storage::disk('local')->put($path, $svg);
        $asset->update(['qr_code_path' => $path]);

        return response()->download(storage_path('app/' . $path), $filename, [
            'Content-Type' => 'image/svg+xml',
        ]);
    }

    public function scan(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'scanned_at' => 'nullable|date',
            'scanned_by' => 'nullable|integer|exists:users,id',
        ]);

        $scannedBy = $validated['scanned_by'] ?? Auth::id();
        $scannedAt = $validated['scanned_at'] ?? now()->toDateTimeString();

        $oldCoordinates = [
            'latitude' => $asset->koordinat_lat,
            'longitude' => $asset->koordinat_lng,
        ];

        $asset->update([
            'koordinat_lat' => $validated['latitude'],
            'koordinat_lng' => $validated['longitude'],
        ]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => $scannedBy,
            'field_changed' => 'scan',
            'old_value' => json_encode($oldCoordinates),
            'new_value' => json_encode([
                'latitude' => $validated['latitude'],
                'longitude' => $validated['longitude'],
                'scanned_at' => $scannedAt,
            ]),
        ]);

        return response()->json([
            'message' => 'Scan berhasil, lokasi aset diperbarui.',
            'asset' => $asset->fresh(),
            'scanned_at' => $scannedAt,
        ]);
    }

    public function location(Asset $asset)
    {
        $lastScan = $asset->histories()
            ->where('field_changed', 'scan')
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'asset_id' => $asset->id,
            'lokasi' => $asset->lokasi,
            'latitude' => $asset->koordinat_lat,
            'longitude' => $asset->koordinat_lng,
            'last_scan' => $lastScan ? json_decode($lastScan->new_value, true) : null,
        ]);
    }
}
