<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\AuditLog;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@bulog.co.id'],
            ['name' => 'Admin IT', 'password' => bcrypt('password'), 'role' => 'admin_it', 'phone' => '081200000001']
        );

        User::query()->updateOrCreate(
            ['email' => 'andi@bulog.co.id'],
            ['name' => 'Andi Saputra', 'password' => bcrypt('password'), 'role' => 'user_pic', 'phone' => '081200000002']
        );

        User::query()->updateOrCreate(
            ['email' => 'sari@bulog.co.id'],
            ['name' => 'Sari Wulandari', 'password' => bcrypt('password'), 'role' => 'user_pic', 'phone' => '081200000003']
        );

        User::query()->updateOrCreate(
            ['email' => 'rudi@bulog.co.id'],
            ['name' => 'Rudi Hartono', 'password' => bcrypt('password'), 'role' => 'user_pic', 'phone' => '081200000004']
        );

        User::query()->updateOrCreate(
            ['email' => 'manager@bulog.co.id'],
            ['name' => 'Direktur Operasional', 'password' => bcrypt('password'), 'role' => 'manajemen', 'phone' => '081200000005']
        );

        if (Asset::count() === 0) {
            $assetOne = Asset::create([
                'kode_aset' => 'AST-001',
                'nama_aset' => 'Laptop Operasional',
                'merk_type' => 'Lenovo ThinkPad X1',
                'serial_number' => 'PF2K4R8J',
                'lokasi' => 'Ruang IT',
                'kondisi' => 'Baik',
                'tgl_perolehan' => '2024-01-15',
                'harga' => 18000000,
                'keterangan' => 'Unit operasional utama',
                'jenis' => 'laptop',
                'pic_name' => 'Andi Saputra',
                'koordinat_lat' => -6.2000000,
                'koordinat_lng' => 106.8166660,
            ]);

            $assetTwo = Asset::create([
                'kode_aset' => 'AST-002',
                'nama_aset' => 'Printer Administrasi',
                'merk_type' => 'HP LaserJet Pro',
                'serial_number' => 'HP-334455',
                'lokasi' => 'Ruang TU',
                'kondisi' => 'Rusak Ringan',
                'tgl_perolehan' => '2024-02-20',
                'harga' => 4500000,
                'keterangan' => 'Butuh servis ringan',
                'jenis' => 'printer',
                'pic_name' => 'Sari Wulandari',
                'koordinat_lat' => -6.2010000,
                'koordinat_lng' => 106.8171000,
            ]);

            AuditLog::create([
                'asset_id' => $assetOne->id,
                'asset_code' => $assetOne->kode_aset,
                'action' => 'created',
                'field_name' => null,
                'old_value' => null,
                'new_value' => null,
                'changed_by' => 'Seeder',
            ]);

            AuditLog::create([
                'asset_id' => $assetTwo->id,
                'asset_code' => $assetTwo->kode_aset,
                'action' => 'created',
                'field_name' => null,
                'old_value' => null,
                'new_value' => null,
                'changed_by' => 'Seeder',
            ]);
        }
    }
}
