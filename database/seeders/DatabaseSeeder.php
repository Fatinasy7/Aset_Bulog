<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bulog.local'],
            [
                'name' => 'Admin IT',
                'password' => Hash::make('password123'),
                'role' => 'admin_it',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pic@bulog.local'],
            [
                'name' => 'PIC User',
                'password' => Hash::make('password'),
                'role' => 'user_pic',
            ]
        );

        $this->call([
            PicSeeder::class,
        ]);
    }
}
