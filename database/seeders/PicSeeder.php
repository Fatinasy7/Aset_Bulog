<?php

namespace Database\Seeders;

use App\Models\Pic;
use Illuminate\Database\Seeder;

class PicSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pic::firstOrCreate(
            ['email' => 'pic1@bulog.local'],
            [
                'nama' => 'PIC Pertama',
                'jabatan' => 'Teknisi',
                'telepon' => '081234567890',
            ]
        );

        Pic::firstOrCreate(
            ['email' => 'pic2@bulog.local'],
            [
                'nama' => 'PIC Kedua',
                'jabatan' => 'Manajemen',
                'telepon' => '081298765432',
            ]
        );
    }
}
