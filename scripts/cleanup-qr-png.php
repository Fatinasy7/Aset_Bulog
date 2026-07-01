<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

$logFile = __DIR__ . '/cleanup-qr-png.log';
function logLine(string $line) {
    global $logFile;
    file_put_contents($logFile, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

if (file_exists($logFile)) {
    unlink($logFile);
}

logLine('START ' . date('c'));

$assets = Asset::where('qr_code_path', 'like', '%.png')->get();
logLine('PNG assets: ' . $assets->count());

foreach ($assets as $asset) {
    logLine('Asset ' . $asset->id . ' old path: ' . $asset->qr_code_path);
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
    $oldPath = $asset->qr_code_path;
    $asset->qr_code_path = $path;
    $asset->save();
    logLine('  Saved new SVG: ' . $path);
    if ($oldPath && Storage::disk('local')->exists($oldPath)) {
        Storage::disk('local')->delete($oldPath);
        logLine('  Deleted old PNG: ' . $oldPath);
    } else {
        logLine('  Old PNG missing: ' . $oldPath);
    }
}

$files = Storage::disk('local')->files('qrcodes');
foreach ($files as $file) {
    if (str_ends_with($file, '.png')) {
        Storage::disk('local')->delete($file);
        logLine('Deleted leftover PNG: ' . $file);
    }
}

logLine('Remaining qrcodes:');
foreach (Storage::disk('local')->files('qrcodes') as $file) {
    logLine('  ' . $file);
}

logLine('END ' . date('c'));
