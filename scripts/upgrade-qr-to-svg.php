<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

function logMessage(string $line): void
{
    $path = __DIR__ . '/upgrade-qr-to-svg.log';
    file_put_contents($path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
}

logMessage('SCRIPT_START: ' . date('c')); 

$assets = Asset::where('qr_code_path', 'like', '%.png')->get();

if ($assets->isEmpty()) {
    logMessage("No PNG-based assets found.");
    echo "No PNG-based assets found.\n";
    exit(0);
}

foreach ($assets as $asset) {
    logMessage("Updating asset {$asset->id}: {$asset->qr_code_path}");
    echo "Updating asset {$asset->id}: {$asset->qr_code_path}\n";

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

    logMessage("  Saved SVG: {$path}");
    echo "  Saved SVG: {$path}\n";

    if ($oldPath && Storage::disk('local')->exists($oldPath)) {
        Storage::disk('local')->delete($oldPath);
        logMessage("  Deleted old PNG: {$oldPath}");
        echo "  Deleted old PNG: {$oldPath}\n";
    }
}

// Remove other standalone PNG files in qrcodes folder
$files = Storage::disk('local')->files('qrcodes');
foreach ($files as $file) {
    if (str_ends_with($file, '.png')) {
        Storage::disk('local')->delete($file);
        logMessage("Deleted leftover PNG: {$file}");
        echo "Deleted leftover PNG: {$file}\n";
    }
}

echo "Done.\n";
logMessage('SCRIPT_END: ' . date('c'));
