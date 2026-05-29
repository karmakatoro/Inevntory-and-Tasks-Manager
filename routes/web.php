
<?php

use App\Http\Controllers\{
    AuthController, CartController, DashboardController,
    ProductCategoryController, ProductController, ProductCustomerController,
    ProductStockController, ProjectController, SaleController,
    SettingController, StockAgenController, TaskController,
    TaskReportFileController, UserController, DailyClosingController,
    WorkSessionController,PayementController,ProductAssignmentController
};
use Illuminate\Support\Facades\Route;

/* --- ROUTES PUBLIQUES (Login) --- */
Route::prefix('auth')->controller(AuthController::class)->group(function () {
       Route::get('/login', 'index')->name('login')->withoutMiddleware('auth');
        Route::get('/forgot', 'forgot')->name('auth.forgot')->withoutMiddleware('auth');
        Route::get('/logout', 'logout')->name('auth.logout')->withoutMiddleware('auth');
        Route::post('/get-connected', 'get_connected')->name('auth.get-connected')->withoutMiddleware('auth');
        Route::get('/get-logged-out', 'get_logged_out')->name('auth.get-logged-out');
});

/* --- ROUTES CONNECTÉES (Nécessitent un Login) --- */
Route::middleware('auth')->group(function () {
    Route::controller(ProductAssignmentController::class)->group(function(){
    Route::get('/assign/stock','index')->name('show-assign-stock');
    Route::get('/products-stock/details/{reference_bon}',  'getDetailsBon')->name('products-stock.details');
    
});
    // 1. GESTION DES SESSIONS (Ouverture de journée)
    // Ces routes ne sont PAS dans check.session pour éviter la boucle infinie
   Route::controller(WorkSessionController::class)->group(function () {
    Route::get('/journal', 'index')->name('journal.show');
    
    // Étape 1 : Chargement du modal de Stock (URL propre)
    Route::get('/journal/stock-modal/{session}', 'loadStockModal')->name('journal.stock.modal');
    Route::post('/journal/stock-validate/{session}', 'validateStock')->name('journal.validate.stock');
    
    // Étape 2 : Chargement du modal de Cash (URL corrigée ici !)
    Route::get('/journal/cash-modal/{session}', 'loadCashModal')->name('journal.cash.modal');
    Route::post('/journal/cash-validate/{session}', 'validateCash')->name('journal.cash-validate');
   
    
    // Autres routes de session
    Route::get('/sessions/create', 'create')->name('sessions.create');
    Route::post('/sessions/store', 'store')->name('sessions.store');
    Route::post('/sessions/close', 'close')->name('sessions.close');
    Route::get('/closing-stat', 'getClosingStats')->name('closing.stats');
});
    // 2. ROUTES SÉCURISÉES (Nécessitent une session de travail OUVERTE)
    Route::middleware('check.session')->group(function () {

        // Accueil
        Route::get('/', function () {
            return redirect()->route('dashboard.sales');
        });
        Route::post('/assignments/accept/{referenceBon}', [ProductAssignmentController::class, 'acceptAssignment'])->name('products-stock.accept');
        // Dashboard
        Route::prefix('dashboard')->controller(DashboardController::class)->group(function () {
            Route::get('/sales', 'index')->name('dashboard.sales');
            Route::get('/analytics', 'analytics')->name('dashboard.analytics');
        });

        // Ventes et Panier
        Route::controller(SaleController::class)->group(function () {
            Route::post('sale-create', 'store')->name('sales.store');
            Route::get('/sales/{agent}/index','index')->name('sales.index');
        });

        Route::controller(CartController::class)->group(function () {
            Route::post('cart', 'addToCart')->name('cart.add');
            Route::get('cart-fetch', 'fetchCart')->name('cart.fetch');
            Route::post('cart-increment', 'incrementer')->name('cart.increment');
            Route::post('cart-decrement', 'decrementer')->name('cart.decrement');
            Route::post('cart-sale', 'addToCartSale')->name('cart-add-sale');
            Route::post('cart-update-quantity', 'upadteQtySaleCart')->name('cart-update');
            Route::delete('cart-sale-delete', 'removeItmToCartSale')->name('cart-delete');
            Route::get('cart-index', 'fetchCartSale')->name('cart-index');
            Route::post('Stock-Assigne', 'validerLot')->name('stock.assgin');
            Route::delete('cart-remove', 'remove')->name('cart.remove');
        });

        // Clôture et Stock Agent
        Route::controller(DailyClosingController::class)->group(function(){

            Route::post('/closing','store')->name('closing.store');
        });

        Route::controller(StockAgenController::class)->group(function () {
            Route::get('/agent/{agent}/stock', 'index')->name('show-stock-agent');
        });
        Route::controller(PayementController::class)->group(function(){
            Route::post('/payement/create','store')->name('payement.store');
            Route::get('/payement/{agent}/index','index')->name('payement.show');
            Route::get('/payement/{agent}/credit','getCustomerDebts')->name('payement.credit');
            Route::get('/payement/{payement}/details','getPayementAllocation');
        });
    });

    /* --- ROUTES ADMINISTRATIVES (Connecté, mais pas besoin de session de vente) --- */
    Route::controller(ProjectController::class)->group(function () {
        Route::resource('projects', ProjectController::class);
        Route::delete('dm-projects', 'delete_multiples')->name('dm-projects');
    });
    
 Route::controller(UserController::class)->group(function () {
        Route::resource('users', UserController::class);
        Route::delete('dm-users', 'delete_multiples')->name('dm-users');
    });
      Route::controller(TaskReportFileController::class)->group(function () {
        Route::resource('tasks_report', TaskReportFileController::class);
        Route::get('get-share-options-task-file', 'get_share_options')->name('get-share-opts');
        Route::post('share-options-task-file', 'share_options')->name('share-opts');
        Route::delete('dm-trp', 'delete_multiples')->name('dm-trp');
    });
   Route::controller(TaskController::class)->group(function () {
        Route::resource('tasks', TaskController::class);
        Route::delete('dm-tasks', 'delete_multiples')->name('dm-tasks');
    });
    Route::controller(ProductCustomerController::class)->group(function () {
        Route::resource('customers', ProductCustomerController::class);
        Route::delete('dm-cs', 'delete_multiples')->name('dm-cs');
    });

      Route::controller(ProductCategoryController::class)->group(function () {
        Route::resource('product-categories', ProductCategoryController::class);
        Route::delete('dm-prcat', 'delete_multiples')->name('dm-prcat');
    });
   Route::controller(ProductStockController::class)->group(function () {
        Route::resource('products-stock', ProductStockController::class);
        Route::get('show-stock-product', 'showProductOnstock')->name('show-stock-product');
        Route::delete('dm-ps', 'delete_multiples')->name('dm-ps');
    });
  Route::controller(ProductController::class)->group(function () {
        Route::resource('products', ProductController::class);
        Route::get('product-price', 'product_price')->name('product-price');
        Route::delete('dm-pr', 'delete_multiples')->name('dm-pr');
    });

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::get('/get-logged-out', [AuthController::class, 'get_logged_out'])->name('auth.get-logged-out');
});
