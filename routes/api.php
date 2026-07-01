<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PicController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ScanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/auth/register', [RegisterController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::apiResource('assets', AssetController::class);
    Route::post('assets/{asset}/assign-pic', [AssetController::class, 'assignPic']);
    Route::get('assets/{asset}/qrcode', [AssetController::class, 'qrcode']);
    Route::post('assets/{asset}/scan', [ScanController::class, 'store']);
    Route::get('assets/{asset}/location', [LocationController::class, 'show']);

    Route::get('/pics', [PicController::class, 'index']);
    Route::post('/pics', [PicController::class, 'store']);
    Route::put('/pics/{pic}', [PicController::class, 'update']);
    Route::delete('/pics/{pic}', [PicController::class, 'destroy']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::patch('/notifications/{notification}/read', [NotificationController::class, 'markRead']);

    Route::get('/reports/assets', [ReportController::class, 'assets']);
});
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

Route::middleware(['sanitize', 'json.api', 'security.headers', 'throttle:10,1'])->group(function () {
    Route::post('auth/register', [AuthController::class, 'register']);
    Route::post('auth/login', [AuthController::class, 'login']);
});

Route::middleware(['auth:sanctum', 'sanitize', 'json.api', 'security.headers'])->group(function () {
    Route::post('auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        $user = $request->user();
        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'createdAt' => $user->created_at ? $user->created_at->toISOString() : null,
            'updatedAt' => $user->updated_at ? $user->updated_at->toISOString() : null,
        ]);
    });

    Route::get('assets', [AssetController::class, 'index']);
    Route::get('assets/{asset}', [AssetController::class, 'show']);
    Route::get('assets/{asset}/qrcode', [AssetController::class, 'qrcode']);
    Route::post('assets/{asset}/scan', [AssetController::class, 'scan']);
    Route::get('assets/{asset}/location', [AssetController::class, 'location']);

    Route::get('pics', [PicController::class, 'index']);
    Route::get('notifications', [NotificationController::class, 'index']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead']);

    Route::middleware('role:admin_it,manajemen')->group(function () {
        Route::get('reports/assets', [ReportController::class, 'index']);
        Route::get('reports/assets/download', [ReportController::class, 'downloadPdf']);
        Route::get('reports/assets/export', [ReportController::class, 'reportsDownload']);
    });

    Route::middleware('role:admin_it')->group(function () {
        Route::post('assets', [AssetController::class, 'store']);
        Route::put('assets/{asset}', [AssetController::class, 'update']);
        Route::delete('assets/{asset}', [AssetController::class, 'destroy']);

        Route::post('pics', [PicController::class, 'store']);
        Route::put('pics/{pic}', [PicController::class, 'update']);
        Route::delete('pics/{pic}', [PicController::class, 'destroy']);
        Route::post('assets/{asset}/assign-pic', [PicController::class, 'assignPic']);

        Route::post('backups', [BackupController::class, 'store']);
        Route::get('backups', [BackupController::class, 'index']);
        Route::get('backups/verify', [BackupController::class, 'verify']);
    });

    Route::get('dashboard/summary', [DashboardController::class, 'summary']);
});
