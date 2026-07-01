<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $asset = Asset::create([
            'kode_aset' => 'SEED-' . time(),
            'nama_aset' => 'Seeder Asset',
            'merk_type' => 'ACME',
            'serial_number' => 'SN' . rand(1000, 9999),
            'lokasi' => 'Gudang',
            'kondisi' => 'baik',
            'jenis' => 'laptop',
        ]);

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

        echo "SEED_ASSET_ID:" . $asset->id . PHP_EOL;
    }
}
