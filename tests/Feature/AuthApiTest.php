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

    public function test_authenticated_user_can_be_fetched_with_bearer_token(): void
    {
        $user = User::factory()->create([
            'name' => 'PIC User',
            'email' => 'pic@example.com',
            'password' => bcrypt('password'),
            'role' => 'user_pic',
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/user');

        $response->assertOk();
        $response->assertJson([
            'id' => $user->id,
            'email' => $user->email,
            'role' => 'user_pic',
        ]);
    }
}
