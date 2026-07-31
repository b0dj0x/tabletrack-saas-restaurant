<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Table;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\User;
use App\Models\Reservation;
use App\Models\PaymentReceipt;
use App\Models\SubscriptionPackage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RestaurantAdminController extends Controller
{
    private function getRestaurant()
    {
        return auth()->user()->restaurant_id 
            ? \App\Models\Restaurant::find(auth()->user()->restaurant_id)
            : null;
    }

    public function index()
    {
        $restaurant = $this->getRestaurant();
        if (!$restaurant) {
            abort(404, 'No restaurant found for your admin user.');
        }

        $ordersCount = $restaurant->orders()->count();
        $tablesCount = $restaurant->tables()->count();
        $reservationsCount = $restaurant->reservations()->count();
        $recentOrders = $restaurant->orders()->latest()->take(5)->get();

        return view('admin.dashboard', compact('restaurant', 'ordersCount', 'tablesCount', 'reservationsCount', 'recentOrders'));
    }

    public function areas()
    {
        $restaurant = $this->getRestaurant();
        $areas = Area::where('restaurant_id', $restaurant->id)->latest()->get();
        return view('admin.areas', compact('areas'));
    }

    public function storeArea(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $restaurant = $this->getRestaurant();

        Area::create([
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Floor area created successfully.');
    }

    public function tables()
    {
        $restaurant = $this->getRestaurant();
        $tables = Table::where('restaurant_id', $restaurant->id)->with('area')->latest()->get();
        $areas = Area::where('restaurant_id', $restaurant->id)->get();
        return view('admin.tables', compact('tables', 'areas'));
    }

    public function storeTable(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'area_id' => 'required|exists:areas,id',
            'capacity' => 'required|integer|min:1',
        ]);
        $restaurant = $this->getRestaurant();

        Table::create([
            'restaurant_id' => $restaurant->id,
            'area_id' => $request->area_id,
            'name' => $request->name,
            'capacity' => $request->capacity,
        ]);

        return back()->with('success', 'Table created successfully.');
    }

    public function updateTableStatus(Request $request, $id)
    {
        $table = Table::findOrFail($id);
        $table->update(['status' => $request->status]);
        return back()->with('success', 'Table status updated successfully.');
    }

    public function categories()
    {
        $restaurant = $this->getRestaurant();
        $categories = Category::where('restaurant_id', $restaurant->id)->latest()->get();
        return view('admin.categories', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $restaurant = $this->getRestaurant();

        Category::create([
            'restaurant_id' => $restaurant->id,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Category created successfully.');
    }

    public function menuItems()
    {
        $restaurant = $this->getRestaurant();
        $menuItems = MenuItem::where('restaurant_id', $restaurant->id)->with('category')->latest()->get();
        $categories = Category::where('restaurant_id', $restaurant->id)->get();
        return view('admin.menu_items', compact('menuItems', 'categories'));
    }

    public function storeMenuItem(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        $restaurant = $this->getRestaurant();

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create([
            'restaurant_id' => $restaurant->id,
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'image' => $imagePath,
        ]);

        return back()->with('success', 'Menu item added successfully.');
    }

    public function staff()
    {
        $restaurant = $this->getRestaurant();
        $staff = User::where('restaurant_id', $restaurant->id)->whereIn('role', ['waiter', 'cook'])->latest()->get();
        return view('admin.staff', compact('staff'));
    }

    public function storeStaff(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:waiter,cook',
        ]);
        $restaurant = $this->getRestaurant();

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'restaurant_id' => $restaurant->id,
        ]);

        return back()->with('success', 'Staff account created successfully.');
    }

    public function reservations()
    {
        $restaurant = $this->getRestaurant();
        $reservations = Reservation::where('restaurant_id', $restaurant->id)->with('table')->latest()->get();
        $tables = Table::where('restaurant_id', $restaurant->id)->get();
        return view('admin.reservations', compact('reservations', 'tables'));
    }

    public function storeReservation(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'table_id' => 'required|exists:tables,id',
            'reservation_time' => 'required|date',
            'party_size' => 'required|integer|min:1',
        ]);
        $restaurant = $this->getRestaurant();

        Reservation::create([
            'restaurant_id' => $restaurant->id,
            'table_id' => $request->table_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'reservation_time' => $request->reservation_time,
            'party_size' => $request->party_size,
        ]);

        return back()->with('success', 'Table reservation scheduled successfully.');
    }

    public function updateReservationStatus(Request $request, $id)
    {
        $res = Reservation::findOrFail($id);
        $res->update(['status' => $request->status]);
        return back()->with('success', 'Reservation status updated.');
    }

    public function paymentReceipts()
    {
        $restaurant = $this->getRestaurant();
        $receipts = PaymentReceipt::where('restaurant_id', $restaurant->id)->with('order')->latest()->get();
        return view('admin.payment_receipts', compact('receipts'));
    }

    public function verifyReceipt(Request $request, $id)
    {
        $receipt = PaymentReceipt::findOrFail($id);
        $receipt->update(['status' => $request->status]);

        if ($request->status === 'approved') {
            $receipt->order->update([
                'payment_status' => 'paid',
                'status' => 'completed',
            ]);
        } else {
            $receipt->order->update([
                'payment_status' => 'pending',
            ]);
        }

        return back()->with('success', 'Receipt status updated and order matching updated.');
    }

    public function billing()
    {
        $restaurant = $this->getRestaurant();
        $packages = SubscriptionPackage::all();
        return view('admin.billing', compact('restaurant', 'packages'));
    }

    public function subscribe(Request $request)
    {
        $request->validate(['package_id' => 'required|exists:subscription_packages,id']);
        $restaurant = $this->getRestaurant();
        $package = SubscriptionPackage::find($request->package_id);

        $restaurant->update([
            'subscription_package_id' => $package->id,
            'subscription_status' => 'active',
            'subscription_expires_at' => $package->billing_period === 'lifetime' 
                ? now()->addYears(99) 
                : ($package->billing_period === 'annually' ? now()->addYear() : now()->addMonth()),
        ]);

        return back()->with('success', 'Subscription updated successfully.');
    }
}
