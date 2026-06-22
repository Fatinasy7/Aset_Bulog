<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@bulog.local'],
            [
                'name' => 'Admin IT',
                'password' => 'password123',
                'role' => 'admin_it',
            ]
        );
    }
}
