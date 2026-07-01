<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PicFrontendController;
use App\Http\Controllers\AssetFrontendController;

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

Route::get('/pics', [PicFrontendController::class, 'index'])->name('pics.index');
Route::get('/assets', [AssetFrontendController::class, 'index'])->name('assets.index');

Route::middleware(['auth', 'role:admin_it'])->group(function () {
});