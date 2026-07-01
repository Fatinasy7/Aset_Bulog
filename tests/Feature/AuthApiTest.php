<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_token_and_user(): void
    {
        User::factory()->create([
            'name' => 'Admin IT',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin_it',
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message',
            'token',
            'token_type',
            'user' => ['id', 'name', 'email', 'role'],
        ]);
    }
}
