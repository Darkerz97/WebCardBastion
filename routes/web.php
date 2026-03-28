<?php

use App\Http\Controllers\Web\AccountOrderController;
use App\Http\Controllers\Web\AccountController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\CartController;
use App\Http\Controllers\Web\CashClosureController;
use App\Http\Controllers\Web\CategoryController;
use App\Http\Controllers\Web\CheckoutController;
use App\Http\Controllers\Web\CustomerController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\InventoryMovementController;
use App\Http\Controllers\Web\ArticleCommentController;
use App\Http\Controllers\Web\ArticleController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\SaleController;
use App\Http\Controllers\Web\PlayerTournamentController;
use App\Http\Controllers\Web\PasswordResetController;
use App\Http\Controllers\Web\PreorderController;
use App\Http\Controllers\Web\SiteSettingController;
use App\Http\Controllers\Web\StorefrontController;
use App\Http\Controllers\Web\TournamentController;
use App\Http\Controllers\Web\PublicMediaController;
use App\Http\Controllers\Web\PublicArticleController;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', [StorefrontController::class, 'home'])->name('store.home');
Route::get('/media/{path}', [PublicMediaController::class, 'show'])->where('path', '.*')->name('media.public');
Route::get('/tienda', [StorefrontController::class, 'catalog'])->name('store.catalog');
Route::get('/tienda/{product:slug}', [StorefrontController::class, 'show'])->name('store.products.show');
Route::get('/articulos', [PublicArticleController::class, 'index'])->name('blog.index');
Route::get('/articulos/{article:slug}', [PublicArticleController::class, 'show'])->name('blog.show');
Route::redirect('/vlog', '/articulos');
Route::post('/articulos/{article:slug}/comentarios', [ArticleCommentController::class, 'store'])->name('blog.comments.store');
Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
Route::post('/carrito', [CartController::class, 'store'])->name('cart.store');
Route::patch('/carrito/{product}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/carrito/{product}', [CartController::class, 'destroy'])->name('cart.destroy');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/registro', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
    Route::get('/recuperar-contrasena', [PasswordResetController::class, 'create'])->name('password.request');
    Route::post('/recuperar-contrasena', [PasswordResetController::class, 'store'])->name('password.email');
    Route::get('/restablecer-contrasena/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
    Route::post('/restablecer-contrasena', [PasswordResetController::class, 'update'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::get('/mi-cuenta', AccountController::class)->name('account.dashboard');
    Route::get('/mi-cuenta/compras', [AccountOrderController::class, 'index'])->name('account.orders.index');
    Route::get('/mi-cuenta/torneos', [PlayerTournamentController::class, 'index'])->name('account.tournaments.index');
    Route::post('/mi-cuenta/torneos/{tournament}', [PlayerTournamentController::class, 'store'])->name('account.tournaments.store');
    Route::get('/checkout', [CheckoutController::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

    Route::middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER.','.User::ROLE_CASHIER)->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::resource('categories', CategoryController::class)->except('show')->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);

        Route::get('products/template', [ProductController::class, 'template'])->name('products.template')->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);
        Route::post('products/import', [ProductController::class, 'import'])->name('products.import')->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);
        Route::resource('products', ProductController::class)->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);

        Route::get('customers/template', [CustomerController::class, 'template'])->name('customers.template');
        Route::post('customers/import', [CustomerController::class, 'import'])->name('customers.import');
        Route::resource('customers', CustomerController::class);

        Route::get('sales/template', [SaleController::class, 'template'])->name('sales.template');
        Route::post('sales/import', [SaleController::class, 'import'])->name('sales.import');
        Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show']);

        Route::get('preorders', [PreorderController::class, 'index'])->name('preorders.index');
        Route::get('preorders/create', [PreorderController::class, 'create'])->name('preorders.create');
        Route::post('preorders', [PreorderController::class, 'store'])->name('preorders.store');
        Route::get('preorders/{preorder}', [PreorderController::class, 'show'])->name('preorders.show');
        Route::post('preorders/{preorder}/payments', [PreorderController::class, 'addPayment'])->name('preorders.payments.store');
        Route::patch('preorders/{preorder}/status', [PreorderController::class, 'updateStatus'])->name('preorders.status.update');

        Route::get('cash-closures', [CashClosureController::class, 'index'])->name('cash-closures.index');
        Route::get('cash-closures/create', [CashClosureController::class, 'create'])->name('cash-closures.create');
        Route::post('cash-closures', [CashClosureController::class, 'store'])->name('cash-closures.store');
        Route::get('cash-closures/{cashClosure}', [CashClosureController::class, 'show'])->name('cash-closures.show');
        Route::patch('cash-closures/{cashClosure}/status', [CashClosureController::class, 'updateStatus'])->name('cash-closures.status.update');

        Route::get('inventory-movements', [InventoryMovementController::class, 'index'])->name('inventory-movements.index');
        Route::get('inventory-movements/create', [InventoryMovementController::class, 'create'])->name('inventory-movements.create');
        Route::post('inventory-movements', [InventoryMovementController::class, 'store'])->name('inventory-movements.store');
        Route::get('inventory-movements/{inventoryMovement}', [InventoryMovementController::class, 'show'])->name('inventory-movements.show');

        Route::post('tournaments/{tournament}/rounds', [TournamentController::class, 'generateRound'])->name('tournaments.rounds.store')->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);
        Route::post('tournament-matches/{match}/report', [TournamentController::class, 'reportMatch'])->name('tournaments.matches.report')->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);
        Route::resource('tournaments', TournamentController::class)->middleware('role:'.User::ROLE_ADMIN.','.User::ROLE_MANAGER);
        Route::resource('articles', ArticleController::class)->middleware('role:'.User::ROLE_ADMIN);
        Route::delete('articles/{article}/comments/{comment}', [ArticleCommentController::class, 'destroy'])->name('articles.comments.destroy')->middleware('role:'.User::ROLE_ADMIN);
        Route::get('site-settings', [SiteSettingController::class, 'edit'])->name('site-settings.edit')->middleware('role:'.User::ROLE_ADMIN);
        Route::put('site-settings', [SiteSettingController::class, 'update'])->name('site-settings.update')->middleware('role:'.User::ROLE_ADMIN);
    });
});
