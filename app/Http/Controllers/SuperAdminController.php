<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;
use App\Models\Restaurant;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;

class SuperAdminController extends Controller
{
    public function index()
    {
        $restaurantsCount = Restaurant::count();
        $packagesCount = SubscriptionPackage::count();
        $recentRestaurants = Restaurant::with('package', 'owner')->latest()->take(5)->get();
        return view('superadmin.dashboard', compact('restaurantsCount', 'packagesCount', 'recentRestaurants'));
    }

    public function restaurants()
    {
        $restaurants = Restaurant::with('package', 'owner')->latest()->paginate(15);
        return view('superadmin.restaurants', compact('restaurants'));
    }

    public function approveRestaurant($id)
    {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update(['subscription_status' => 'active', 'subscription_expires_at' => now()->addDays(30)]);
        return back()->with('success', 'Restaurant subscription has been approved and activated.');
    }

    public function packages()
    {
        $packages = SubscriptionPackage::all();
        return view('superadmin.packages', compact('packages'));
    }

    public function storePackage(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'billing_period' => 'required|in:monthly,annually,lifetime',
            'price' => 'required|numeric|min:0',
            'features' => 'required|array',
        ]);

        SubscriptionPackage::create([
            'name' => $request->name,
            'billing_period' => $request->billing_period,
            'price' => $request->price,
            'features' => $request->features,
        ]);

        return back()->with('success', 'Pricing package created successfully.');
    }

    public function payments()
    {
        $receipts = PaymentReceipt::with('restaurant', 'order')->latest()->paginate(15);
        return view('superadmin.payments', compact('receipts'));
    }
}
