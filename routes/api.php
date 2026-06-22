<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
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

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('assets', [AssetController::class, 'index']);
    Route::get('assets/{asset}', [AssetController::class, 'show']);
    Route::get('assets/{asset}/qrcode', [AssetController::class, 'qrcode']);

    Route::middleware('role:admin_it')->group(function () {
        Route::post('assets', [AssetController::class, 'store']);
        Route::put('assets/{asset}', [AssetController::class, 'update']);
        Route::delete('assets/{asset}', [AssetController::class, 'destroy']);
    });
});
