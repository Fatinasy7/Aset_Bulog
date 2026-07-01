<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\AuthController;
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
