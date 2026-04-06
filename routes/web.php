<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
// ── Payment Gateway Pages (no auth required) ─────────────────────────────
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/payhere/{order}',         [PaymentController::class, 'payhereForm'])->name('payhere');
    Route::post('/payhere/{order}/process',[PaymentController::class, 'payhereProcess'])->name('payhere.process');
    Route::get('/paypal/{order}',          [PaymentController::class, 'paypalForm'])->name('paypal');
    Route::post('/paypal/{order}/process', [PaymentController::class, 'paypalProcess'])->name('paypal.process');
    Route::get('/success/{order}',         [PaymentController::class, 'success'])->name('success');
});

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
| WhatsApp Order Form — Public, no auth, no maintenance check
| Must be declared BEFORE the website.live middleware group so it is always
| accessible even when the storefront is in maintenance mode.
|--------------------------------------------------------------------------
*/
Route::get('/order/whatsapp/{token}', App\Livewire\Webpage\WhatsappOrderForm::class)
    ->name('whatsapp.order.form');

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
    Route::get('/reviews',        App\Livewire\Webpage\Reviews::class)->name('reviews');

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
    Route::get('/shipments',            App\Livewire\Admin\Shipment::class)->name('shipments');
    Route::get('/website-settings',     App\Livewire\Admin\WebsiteSettings::class)->name('website-settings');
    Route::get('/whatsapp-orders',      App\Livewire\Admin\WhatsappOrders::class)->name('whatsapp-orders');

    // Payment Management
    Route::get('/supplier-payments', App\Livewire\Admin\SupplierPayments::class)->name('supplier-payments');
    Route::get('/customer-payments', App\Livewire\Admin\CustomerPayments::class)->name('customer-payments');

    Route::get('/returns', App\Livewire\Admin\Returns::class)->name('returns');
    Route::get('/profile', App\Livewire\Admin\Profile::class)->name('profile');

    // Waybill / Packing Slip — plain print view, not a Livewire component
    Route::get('/orders/{order}/waybill', function (App\Models\Order $order) {
        $order->load(['items', 'shipmentBatch']);
        $addr = $order->shipping_address ?? [];
        return view('admin.waybill', compact('order', 'addr'));
    })->name('order.waybill');
});

/*
|--------------------------------------------------------------------------
| Staff Panel Routes
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->name('staff.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('/',                 App\Livewire\Staff\Dashboard::class)->name('dashboard');
    Route::get('/orders',           App\Livewire\Admin\Order::class)->name('orders');
    Route::get('/whatsapp-orders',  App\Livewire\Admin\WhatsappOrders::class)->name('whatsapp-orders');
    Route::get('/payments',         App\Livewire\Admin\Payment::class)->name('payments');
    Route::get('/returns',          App\Livewire\Admin\Returns::class)->name('returns');
    Route::get('/notifications',    App\Livewire\Staff\Notifications::class)->name('notifications');
    Route::get('/customers',        App\Livewire\Staff\Customer::class)->name('customers');
    Route::get('/profile',          App\Livewire\Admin\Profile::class)->name('profile');
});

// Jetstream session-auth middleware group — kept for Jetstream's own routes.
// The generic /dashboard route is intentionally removed; role-based redirects
// are handled in FortifyServiceProvider::redirectUsersTo().
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    // Reserved for any future Jetstream-gated routes.
});
