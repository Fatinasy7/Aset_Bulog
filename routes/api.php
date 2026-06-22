<?php

use App\Http\Controllers\AssetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/login', function (Request $request) {
    $credentials = $request->validate([
        'username' => 'required|string',
        'password' => 'required|string',
        'role' => 'required|string|in:admin,pic'
    ]);

    $demoUsers = [
        'admin' => [
            'username' => 'admin',
            'password' => 'admin123',
            'role' => 'admin',
            'name' => 'Administrator'
        ],
        'pic' => [
            'username' => 'pic',
            'password' => 'pic123',
            'role' => 'pic',
            'name' => 'PIC'
        ]
    ];

    $user = $demoUsers[$credentials['username']] ?? null;
    if (!$user || $user['password'] !== $credentials['password'] || $user['role'] !== $credentials['role']) {
        return response()->json(['message' => 'Invalid username, password, or role'], 401);
    }

    return response()->json([
        'access_token' => base64_encode($user['username'] . ':' . now()->timestamp),
        'user' => [
            'username' => $user['username'],
            'role' => $user['role'],
            'name' => $user['name']
        ]
    ]);
});

Route::post('auth/logout', function () {
    return response()->json(['message' => 'Logout successful']);
});

Route::apiResource('assets', AssetController::class);
Route::post('assets/{asset}/scan', [AssetController::class, 'scan']);
Route::get('assets/{asset}/qrcode', [AssetController::class, 'qrcode']);
