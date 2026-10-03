<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ShopController::class, 'index'])->name('home');
Route::get('/p/{product}', [ShopController::class, 'show'])->name('product');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/policy', [PageController::class, 'policy'])->name('policy');

Route::post('/bag', [CartController::class, 'store'])->name('bag.store');
Route::patch('/bag/{key}', [CartController::class, 'update'])->name('bag.update');
Route::delete('/bag/{key}', [CartController::class, 'destroy'])->name('bag.destroy');

Route::get('/checkout', [CheckoutController::class, 'show'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');

Route::get('/pay/{order}', [PaymentController::class, 'show'])->name('pay');
Route::post('/pay/{order}/verify', [PaymentController::class, 'verify'])->name('pay.verify');
Route::post('/pay/{order}/simulate', [PaymentController::class, 'simulate'])->name('pay.simulate');
Route::post('/razorpay/webhook', [PaymentController::class, 'webhook'])->name('razorpay.webhook');

Route::get('/order/{order}', [OrderController::class, 'show'])->middleware('signed')->name('order.show');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [Admin\AuthController::class, 'create'])->name('login');
    Route::post('login', [Admin\AuthController::class, 'store'])->middleware('throttle:6,1');

    Route::middleware(\App\Http\Middleware\EnsureAdmin::class)->group(function () {
        Route::post('logout', [Admin\AuthController::class, 'destroy'])->name('logout');

        Route::redirect('/', '/admin/orders');
        Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
        Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
        Route::patch('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

        Route::resource('products', Admin\ProductController::class)->except('show');
    });
});
