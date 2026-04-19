<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ReservationController; // Tambahkan ini
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController as CustomerOrderController;

// =============================================
// LANDING PAGE & RESERVATION ROUTES
// =============================================
Route::get('/', [ReservationController::class, 'index'])->name('landing');
Route::post('/reserve/store', [ReservationController::class, 'store'])->name('reserve.store');
Route::get('/reserve/success/{id}', [ReservationController::class, 'success'])->name('reserve.success');
Route::get('/reserve/pending/{id}', [ReservationController::class, 'pending'])->name('reserve.pending');


// =============================================
// PUBLIC ROUTES (Pelanggan - Tanpa Login)
// =============================================
Route::get('/scan/{hash}', [OrderController::class, 'index'])->name('scan.qr');
Route::get('/menu/{id}', [OrderController::class, 'show'])->name('menu.show');
Route::post('/add-to-cart/{id}', [OrderController::class, 'addToCart'])->name('add-to-cart');
Route::post('/cart/update/{id}', [OrderController::class, 'updateCart'])->name('cart.update');
Route::post('/cart/remove/{id}', [OrderController::class, 'removeCart'])->name('cart.remove');
Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout');
Route::get('/search', [OrderController::class, 'search'])->name('order.search');
Route::get('/payment', [OrderController::class, 'payment'])->name('order.payment');

// Simpan nama customer ke session (dipanggil via fetch dari index.blade.php)
Route::post('/save-customer-name', function (\Illuminate\Http\Request $request) {
    $request->validate(['customer_name' => 'required|string|max:100']);
    session(['customer_name' => $request->customer_name]);
    return response()->json(['ok' => true]);
})->name('save.customer.name');

Route::post('/process-checkout', [OrderController::class, 'processCheckout'])->name('process-checkout');
Route::get('/order/success/{id}', [OrderController::class, 'paymentSuccess'])->name('order.success');
Route::get('/order/pending-cash/{id}', [OrderController::class, 'pendingCash'])->name('order.pending-cash');

// =============================================
// BREEZE AUTH ROUTES (Hanya Login Biasa)
// =============================================
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role.redirect'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// =============================================
// ADMIN ROUTES (Wajib Login & Wajib Role Admin/Kasir)
// =============================================
Route::middleware(['auth', 'verified', 'role:admin,kasir'])->group(function () {
    Route::get('/admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.index');
    Route::patch('/admin/orders/{id}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.update');
    Route::get('/admin/orders/{id}/print', [AdminOrderController::class, 'print'])->name('admin.orders.print');
    Route::get('/admin/reservations', [ReservationController::class, 'adminIndex'])->name('admin.reservations.index');
    Route::patch('/admin/reservations/{id}/status', [ReservationController::class, 'updateStatus'])->name('admin.reservations.updateStatus');
});

Route::middleware(['auth', 'verified', 'role:admin'])->group(function () {
    // Menu Management
    Route::get('/admin/menu', [App\Http\Controllers\Admin\MenuController::class, 'index'])->name('admin.menu.index');
    Route::get('/admin/menu/create', [App\Http\Controllers\Admin\MenuController::class, 'create'])->name('admin.menu.create');
    Route::post('/admin/menu', [App\Http\Controllers\Admin\MenuController::class, 'store'])->name('admin.menu.store');
    Route::get('/admin/menu/{id}/edit', [App\Http\Controllers\Admin\MenuController::class, 'edit'])->name('admin.menu.edit');
    Route::put('/admin/menu/{id}', [App\Http\Controllers\Admin\MenuController::class, 'update'])->name('admin.menu.update');
    Route::delete('/admin/menu/{id}', [App\Http\Controllers\Admin\MenuController::class, 'destroy'])->name('admin.menu.destroy');
    Route::post('/admin/menu/{id}/restore', [App\Http\Controllers\Admin\MenuController::class, 'restore'])->name('admin.menu.restore');
    
    // Table Management
    Route::get('/admin/tables', [App\Http\Controllers\Admin\TableController::class, 'index'])->name('admin.tables.index');
    Route::post('/admin/tables', [App\Http\Controllers\Admin\TableController::class, 'store'])->name('admin.tables.store');
    Route::get('/admin/tables/{id}/print', [App\Http\Controllers\Admin\TableController::class, 'print'])->name('admin.tables.print');
    Route::patch('/admin/tables/{id}/toggle-status', [App\Http\Controllers\Admin\TableController::class, 'toggleStatus'])->name('admin.tables.toggleStatus');
    Route::delete('/admin/tables/{id}', [App\Http\Controllers\Admin\TableController::class, 'destroy'])->name('admin.tables.destroy');
    
    // User Management
    Route::get('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('admin.users.index');
    Route::post('/admin/users', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('admin.users.store');
    Route::delete('/admin/users/{id}', [App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('admin.users.destroy');

    // Reservation Management (Owner)
    Route::get('/admin/reservations/{id}/edit', [ReservationController::class, 'edit'])->name('admin.reservations.edit');
    Route::put('/admin/reservations/{id}', [ReservationController::class, 'update'])->name('admin.reservations.update');
    Route::delete('/admin/reservations/{id}', [ReservationController::class, 'destroy'])->name('admin.reservations.destroy');

    //report
    Route::get('/admin/report', [AdminOrderController::class, 'report'])->name('admin.report.index');
});

Route::get('/order/track/{id}', [\App\Http\Controllers\OrderController::class, 'track'])->name('order.track');

require __DIR__.'/auth.php';