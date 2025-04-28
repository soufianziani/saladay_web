<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/customer-order', [App\Http\Controllers\CustomerOrderController::class, 'index'])->name('customer.order');
Route::post('/customer-order', [App\Http\Controllers\CustomerOrderController::class, 'store'])->name('customer.order.store');
Route::get('/customer-order/{id}/receipt', [App\Http\Controllers\CustomerOrderController::class, 'getReceipt'])->name('customer.order.receipt');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
