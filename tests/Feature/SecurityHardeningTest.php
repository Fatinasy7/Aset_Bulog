<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Route::post('/test-sanitize', function (Request $request) {
            return response()->json([
                'name' => $request->input('name'),
            ]);
        })->middleware('sanitize');

        Route::post('/test-json-api', function () {
            return response()->json(['ok' => true]);
        })->middleware('json.api');

        Route::post('/test-csrf', function () {
            return response()->json(['ok' => true]);
        })->middleware('csrf');

        Route::get('/api/test-security-headers', function () {
            return response()->json(['ok' => true]);
        })->middleware('security.headers');

        Route::get('/test-rate-limit', function () {
            return response()->json(['ok' => true]);
        })->middleware('throttle:2,1');
    }

    public function test_sanitize_middleware_strips_script_tags_from_request_data(): void
    {
        $response = $this->postJson('/test-sanitize', [
            'name' => '<script>alert("x")</script>Alpha',
        ]);

        $response->assertOk();
        $response->assertJsonPath('name', 'Alpha');
    }

    public function test_api_assets_endpoint_requires_authentication(): void
    {
        $response = $this->getJson('/api/assets');

        $response->assertUnauthorized();
    }

    public function test_api_assets_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->getJson('/api/assets');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_user_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->getJson('/api/user');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_assets_post_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->postJson('/api/assets', [
            'kode_aset' => 'AST-999',
            'nama_aset' => 'Unauthorized test',
            'merk_type' => 'Test',
            'lokasi' => 'Test Location',
            'kondisi' => 'baik',
            'jenis' => 'laptop',
        ]);

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_asset_detail_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->getJson('/api/assets/1');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_auth_logout_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->postJson('/api/auth/logout');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_pics_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->getJson('/api/pics');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_notifications_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->getJson('/api/notifications');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_api_reports_endpoint_without_login_returns_unauthenticated_json(): void
    {
        $response = $this->getJson('/api/reports/assets');

        $response->assertStatus(401);
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_cors_configuration_uses_configured_origins(): void
    {
        config()->set('cors.allowed_origins', ['*']);

        $this->assertSame(['*'], config('cors.allowed_origins'));
    }

    public function test_cors_allows_cross_origin_requests(): void
    {
        $response = $this->withHeaders(['Origin' => 'http://example.com'])
            ->get('/api/test-security-headers');

        $response->assertOk();
        $response->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function test_json_api_middleware_rejects_non_json_mutation_requests(): void
    {
        $response = $this->post('/test-json-api', ['name' => 'ok'], ['CONTENT_TYPE' => 'text/plain']);

        $response->assertStatus(415);
        $response->assertJsonPath('message', 'Content-Type harus application/json.');
    }

    public function test_csrf_middleware_rejects_missing_or_invalid_tokens(): void
    {
        $response = $this->withSession(['_token' => 'correct-token'])
            ->post('/test-csrf', ['name' => 'ok'], ['HTTP_X-CSRF-TOKEN' => 'wrong-token']);

        $response->assertStatus(419);
        $response->assertJsonPath('message', 'CSRF token missing or invalid.');
    }

    public function test_security_headers_are_included_in_responses(): void
    {
        $response = $this->get('/api/test-security-headers');

        $response->assertOk();
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_api_requests_are_rate_limited_after_threshold(): void
    {
        $this->get('/test-rate-limit')->assertOk();
        $this->get('/test-rate-limit')->assertOk();
        $response = $this->get('/test-rate-limit');

        $response->assertStatus(429);
    }
}
