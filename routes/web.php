<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->name('auth.')->group(function () {
    Route::get('/login',    App\Livewire\Auth\Login::class)->name('login')->middleware('guest');
    Route::get('/register', App\Livewire\Auth\Register::class)->name('register')->middleware('guest');
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('webpage.home');
    })->name('logout');
});

/*
|--------------------------------------------------------------------------
| Webpage (Public Storefront) Routes
|--------------------------------------------------------------------------
*/
Route::prefix('/')->name('webpage.')->middleware('website.live')->group(function () {
    Route::get('/',               App\Livewire\Webpage\Index::class)->name('home');
    Route::get('/shop',           App\Livewire\Webpage\Shop::class)->name('shop');
    Route::get('/product/{slug}', App\Livewire\Webpage\ProductDetails::class)->name('product-details');
    Route::get('/about',          App\Livewire\Webpage\About::class)->name('about');
    Route::get('/contact',        App\Livewire\Webpage\Contact::class)->name('contact');

    // Guest-accessible routes (no login required)
    Route::get('/cart',     App\Livewire\Webpage\Cart::class)->name('cart');
    Route::get('/checkout', App\Livewire\Webpage\Checkout::class)->name('checkout');
    Route::get('/orders',   App\Livewire\Webpage\Orders::class)->name('orders');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/',           App\Livewire\Admin\Dashboard::class)->name('dashboard');
    Route::get('/orders',     App\Livewire\Admin\Order::class)->name('orders');
    Route::get('/products',   App\Livewire\Admin\Product::class)->name('products');
    Route::get('/categories', App\Livewire\Admin\Category::class)->name('categories');
    Route::get('/customers',  App\Livewire\Admin\Customer::class)->name('customers');
    Route::get('/payments',             App\Livewire\Admin\Payment::class)->name('payments');
    Route::get('/payment-integration', App\Livewire\Admin\PaymentIntegration::class)->name('payment-integration');
    Route::get('/reports',              App\Livewire\Admin\Report::class)->name('reports');
    Route::get('/manual-order',         App\Livewire\Admin\ManualOrder::class)->name('manual-order');
    Route::get('/suppliers',            App\Livewire\Admin\Supplier::class)->name('suppliers');
    Route::get('/purchasing',           App\Livewire\Admin\Purchasing::class)->name('purchasing');
    Route::get('/website-settings',     App\Livewire\Admin\WebsiteSettings::class)->name('website-settings');
});

/*
|--------------------------------------------------------------------------
| Staff Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->name('staff.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('/',          App\Livewire\Staff\Order::class)->name('dashboard');
    Route::get('/orders',    App\Livewire\Staff\Order::class)->name('orders');
    Route::get('/customers', App\Livewire\Staff\Customer::class)->name('customers');
});
