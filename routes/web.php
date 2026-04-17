<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductCustomerController;
use App\Http\Controllers\ProductStockController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockAgenController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskReportFileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Notifications\StockAssignementNofication;

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
    Route::controller(StockAgenController::class)->group(function () {
        Route::get('/agent/{agent}/stock', 'index')->name('show-stock-agent');
    });

    Route::controller(CartController::class)->group(function () {
        Route::post('cart', 'addToCart')->name('cart.add');
        Route::get('cart-fetch', 'fetchCart')->name('cart.fetch');
        Route::post('cart-increment', 'incrementer')->name('cart.increment');
        Route::post('cart-decrement', 'decrementer')->name('cart.decrement');
        Route::post('Stock-Assigne', 'validerLot')->name('stock.assgin');
        Route::delete('cart-remove', 'remove')->name('cart.remove');
        Route::post('cart-sale', 'addToCartSale')->name('cart-add-sale');
        Route::post('cart-update-quantity', 'upadteQtySaleCart')->name('cart-update');
        Route::delete('cart-sale-delete', 'removeItmToCartSale')->name('cart-delete');
        Route::get('cart-index', 'fetchCartSale')->name('cart-index');
    });

    Route::controller(SaleController::class)->group(function () {
        Route::post('sale-create', 'store')->name('sales.store');
        Route::get('/sales/{agent}/index','index')->name('sales.index');
    });
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
        Route::get('show-stock-product', 'showProductOnstock')->name('show-stock-product');
        Route::delete('dm-ps', 'delete_multiples')->name('dm-ps');
    });

});

