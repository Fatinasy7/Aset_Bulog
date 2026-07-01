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
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin IT',
                'password' => Hash::make('password'),
                'role' => 'admin_it',
            ]
        );

        User::updateOrCreate(
            ['email' => 'pic@example.com'],
            [
                'name' => 'PIC User',
                'password' => Hash::make('password'),
                'role' => 'user_pic',
            ]
        );
    }
}
