<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
<<<<<<< HEAD
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin IT',
                'password' => Hash::make('password'),
=======
        User::firstOrCreate(
            ['email' => 'admin@bulog.local'],
            [
                'name' => 'Admin IT',
                'password' => Hash::make('password123'),
>>>>>>> 22589e0065f85f8afe27c27718fc715915ec2569
                'role' => 'admin_it',
            ]
        );

<<<<<<< HEAD
        User::updateOrCreate(
            ['email' => 'pic@example.com'],
            [
                'name' => 'PIC User',
                'password' => Hash::make('password'),
                'role' => 'user_pic',
            ]
        );
=======
        $this->call([
            PicSeeder::class,
        ]);
>>>>>>> 22589e0065f85f8afe27c27718fc715915ec2569
    }
}
