<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\ApiResponseFormatter;
use App\Mail\DamageReportMail;
use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Notification;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetController extends Controller
{
    use ApiResponseFormatter;

    public function index(Request $request)
    {
        $query = $this->buildAssetQuery($request);

        return $query->get()->map(function (Asset $asset) {
            return $this->formatAssetPayload($asset);
        });
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
        $qrCodePath = $this->generateQrCode($asset);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'field_changed' => 'created',
            'old_value' => null,
            'new_value' => json_encode($asset->toArray()),
        ]);

        $this->recordAudit($asset, 'created', null, null, 'System');

        return response()->json($this->formatAssetPayload($asset), Response::HTTP_CREATED);
    }

    public function show(Asset $asset)
    {
        $asset->load('pic:id,nama,jabatan,email');

        return $this->formatAssetPayload($asset);
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

        $before = $asset->only(array_keys($validated));
        $originalValues = $asset->getOriginal();
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

        return response()->json($this->formatAssetPayload($asset));

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
        $previous = $asset->toArray();
        $this->recordAudit($asset, 'deleted', null, null, null, 'System');
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

    /**
     * Public web endpoint to return QR image inline so it can be embedded in <img>.
     */
    public function qrcodePublic(Asset $asset)
    {
        if (! $asset->qr_code_path || ! Storage::disk('local')->exists($asset->qr_code_path)) {
            abort(404);
        }

        $fullPath = storage_path('app/' . $asset->qr_code_path);
        $filename = basename($asset->qr_code_path);
        $contentType = str_ends_with($filename, '.svg') ? 'image/svg+xml' : 'image/png';

        return response()->file($fullPath, [
            'Content-Type' => $contentType,
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
            'asset' => $this->formatAssetPayload($asset->fresh()),
            'scannedAt' => $scannedAt,
        ]);
    }

    public function location(Asset $asset)
    {
        $lastScan = $asset->histories()
            ->where('field_changed', 'scan')
            ->latest('created_at')
            ->first();

        return response()->json([
            'assetId' => $asset->id,
            'lokasi' => $asset->lokasi,
            'latitude' => $asset->koordinat_lat,
            'longitude' => $asset->koordinat_lng,
            'lastScan' => $lastScan ? json_decode($lastScan->new_value, true) : null,
        ]);
    }

    protected function buildAssetQuery(Request $request)
    {
        $query = Asset::query()->with('pic:id,nama,jabatan,email')->orderBy('created_at', 'desc');

        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        if ($request->filled('lokasi')) {
            $query->where('lokasi', 'like', '%' . $request->lokasi . '%');
        }

        return $query;
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
        // generate and store QR code for this asset
        $this->generateQrCode($asset);
        $this->recordAudit($asset, 'created', null, null, null, 'System');

        $redirectRoute = $request->input('redirect_to');
        $allowedRedirects = [
            'frontend.assets.laptops',
            'frontend.assets.printers',
        ];

        if (! in_array($redirectRoute, $allowedRedirects, true)) {
            $redirectRoute = $validated['jenis'] === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops';
        }

        return redirect()->route($redirectRoute)->with('success', 'Aset berhasil ditambahkan.');
    }

    /**
     * Generate QR code SVG for an asset and store path on the model.
     * Returns the storage path.
     */
    private function generateQrCode(Asset $asset): string
    {
        $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ]);

        $filename = 'asset-' . $asset->id . '-' . time() . '.svg';
        $path = 'qrcodes/' . $filename;

        $svg = QrCode::format('svg')
            ->size(400)
            ->generate($payload);

        Storage::disk('local')->put($path, $svg);
        $asset->update(['qr_code_path' => $path]);

        return $path;
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

        $redirectRoute = $request->input('redirect_to');
        $allowedRedirects = [
            'frontend.assets.laptops',
            'frontend.assets.printers',
        ];

        if (! in_array($redirectRoute, $allowedRedirects, true)) {
            $redirectRoute = $asset->jenis === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops';
        }

        return redirect()->route($redirectRoute)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroyWeb(Request $request, Asset $asset)
    {
        $this->recordAudit($asset, 'deleted', null, null, null, 'System');
        $asset->delete();

        $redirectRoute = $request->input('redirect_to');
        $allowedRedirects = [
            'frontend.assets.laptops',
            'frontend.assets.printers',
        ];

        if (! in_array($redirectRoute, $allowedRedirects, true)) {
            $redirectRoute = $asset->jenis === 'printer' ? 'frontend.assets.printers' : 'frontend.assets.laptops';
        }

        return redirect()->route($redirectRoute)->with('success', 'Aset berhasil dihapus.');
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
