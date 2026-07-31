<?php

namespace App\Http\Controllers;

use App\Models\SubscriptionPackage;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SaaSController extends Controller
{
    public function landing()
    {
        $packages = SubscriptionPackage::all();
        $restaurants = Restaurant::with('owner')->latest()->take(3)->get();
        return view('landing', compact('packages', 'restaurants'));
    }

    public function pricing()
    {
        $packages = SubscriptionPackage::all();
        return view('pricing', compact('packages'));
    }

    public function signupForm()
    {
        $packages = SubscriptionPackage::all();
        return view('signup', compact('packages'));
    }

    public function registerRestaurant(Request $request)
    {
        $request->validate([
            'restaurant_name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'package_id' => 'required|exists:subscription_packages,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
        ]);

        $owner = User::create([
            'name' => $request->owner_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        $slug = Str::slug($request->restaurant_name);
        $originalSlug = $slug;
        $counter = 1;
        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $restaurant = Restaurant::create([
            'name' => $request->restaurant_name,
            'slug' => $slug,
            'user_id' => $owner->id,
            'subscription_package_id' => $request->package_id,
            'subscription_status' => 'active',
            'subscription_expires_at' => now()->addDays(30),
            'address' => $request->address,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $owner->update(['restaurant_id' => $restaurant->id]);

        auth()->login($owner);

        return redirect()->route('admin.dashboard')->with('success', 'Your restaurant SaaS account has been created successfully! Welcome to TableTrack!');
    }

    public function restaurantsList(Request $request)
    {
        $query = Restaurant::query()->where('subscription_status', 'active');

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
        }

        $restaurants = $query->latest()->paginate(12);

        return view('restaurants.index', compact('restaurants'));
    }
}
