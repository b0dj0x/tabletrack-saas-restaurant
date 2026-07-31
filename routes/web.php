<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaaSController;
use App\Http\Controllers\CustomerMenuController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\RestaurantAdminController;
use App\Http\Controllers\KitchenPOSController;
use Illuminate\Support\Facades\Route;

// 1. SaaS Public Landing & Information Pages
Route::get('/', [SaaSController::class, 'landing'])->name('landing');
Route::get('/pricing', [SaaSController::class, 'pricing'])->name('pricing');
Route::get('/restaurant-signup', [SaaSController::class, 'signupForm'])->name('signup.form');
Route::post('/restaurant-signup', [SaaSController::class, 'registerRestaurant'])->name('signup.submit');

// 2. Public Restaurant Directory for Customers
Route::get('/restaurants', [SaaSController::class, 'restaurantsList'])->name('restaurants.list');

// 3. Contactless QR Code Customer Interactive Menu (Tenant Bound)
Route::middleware('tenant')->group(function () {
    Route::get('/r/{restaurant_slug}', [CustomerMenuController::class, 'showMenu'])->name('customer.menu');
    Route::post('/r/{restaurant_slug}/order', [CustomerMenuController::class, 'placeOrder'])->name('customer.order.submit');
    Route::get('/r/{restaurant_slug}/order/{order_id}/checkout', [CustomerMenuController::class, 'checkout'])->name('customer.checkout');
    Route::post('/r/{restaurant_slug}/order/{order_id}/pay', [CustomerMenuController::class, 'processPayment'])->name('customer.payment.process');
    Route::get('/r/{restaurant_slug}/order/{order_id}/success', [CustomerMenuController::class, 'success'])->name('customer.order.success');
});

// 4. Super Admin Dashboard (SaaS-wide management)
Route::middleware(['auth', 'superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'index'])->name('dashboard');
    Route::get('/restaurants', [SuperAdminController::class, 'restaurants'])->name('restaurants');
    Route::post('/restaurants/{id}/approve', [SuperAdminController::class, 'approveRestaurant'])->name('restaurants.approve');
    Route::get('/packages', [SuperAdminController::class, 'packages'])->name('packages');
    Route::post('/packages/store', [SuperAdminController::class, 'storePackage'])->name('packages.store');
    Route::get('/payments', [SuperAdminController::class, 'payments'])->name('payments');
});

// 5. Restaurant Owner Admin Dashboard (Specific Restaurant control)
Route::middleware(['auth', 'restaurant_admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [RestaurantAdminController::class, 'index'])->name('dashboard');
    
    // Areas & Tables
    Route::get('/areas', [RestaurantAdminController::class, 'areas'])->name('areas');
    Route::post('/areas', [RestaurantAdminController::class, 'storeArea'])->name('areas.store');
    Route::get('/tables', [RestaurantAdminController::class, 'tables'])->name('tables');
    Route::post('/tables', [RestaurantAdminController::class, 'storeTable'])->name('tables.store');
    Route::post('/tables/{id}/status', [RestaurantAdminController::class, 'updateTableStatus'])->name('tables.status');

    // Menu Categories & Items
    Route::get('/categories', [RestaurantAdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [RestaurantAdminController::class, 'storeCategory'])->name('categories.store');
    Route::get('/menu-items', [RestaurantAdminController::class, 'menuItems'])->name('menu_items');
    Route::post('/menu-items', [RestaurantAdminController::class, 'storeMenuItem'])->name('menu_items.store');

    // Staff management
    Route::get('/staff', [RestaurantAdminController::class, 'staff'])->name('staff');
    Route::post('/staff', [RestaurantAdminController::class, 'storeStaff'])->name('staff.store');

    // Reservations
    Route::get('/reservations', [RestaurantAdminController::class, 'reservations'])->name('reservations');
    Route::post('/reservations', [RestaurantAdminController::class, 'storeReservation'])->name('reservations.store');
    Route::post('/reservations/{id}/status', [RestaurantAdminController::class, 'updateReservationStatus'])->name('reservations.status');

    // Payment receipts from customers (BaridiMob transfers verification)
    Route::get('/payment-receipts', [RestaurantAdminController::class, 'paymentReceipts'])->name('payment_receipts');
    Route::post('/payment-receipts/{id}/verify', [RestaurantAdminController::class, 'verifyReceipt'])->name('payment_receipts.verify');

    // Billing/Subscription Page
    Route::get('/billing', [RestaurantAdminController::class, 'billing'])->name('billing');
    Route::post('/billing/subscribe', [RestaurantAdminController::class, 'subscribe'])->name('billing.subscribe');
});

// 6. Kitchen & POS Views (Waiters & Cooks)
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    // POS Terminal
    Route::get('/pos', [KitchenPOSController::class, 'pos'])->name('pos');
    Route::post('/pos/order', [KitchenPOSController::class, 'placePosOrder'])->name('pos.order');
    Route::get('/pos/order/{id}/bill', [KitchenPOSController::class, 'printBill'])->name('pos.bill');

    // Kitchen Order Tickets (KOT) Live Grid
    Route::get('/kitchen', [KitchenPOSController::class, 'kitchen'])->name('kitchen');
    Route::post('/kitchen/order-item/{id}/status', [KitchenPOSController::class, 'updateItemStatus'])->name('kitchen.item.status');
    Route::post('/kitchen/order/{id}/status', [KitchenPOSController::class, 'updateOrderStatus'])->name('kitchen.order.status');
});

// 7. Profile Settings (Scaffolded)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Root fallback to handle authentication routing redirects safely
Route::get('/dashboard', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    if (auth()->user()->role === 'superadmin') {
        return redirect()->route('superadmin.dashboard');
    }
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if (in_array(auth()->user()->role, ['waiter', 'cook'])) {
        return redirect()->route('staff.pos');
    }
    return redirect()->route('landing');
})->name('dashboard');

require __DIR__.'/auth.php';
