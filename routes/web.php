<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webpage (Public Storefront) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/')->name('webpage.')->group(function () {
    Route::get('/',               App\Livewire\Webpage\Index::class)->name('home');
    Route::get('/shop',           App\Livewire\Webpage\Shop::class)->name('shop');
    Route::get('/product/{slug}', App\Livewire\Webpage\ProductDetails::class)->name('product-details');
    Route::get('/cart',           App\Livewire\Webpage\Cart::class)->name('cart');
    Route::get('/checkout',       App\Livewire\Webpage\Checkout::class)->name('checkout');
    Route::get('/orders',         App\Livewire\Webpage\Orders::class)->name('orders');
    Route::get('/about',          App\Livewire\Webpage\About::class)->name('about');
    Route::get('/contact',        App\Livewire\Webpage\Contact::class)->name('contact');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/',          App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/orders',    App\Livewire\Admin\Order::class)->name('orders');
    Route::get('/products',  App\Livewire\Admin\Product::class)->name('products');
    Route::get('/categories',App\Livewire\Admin\Category::class)->name('categories');
    Route::get('/customers', App\Livewire\Admin\Customer::class)->name('customers');
    Route::get('/payments',  App\Livewire\Admin\Payment::class)->name('payments');
    Route::get('/reports',   App\Livewire\Admin\Report::class)->name('reports');
});

/*
|--------------------------------------------------------------------------
| Staff Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->name('staff.')->group(function () {
    Route::get('/',          App\Livewire\Staff\Order::class)->name('dashboard');
    Route::get('/orders',    App\Livewire\Staff\Order::class)->name('orders');
    Route::get('/customers', App\Livewire\Staff\Customer::class)->name('customers');
});
