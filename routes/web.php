<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\FrontendPageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->file(public_path('index.html'));
});

Route::get('/app', function () {
    return response()->file(public_path('index.html'));
});

Route::view('/frontend/login', 'auth.login')->name('frontend.login');
Route::view('/frontend/design-system', 'ui.design-system')->name('frontend.design-system');

Route::controller(FrontendPageController::class)->group(function () {
    Route::get('/frontend/dashboard', 'dashboard')->name('frontend.dashboard');
    Route::get('/frontend/assets', 'assetsIndex')->name('frontend.assets.index');
    Route::get('/frontend/assets/create', 'assetsCreate')->name('frontend.assets.create');
    Route::get('/frontend/assets/{asset}/edit', 'assetsEdit')->name('frontend.assets.edit');
    Route::get('/frontend/assets/detail/{asset}', 'assetShow')->name('frontend.assets.show');
    Route::get('/frontend/pics', 'picsIndex')->name('frontend.pics.index');
    Route::get('/frontend/pics/form', 'picsForm')->name('frontend.pics.form');
    Route::get('/frontend/reports', 'reportsIndex')->name('frontend.reports.index');
    Route::get('/frontend/audit-trail', 'auditIndex')->name('frontend.audit.index');
});

Route::post('/frontend/assets', [AssetController::class, 'storeWeb'])->name('frontend.assets.store');
Route::put('/frontend/assets/{asset}', [AssetController::class, 'updateWeb'])->name('frontend.assets.update');
Route::delete('/frontend/assets/{asset}', [AssetController::class, 'destroyWeb'])->name('frontend.assets.destroy');
