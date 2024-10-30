<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCustomerController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskReportFileController;
use App\Http\Controllers\UserController;
use App\Models\Product;
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


Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard.sales');
    });
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
    Route::controller(ProjectController::class)->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::delete('dm-projects', 'delete_multiples')->name('dm-projects');
    });
    Route::controller(TaskController::class)->group(function () {
        Route::resource('tasks', TaskController::class);
        Route::delete('dm-tasks', 'delete_multiples')->name('dm-tasks');
    });
    Route::controller(TaskReportFileController::class)->group(function () {
        Route::resource('tasks_report', TaskReportFileController::class);
        Route::get('get-share-options-task-file', 'get_share_options')->name('get-share-opts');
        Route::post('share-options-task-file', 'share_options')->name('share-opts');
        Route::delete('dm-trp', 'delete_multiples')->name('dm-trp');
    });
    Route::controller(ProductCategoryController::class)->group(function () {
        Route::resource('product-categories', ProductCategoryController::class);
        Route::delete('dm-prcat', 'delete_multiples')->name('dm-prcat');
    });
    Route::controller(ProductController::class)->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('product-price', 'product_price')->name('product-price');
        Route::delete('dm-pr', 'delete_multiples')->name('dm-pr');
    });
    Route::controller(ProductCustomerController::class)->group(function () {
        Route::resource('customers', ProductCustomerController::class);
        Route::delete('dm-cs', 'delete_multiples')->name('dm-cs');
    });
    Route::controller(ProductStockController::class)->group(function () {
        Route::resource('products-stock', ProductStockController::class);
        Route::delete('dm-ps', 'delete_multiples')->name('dm-ps');
    });
});
