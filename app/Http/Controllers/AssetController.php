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
        $asset->update(['qr_code_path' => $qrCodePath]);

        AssetHistory::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'field_changed' => 'created',
            'old_value' => null,
            'new_value' => json_encode($asset->toArray()),
        ]);

        $this->recordAudit($asset, 'created', null, null, null, 'System');

        return response()->json($this->formatAssetPayload($asset), Response::HTTP_CREATED);
    }

    public function show(Asset $asset)
    {
        $asset->load('pic:id,name,role,email,telepon');

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

        foreach ($validated as $field => $newValue) {
            $oldValue = $originalValues[$field] ?? null;
            if ($oldValue !== $newValue) {
                $this->recordAudit($asset, 'updated', $field, (string) $oldValue, (string) $newValue, 'System');
            }
        }

        return response()->json($this->formatAssetPayload($asset));
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

    public function qrcodeLabel(Asset $asset)
    {
        $svg = $this->generateQrCodeLabelSvg($asset);
        $filename = 'asset-label-' . $asset->id . '-' . time() . '.svg';

        return response($svg, Response::HTTP_OK, [
            'Content-Type' => 'image/svg+xml',
            'Content-Disposition' => 'attachment; filename=' . $filename,
        ]);
    }

    public function qrcodeLabelPng(Asset $asset)
    {
        // If Imagick is not available in the environment, fall back to SVG label
        if (! extension_loaded('imagick')) {
            return $this->qrcodeLabel($asset);
        }

        try {
            $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
            ]);

            $qrPng = QrCode::format('png')
                ->size(180)
                ->margin(0)
                ->generate($payload);

            $qrImage = imagecreatefromstring($qrPng);
            if ($qrImage === false) {
                return response()->json(['message' => 'Failed to generate QR image'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $qrW = imagesx($qrImage);
            $qrH = imagesy($qrImage);

            $canvasW = $qrW + 320;
            $canvasH = max($qrH + 20, 240);

            $canvas = imagecreatetruecolor($canvasW, $canvasH);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, $canvasW, $canvasH, $white);

            imagecopy($canvas, $qrImage, 10, 10, 0, 0, $qrW, $qrH);

            $black = imagecolorallocate($canvas, 0, 0, 0);
            imagestring($canvas, 5, $qrW + 20, 20, 'Label QR Aset', $black);
            imagestring($canvas, 3, $qrW + 20, 50, 'Kode Aset: ' . $asset->kode_aset, $black);
            imagestring($canvas, 3, $qrW + 20, 74, 'Nama: ' . $asset->nama_aset, $black);
            imagestring($canvas, 3, $qrW + 20, 98, 'Jenis: ' . $asset->jenis, $black);
            imagestring($canvas, 3, $qrW + 20, 122, 'Lokasi: ' . $asset->lokasi, $black);
            imagestring($canvas, 3, $qrW + 20, 146, 'Kondisi: ' . $asset->kondisi, $black);

            ob_start();
            imagepng($canvas);
            $pngData = ob_get_clean();

            imagedestroy($canvas);
            imagedestroy($qrImage);

            $filename = 'asset-label-' . $asset->id . '-' . time() . '.png';

            return response($pngData, Response::HTTP_OK, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename=' . $filename,
            ]);
        } catch (\Throwable $e) {
            $logPath = storage_path('logs/asset_label_error.log');
            $content = "[" . now()->toDateTimeString() . "] " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n\n";
            @file_put_contents($logPath, $content, FILE_APPEND);

            return response()->json([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function qrcodeLabelPngForce(Asset $asset)
    {
        // This endpoint requires the optional dependency `endroid/qr-code` and ext-gd.
        if (! class_exists(\Endroid\QrCode\QrCode::class)) {
            return response()->json([
                'message' => 'Optional PNG backend not installed. To enable, run: composer require endroid/qr-code and ensure ext-gd is enabled in PHP.'
            ], Response::HTTP_NOT_IMPLEMENTED);
        }

        try {
            $payload = json_encode([
                'id' => $asset->id,
                'kode_aset' => $asset->kode_aset,
                'jenis' => $asset->jenis,
                'nama_aset' => $asset->nama_aset,
            ]);

            $qr = new \Endroid\QrCode\QrCode($payload);
            $qr->setSize(180);
            $qr->setMargin(0);

            $pngData = $qr->writeString();

            $qrImage = imagecreatefromstring($pngData);
            if ($qrImage === false) {
                return response()->json(['message' => 'Failed to create image from QR data'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            $qrW = imagesx($qrImage);
            $qrH = imagesy($qrImage);

            $canvasW = $qrW + 320;
            $canvasH = max($qrH + 20, 240);

            $canvas = imagecreatetruecolor($canvasW, $canvasH);
            $white = imagecolorallocate($canvas, 255, 255, 255);
            imagefilledrectangle($canvas, 0, 0, $canvasW, $canvasH, $white);

            imagecopy($canvas, $qrImage, 10, 10, 0, 0, $qrW, $qrH);

            $black = imagecolorallocate($canvas, 0, 0, 0);
            imagestring($canvas, 5, $qrW + 20, 20, 'Label QR Aset', $black);
            imagestring($canvas, 3, $qrW + 20, 50, 'Kode Aset: ' . $asset->kode_aset, $black);
            imagestring($canvas, 3, $qrW + 20, 74, 'Nama: ' . $asset->nama_aset, $black);
            imagestring($canvas, 3, $qrW + 20, 98, 'Jenis: ' . $asset->jenis, $black);
            imagestring($canvas, 3, $qrW + 20, 122, 'Lokasi: ' . $asset->lokasi, $black);
            imagestring($canvas, 3, $qrW + 20, 146, 'Kondisi: ' . $asset->kondisi, $black);

            ob_start();
            imagepng($canvas);
            $pngOut = ob_get_clean();

            imagedestroy($canvas);
            imagedestroy($qrImage);

            $filename = 'asset-label-' . $asset->id . '-' . time() . '.png';

            return response($pngOut, Response::HTTP_OK, [
                'Content-Type' => 'image/png',
                'Content-Disposition' => 'attachment; filename=' . $filename,
            ]);
        } catch (\Throwable $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function generateQrCodeLabelSvg(Asset $asset): string
    {
        $payload = json_encode([
            'id' => $asset->id,
            'kode_aset' => $asset->kode_aset,
            'jenis' => $asset->jenis,
            'nama_aset' => $asset->nama_aset,
        ]);

        $qrSvg = QrCode::format('svg')
            ->size(180)
            ->margin(0)
            ->generate($payload);

        return <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="520" height="320" viewBox="0 0 520 320" role="img" aria-label="QR Code Label">
    <rect width="100%" height="100%" fill="#ffffff" rx="16" ry="16" />
    <rect x="16" y="16" width="232" height="232" fill="#f9fafb" stroke="#d1d5db" stroke-width="1" rx="12" ry="12" />
    <g transform="translate(26,26)">
        $qrSvg
    </g>
    <text x="268" y="42" font-family="Inter, sans-serif" font-size="18" font-weight="700" fill="#111827">Label QR Aset</text>
    <text x="268" y="74" font-family="Inter, sans-serif" font-size="12" fill="#6b7280">Kode Aset</text>
    <text x="268" y="94" font-family="Inter, sans-serif" font-size="14" fill="#111827">{$asset->kode_aset}</text>
    <text x="268" y="122" font-family="Inter, sans-serif" font-size="12" fill="#6b7280">Nama Aset</text>
    <text x="268" y="142" font-family="Inter, sans-serif" font-size="14" fill="#111827">{$asset->nama_aset}</text>
    <text x="268" y="170" font-family="Inter, sans-serif" font-size="12" fill="#6b7280">Jenis</text>
    <text x="268" y="190" font-family="Inter, sans-serif" font-size="14" fill="#111827">{$asset->jenis}</text>
    <text x="268" y="218" font-family="Inter, sans-serif" font-size="12" fill="#6b7280">Lokasi</text>
    <text x="268" y="238" font-family="Inter, sans-serif" font-size="14" fill="#111827">{$asset->lokasi}</text>
    <text x="268" y="266" font-family="Inter, sans-serif" font-size="12" fill="#6b7280">Kondisi</text>
    <text x="268" y="286" font-family="Inter, sans-serif" font-size="14" fill="#111827">{$asset->kondisi}</text>
</svg>
SVG;
    }

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
        $svg = QrCode::format('svg')->size(400)->generate($payload);
        Storage::disk('local')->put($path, $svg);

        return $path;
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
            'lastScan' => $lastScan ? json_decode($lastScan->new_value, true) : [
                'latitude' => null,
                'longitude' => null,
                'scanned_at' => null,
            ],
        ]);
    }

    protected function buildAssetQuery(Request $request)
    {
        $query = Asset::query()->with('pic:id,name,role,email,telepon')->orderBy('created_at', 'desc');

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
