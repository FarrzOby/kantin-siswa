<?php
// routes/web.php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\QrisController;
use Illuminate\Support\Facades\Route;

// Public: redirect root to menu or login
Route::get('/', fn() => auth()->check()
    ? redirect()->route('home')
    : redirect()->route('login')
);

// Breeze auth routes (login, register, logout, password reset)
require __DIR__.'/auth.php';

// ─── Authenticated routes ──────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Redirect after login based on role
    Route::get('/dashboard', function () {
        return match(auth()->user()->role) {
            'admin'  => redirect()->route('admin.dashboard'),
            'kasir'  => redirect()->route('cashier.orders'),
            default  => redirect()->route('home'),
        };
    })->name('dashboard');

    // ── Student / All authenticated ──────────────────────────────────────
    Route::get('/menu', [MenuController::class, 'index'])->name('home');

    // Cart
    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/',            [CartController::class, 'index'])->name('index');
        Route::post('/add',        [CartController::class, 'add'])->name('add');
        Route::patch('/{cartItem}',[CartController::class, 'update'])->name('update');
        Route::delete('/{cartItem}',[CartController::class,'remove'])->name('remove');
        Route::get('/count',       [CartController::class, 'count'])->name('count');
    });
    Route::get('/cart', [CartController::class, 'index'])->name('cart');

    // Orders
    Route::get('/checkout',          [OrderController::class, 'checkout'])->name('checkout');
    Route::post('/orders',           [OrderController::class, 'store'])->name('orders.store');
    Route::get('/orders',            [OrderController::class, 'myOrders'])->name('orders.my');
    Route::get('/orders/{order}',    [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');

    // Profile
    Route::get('/profile',                  [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile',                 [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/password',        [ProfileController::class, 'updatePassword'])->name('profile.password');

    // ── Kasir routes ─────────────────────────────────────────────────────
    Route::middleware('role:kasir,admin')->prefix('cashier')->name('cashier.')->group(function () {
        Route::get('/orders',                       [OrderController::class, 'kasirIndex'])->name('orders');
        Route::patch('/orders/{order}/status',      [OrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/pay',          [OrderController::class, 'processPayment'])->name('orders.pay');
        Route::get('/orders/{order}/qris',          [QrisController::class, 'scanner'])->name('qris');
        Route::post('/orders/{order}/qris/verify',  [QrisController::class, 'verify'])->name('qris.verify');
    });

    // ── Admin routes ──────────────────────────────────────────────────────
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Users
        Route::get('/users',           [AdminController::class, 'users'])->name('users');
        Route::get('/users/create',    [AdminController::class, 'createUser'])->name('users.create');
        Route::post('/users',          [AdminController::class, 'storeUser'])->name('users.store');
        Route::get('/users/{user}/edit',[AdminController::class,'editUser'])->name('users.edit');
        Route::put('/users/{user}',    [AdminController::class, 'updateUser'])->name('users.update');
        Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

        // Menu management
        Route::get('/menu',              [MenuController::class, 'adminIndex'])->name('menu.index');
        Route::get('/menu/create',       [MenuController::class, 'create'])->name('menu.create');
        Route::post('/menu',             [MenuController::class, 'store'])->name('menu.store');
        Route::get('/menu/{menuItem}/edit',[MenuController::class,'edit'])->name('menu.edit');
        Route::put('/menu/{menuItem}',   [MenuController::class, 'update'])->name('menu.update');
        Route::delete('/menu/{menuItem}',[MenuController::class, 'destroy'])->name('menu.destroy');

        // All orders
        Route::get('/orders',            [OrderController::class, 'adminIndex'])->name('orders');
    });
});
