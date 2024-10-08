<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
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
    return redirect()->route('login');
});
Route::middleware('auth')->group(function () {
    Route::prefix('auth')->controller(AuthController::class)->group(function () {
        Route::get('/login', 'index')->name('login')->withoutMiddleware('auth');
        Route::get('/forgot', 'forgot')->name('auth.forgot')->withoutMiddleware('auth');
        Route::get('/logout', 'logout')->name('auth.logout')->withoutMiddleware('auth');
        Route::post('/get-connected', 'get_connected')->name('auth.get-connected')->withoutMiddleware('auth');
        Route::get('/get-logged-out', 'get_logged_out')->name('auth.get-logged-out');
    });
    Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
        Route::get('/sales', 'index')->name('dashboard.sales');
        Route::get('/analytics', 'analytics')->name('dashboard.analytics');
    });
    Route::controller(SettingController::class)->group(function () {
        Route::get('settings', 'index')->name('settings.index');
    });
    Route::controller(UserController::class)->group(function () {
        Route::resource('users', UserController::class);
        Route::delete('dm-users', 'delete_multiples')->name('dm-users');
    });
});
