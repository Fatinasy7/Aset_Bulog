<?php

use Illuminate\Support\Facades\Route;

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
Route::view('/frontend/dashboard', 'dashboard.index')->name('frontend.dashboard');
Route::view('/frontend/assets', 'assets.index')->name('frontend.assets.index');
Route::view('/frontend/assets/create', 'assets.create')->name('frontend.assets.create');
Route::view('/frontend/assets/detail', 'assets.show')->name('frontend.assets.show');
Route::view('/frontend/pics', 'pics.index')->name('frontend.pics.index');
Route::view('/frontend/pics/form', 'pics.form')->name('frontend.pics.form');
Route::view('/frontend/reports', 'reports.index')->name('frontend.reports.index');
Route::view('/frontend/audit-trail', 'audit.index')->name('frontend.audit.index');
