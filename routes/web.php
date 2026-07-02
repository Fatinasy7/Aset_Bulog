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
    return redirect()->route('frontend.dashboard');
});

Route::get('/app', function () {
    return redirect()->route('frontend.dashboard');
});

Route::get('/frontend/login', function () {
    return view('auth.login');
})->name('frontend.login')->middleware('guest');
Route::post('/frontend/login', [App\Http\Controllers\AuthController::class, 'loginWeb'])->name('frontend.login.submit');
Route::post('/frontend/logout', [App\Http\Controllers\AuthController::class, 'logoutWeb'])->name('frontend.logout');
Route::view('/frontend/design-system', 'ui.design-system')->name('frontend.design-system');

Route::controller(FrontendPageController::class)->middleware('auth')->group(function () {
    Route::get('/frontend/dashboard', 'dashboard')->name('frontend.dashboard');
    Route::get('/frontend/data-laptop', 'dataLaptop')->name('frontend.assets.laptops');
    Route::get('/frontend/data-printer', 'dataPrinter')->name('frontend.assets.printers');
    Route::get('/frontend/assets/create', 'assetsCreate')->name('frontend.assets.create');
    Route::get('/frontend/assets/{asset}/edit', 'assetsEdit')->name('frontend.assets.edit');
    Route::get('/frontend/assets/detail/{asset}', 'assetShow')->name('frontend.assets.show');
    Route::get('/frontend/pics', 'picsIndex')->name('frontend.pics.index');
    Route::get('/frontend/pics/form', 'picsForm')->name('frontend.pics.form');
    Route::get('/frontend/pics/create', 'picsCreate')->name('frontend.pics.create');
    Route::get('/frontend/pics/{pic}/edit', 'picsEdit')->name('frontend.pics.edit');
    Route::get('/frontend/reports', 'reportsIndex')->name('frontend.reports.index');
    Route::get('/frontend/reports/export', 'reportsExport')->name('frontend.reports.export');
    Route::get('/frontend/reports/download', 'reportsDownload')->name('frontend.reports.download');
    Route::get('/frontend/settings', 'settings')->name('frontend.settings');
    Route::get('/frontend/audit-trail', 'auditIndex')->name('frontend.audit.index');
    Route::get('/frontend/scan-qr', 'scanQr')->name('frontend.scan-qr');
    Route::post('/frontend/scan-qr/lookup', 'scanQrLookup')->name('frontend.scan-qr.lookup');
    Route::get('/frontend/dashboard-management', 'dashboardManagement')->name('frontend.dashboard.management');
});

Route::post('/frontend/assets', [AssetController::class, 'storeWeb'])->name('frontend.assets.store');
Route::put('/frontend/assets/{asset}', [AssetController::class, 'updateWeb'])->name('frontend.assets.update');
Route::delete('/frontend/assets/{asset}', [AssetController::class, 'destroyWeb'])->name('frontend.assets.destroy');
Route::get('/frontend/assets/{asset}/qrcode', [AssetController::class, 'qrcodePublic'])->name('frontend.assets.qrcode');

Route::post('/frontend/pics', [FrontendPageController::class, 'storePic'])->name('frontend.pics.store');
Route::put('/frontend/pics/{pic}', [FrontendPageController::class, 'updatePic'])->name('frontend.pics.update');
Route::delete('/frontend/pics/{pic}', [FrontendPageController::class, 'destroyPic'])->name('frontend.pics.destroy');
