<?php

use App\Http\Controllers\CustomerOrderController;

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/customer-order', [App\Http\Controllers\CustomerOrderController::class, 'index'])->name('customer.order');
Route::post('/customer-order', [App\Http\Controllers\CustomerOrderController::class, 'store'])->name('customer.order.store');
Route::get('/customer-order/receipt/{id}', [App\Http\Controllers\CustomerOrderController::class, 'getReceipt'])->name('customer.order.receipt');
Route::post('/customer-order/filtered-receipt', [App\Http\Controllers\CustomerOrderController::class, 'getFilteredReceipt'])->name('customer.order.filtered-receipt');
Route::delete('/customer-order/{id}', [App\Http\Controllers\CustomerOrderController::class, 'destroy'])
    ->middleware(['auth', 'verified'])
    ->name('customer.order.destroy');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/orders', function () {
    $orders = \App\Models\Order::with(['orderItems.product'])
        ->orderBy('created_at', 'desc')
        ->get();
        
    return Inertia::render('Orders', [
        'orders' => $orders
    ]);
})->middleware(['auth', 'verified'])->name('orders');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
