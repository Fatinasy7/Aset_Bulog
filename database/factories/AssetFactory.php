<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'kode_aset' => 'AST-' . fake()->unique()->numerify('#####'),
            'nama_aset' => fake()->word() . ' Asset',
            'merk_type' => fake()->company(),
            'serial_number' => fake()->bothify('SN-#####'),
            'lokasi' => fake()->city(),
            'koordinat_lat' => fake()->latitude(),
            'koordinat_lng' => fake()->longitude(),
            'kondisi' => 'baik',
            'tgl_perolehan' => fake()->date(),
            'harga' => fake()->numberBetween(1000000, 50000000),
            'keterangan' => fake()->sentence(),
            'jenis' => fake()->randomElement(['laptop', 'printer']),
            'qr_code_path' => null,
            'pic_id' => null,
        ];
    }
}
