import os

print("Starting full TableTrack recreation script...")

# 1. Models
models = {
    'app/Models/SubscriptionPackage.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class SubscriptionPackage extends Model {
    use HasFactory;
    protected $fillable = ['name', 'billing_period', 'price', 'features'];
    protected $casts = ['features' => 'array', 'price' => 'decimal:2'];
    public function restaurants() { return $this->hasMany(Restaurant::class); }
}""",

    'app/Models/Restaurant.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Restaurant extends Model {
    use HasFactory;
    protected $fillable = ['name', 'slug', 'user_id', 'subscription_package_id', 'subscription_status', 'subscription_expires_at', 'logo', 'address', 'email', 'phone', 'baridimob_rip', 'baridimob_qr'];
    protected $casts = ['subscription_expires_at' => 'datetime'];
    public function owner() { return $this->belongsTo(User::class, 'user_id'); }
    public function package() { return $this->belongsTo(SubscriptionPackage::class, 'subscription_package_id'); }
    public function areas() { return $this->hasMany(Area::class); }
    public function tables() { return $this->hasMany(Table::class); }
    public function categories() { return $this->hasMany(Category::class); }
    public function menuItems() { return $this->hasMany(MenuItem::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
}""",

    'app/Models/Area.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Area extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'name'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function tables() { return $this->hasMany(Table::class); }
}""",

    'app/Models/Table.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Table extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'area_id', 'name', 'capacity', 'status'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function area() { return $this->belongsTo(Area::class); }
    public function orders() { return $this->hasMany(Order::class); }
    public function reservations() { return $this->hasMany(Reservation::class); }
}""",

    'app/Models/Category.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Category extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'name'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function menuItems() { return $this->hasMany(MenuItem::class); }
}""",

    'app/Models/MenuItem.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class MenuItem extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'category_id', 'name', 'description', 'price', 'image', 'is_available'];
    protected $casts = ['is_available' => 'boolean', 'price' => 'decimal:2'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}""",

    'app/Models/Order.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'table_id', 'waiter_id', 'status', 'payment_status', 'payment_method', 'total_price', 'type', 'customer_name', 'customer_phone', 'notes'];
    protected $casts = ['total_price' => 'decimal:2'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function table() { return $this->belongsTo(Table::class); }
    public function waiter() { return $this->belongsTo(User::class, 'waiter_id'); }
    public function items() { return $this->hasMany(OrderItem::class); }
    public function paymentReceipts() { return $this->hasMany(PaymentReceipt::class); }
}""",

    'app/Models/OrderItem.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
    use HasFactory;
    protected $fillable = ['order_id', 'menu_item_id', 'quantity', 'price', 'kot_status'];
    protected $casts = ['price' => 'decimal:2'];
    public function order() { return $this->belongsTo(Order::class); }
    public function menuItem() { return $this->belongsTo(MenuItem::class); }
}""",

    'app/Models/Reservation.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Reservation extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'table_id', 'customer_name', 'customer_phone', 'reservation_time', 'party_size', 'status'];
    protected $casts = ['reservation_time' => 'datetime'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function table() { return $this->belongsTo(Table::class); }
}""",

    'app/Models/PaymentReceipt.php': """<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PaymentReceipt extends Model {
    use HasFactory;
    protected $fillable = ['restaurant_id', 'order_id', 'amount', 'transaction_id', 'receipt_screenshot', 'status'];
    protected $casts = ['amount' => 'decimal:2'];
    public function restaurant() { return $this->belongsTo(Restaurant::class); }
    public function order() { return $this->belongsTo(Order::class); }
}"""
}

# 2. Migrations
migrations = {
    'database/migrations/2026_07_31_110708_create_areas_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('areas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('areas'); }
};""",

    'database/migrations/2026_07_31_110708_create_categories_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('categories'); }
};""",

    'database/migrations/2026_07_31_110708_create_menu_items_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 8, 2);
            $table->string('image')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('menu_items'); }
};""",

    'database/migrations/2026_07_31_110708_create_tables_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->integer('capacity')->default(2);
            $table->string('status')->default('available');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('tables'); }
};""",

    'database/migrations/2026_07_31_110709_create_orders_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('waiter_id')->nullable()->references('id')->on('users')->nullOnDelete();
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->decimal('total_price', 8, 2)->default(0.00);
            $table->string('type')->default('dine_in');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};""",

    'database/migrations/2026_07_31_110709_create_order_items_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2);
            $table->string('kot_status')->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('order_items'); }
};""",

    'database/migrations/2026_07_31_110709_create_payment_receipts_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('payment_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 8, 2);
            $table->string('transaction_id')->nullable();
            $table->string('receipt_screenshot')->nullable();
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('payment_receipts'); }
};""",

    'database/migrations/2026_07_31_110709_create_reservations_table.php': """<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restaurant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('table_id')->constrained()->cascadeOnDelete();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->dateTime('reservation_time');
            $table->integer('party_size');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('reservations'); }
};"""
}

# 3. Middlewares
middlewares = {
    'app/Http/Middleware/TenantMiddleware.php': """<?php
namespace App\Http\Middleware;
use Closure;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class TenantMiddleware {
    public function handle(Request $request, Closure $next): Response {
        $slug = $request->route('restaurant_slug');
        if ($slug) {
            $restaurant = Restaurant::where('slug', $slug)->first();
            if (!$restaurant) { abort(404, 'Restaurant not found.'); }
            if ($restaurant->subscription_status !== 'active' && now()->greaterThan($restaurant->subscription_expires_at)) {
                abort(403, "This restaurant's subscription is currently inactive.");
            }
            app()->instance('current_restaurant', $restaurant);
            view()->share('current_restaurant', $restaurant);
        }
        return $next($request);
    }
}""",

    'app/Http/Middleware/SuperAdminMiddleware.php': """<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class SuperAdminMiddleware {
    public function handle(Request $request, Closure $next): Response {
        if (!auth()->check() || auth()->user()->role !== 'superadmin') {
            abort(403, 'Unauthorized access. Super Admin role required.');
        }
        return $next($request);
    }
}""",

    'app/Http/Middleware/RestaurantAdminMiddleware.php': """<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class RestaurantAdminMiddleware {
    public function handle(Request $request, Closure $next): Response {
        if (!auth()->check() || auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized access. Restaurant Admin role required.');
        }
        return $next($request);
    }
}""",

    'app/Http/Middleware/StaffMiddleware.php': """<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class StaffMiddleware {
    public function handle(Request $request, Closure $next): Response {
        if (!auth()->check() || !in_array(auth()->user()->role, ['admin', 'waiter', 'cook'])) {
            abort(403, 'Unauthorized access. Staff credentials required.');
        }
        return $next($request);
    }
}"""
}

# 4. Controllers
controllers = {
    'app/Http/Controllers/SaaSController.php': """<?php
namespace App\Http\Controllers;
use App\Models\SubscriptionPackage;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class SaaSController extends Controller {
    public function landing() {
        $packages = SubscriptionPackage::all();
        $restaurants = Restaurant::with('owner')->latest()->take(3)->get();
        return view('landing', compact('packages', 'restaurants'));
    }
    public function pricing() {
        $packages = SubscriptionPackage::all();
        return view('pricing', compact('packages'));
    }
    public function signupForm() {
        $packages = SubscriptionPackage::all();
        return view('signup', compact('packages'));
    }
    public function registerRestaurant(Request $request) {
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
        $originalSlug = $slug; $counter = 1;
        while (Restaurant::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter; $counter++;
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
        return redirect()->route('admin.dashboard')->with('success', 'Created!');
    }
    public function restaurantsList(Request $request) {
        $query = Restaurant::query()->where('subscription_status', 'active');
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%');
        }
        $restaurants = $query->latest()->paginate(12);
        return view('restaurants.index', compact('restaurants'));
    }
}""",

    'app/Http/Controllers/CustomerMenuController.php': """<?php
namespace App\Http\Controllers;
use App\Models\Restaurant;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;
class CustomerMenuController extends Controller {
    public function showMenu($restaurant_slug) {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
        $tables = Table::where('restaurant_id', $restaurant->id)->get();
        return view('menu.show', compact('restaurant', 'categories', 'tables'));
    }
    public function placeOrder(Request $request, $restaurant_slug) {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'type' => 'required|in:dine_in,takeaway,delivery',
            'table_id' => 'nullable|exists:tables,id',
            'cart' => 'required|array',
            'cart.*.id' => 'required|exists:menu_items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);
        $totalPrice = 0; $orderItemsData = [];
        foreach ($request->cart as $cartItem) {
            $menuItem = MenuItem::findOrFail($cartItem['id']);
            $price = $menuItem->price; $quantity = $cartItem['quantity'];
            $totalPrice += $price * $quantity;
            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id, 'quantity' => $quantity, 'price' => $price, 'kot_status' => 'pending',
            ];
        }
        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'table_id' => $request->type === 'dine_in' ? $request->table_id : null,
            'status' => 'pending', 'payment_status' => 'pending', 'total_price' => $totalPrice, 'type' => $request->type,
            'customer_name' => $request->customer_name, 'customer_phone' => $request->customer_phone, 'notes' => $request->notes,
        ]);
        foreach ($orderItemsData as $itemData) { $order->items()->create($itemData); }
        if ($request->type === 'dine_in' && $request->table_id) {
            Table::where('id', $request->table_id)->update(['status' => 'occupied']);
        }
        return response()->json([
            'success' => true,
            'redirect_url' => route('customer.checkout', [$restaurant->slug, $order->id]),
        ]);
    }
    public function checkout($restaurant_slug, $order_id) {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $order = Order::with('items.menuItem')->where('id', $order_id)->firstOrFail();
        return view('menu.checkout', compact('restaurant', 'order'));
    }
    public function processPayment(Request $request, $restaurant_slug, $order_id) {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $order = Order::findOrFail($order_id);
        $request->validate([
            'payment_method' => 'required|in:cash,baridimob',
            'transaction_id' => 'required_if:payment_method,baridimob|nullable|string|max:255',
            'receipt_screenshot' => 'required_if:payment_method,baridimob|nullable|image|max:4096',
        ]);
        $order->update([
            'payment_method' => $request->payment_method, 'payment_status' => 'pending',
        ]);
        if ($request->payment_method === 'baridimob') {
            $screenshotPath = null;
            if ($request->hasFile('receipt_screenshot')) {
                $screenshotPath = $request->file('receipt_screenshot')->store('receipts', 'public');
            }
            PaymentReceipt::create([
                'restaurant_id' => $restaurant->id, 'order_id' => $order->id, 'amount' => $order->total_price,
                'transaction_id' => $request->transaction_id, 'receipt_screenshot' => $screenshotPath, 'status' => 'pending',
            ]);
        }
        return redirect()->route('customer.order.success', [$restaurant->slug, $order->id]);
    }
    public function success($restaurant_slug, $order_id) {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $order = Order::with('items.menuItem')->findOrFail($order_id);
        return view('menu.success', compact('restaurant', 'order'));
    }
}""",

    'app/Http/Controllers/SuperAdminController.php': """<?php
namespace App\Http\Controllers;
use App\Models\SubscriptionPackage;
use App\Models\Restaurant;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;
class SuperAdminController extends Controller {
    public function index() {
        $restaurantsCount = Restaurant::count(); $packagesCount = SubscriptionPackage::count();
        $recentRestaurants = Restaurant::with('package', 'owner')->latest()->take(5)->get();
        return view('superadmin.dashboard', compact('restaurantsCount', 'packagesCount', 'recentRestaurants'));
    }
    public function restaurants() {
        $restaurants = Restaurant::with('package', 'owner')->latest()->paginate(15);
        return view('superadmin.restaurants', compact('restaurants'));
    }
    public function approveRestaurant($id) {
        $restaurant = Restaurant::findOrFail($id);
        $restaurant->update(['subscription_status' => 'active', 'subscription_expires_at' => now()->addDays(30)]);
        return back()->with('success', 'Activated.');
    }
    public function packages() {
        $packages = SubscriptionPackage::all();
        return view('superadmin.packages', compact('packages'));
    }
    public function storePackage(Request $request) {
        $request->validate(['name' => 'required|string', 'billing_period' => 'required', 'price' => 'required|numeric', 'features' => 'required|array']);
        SubscriptionPackage::create($request->all());
        return back()->with('success', 'Created.');
    }
    public function payments() {
        $receipts = PaymentReceipt::with('restaurant', 'order')->latest()->paginate(15);
        return view('superadmin.payments', compact('receipts'));
    }
}""",

    'app/Http/Controllers/RestaurantAdminController.php': """<?php
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
class RestaurantAdminController extends Controller {
    private function getRestaurant() {
        return auth()->user()->restaurant_id ? \App\Models\Restaurant::find(auth()->user()->restaurant_id) : null;
    }
    public function index() {
        $restaurant = $this->getRestaurant();
        $ordersCount = $restaurant->orders()->count(); $tablesCount = $restaurant->tables()->count(); $reservationsCount = $restaurant->reservations()->count();
        $recentOrders = $restaurant->orders()->latest()->take(5)->get();
        return view('admin.dashboard', compact('restaurant', 'ordersCount', 'tablesCount', 'reservationsCount', 'recentOrders'));
    }
    public function areas() {
        $restaurant = $this->getRestaurant();
        $areas = Area::where('restaurant_id', $restaurant->id)->latest()->get();
        return view('admin.areas', compact('areas'));
    }
    public function storeArea(Request $request) {
        $request->validate(['name' => 'required|string']);
        Area::create(['restaurant_id' => $this->getRestaurant()->id, 'name' => $request->name]);
        return back()->with('success', 'Created.');
    }
    public function tables() {
        $restaurant = $this->getRestaurant();
        $tables = Table::where('restaurant_id', $restaurant->id)->with('area')->latest()->get();
        $areas = Area::where('restaurant_id', $restaurant->id)->get();
        return view('admin.tables', compact('tables', 'areas'));
    }
    public function storeTable(Request $request) {
        $request->validate(['name' => 'required', 'area_id' => 'required', 'capacity' => 'required']);
        Table::create(array_merge($request->all(), ['restaurant_id' => $this->getRestaurant()->id]));
        return back()->with('success', 'Created.');
    }
    public function updateTableStatus(Request $request, $id) {
        Table::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Updated.');
    }
    public function categories() {
        $categories = Category::where('restaurant_id', $this->getRestaurant()->id)->get();
        return view('admin.categories', compact('categories'));
    }
    public function storeCategory(Request $request) {
        Category::create(['restaurant_id' => $this->getRestaurant()->id, 'name' => $request->name]);
        return back()->with('success', 'Created.');
    }
    public function menuItems() {
        $restaurant = $this->getRestaurant();
        $menuItems = MenuItem::where('restaurant_id', $restaurant->id)->with('category')->get();
        $categories = Category::where('restaurant_id', $restaurant->id)->get();
        return view('admin.menu_items', compact('menuItems', 'categories'));
    }
    public function storeMenuItem(Request $request) {
        $request->validate(['name' => 'required', 'category_id' => 'required', 'price' => 'required']);
        MenuItem::create(array_merge($request->all(), ['restaurant_id' => $this->getRestaurant()->id]));
        return back()->with('success', 'Created.');
    }
    public function staff() {
        $staff = User::where('restaurant_id', $this->getRestaurant()->id)->whereIn('role', ['waiter', 'cook'])->get();
        return view('admin.staff', compact('staff'));
    }
    public function storeStaff(Request $request) {
        $request->validate(['name' => 'required', 'email' => 'required|email|unique:users', 'password' => 'required', 'role' => 'required']);
        User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password), 'role' => $request->role, 'restaurant_id' => $this->getRestaurant()->id]);
        return back()->with('success', 'Created.');
    }
    public function reservations() {
        $restaurant = $this->getRestaurant();
        $reservations = Reservation::where('restaurant_id', $restaurant->id)->get();
        $tables = Table::where('restaurant_id', $restaurant->id)->get();
        return view('admin.reservations', compact('reservations', 'tables'));
    }
    public function storeReservation(Request $request) {
        Reservation::create(array_merge($request->all(), ['restaurant_id' => $this->getRestaurant()->id]));
        return back()->with('success', 'Reserved.');
    }
    public function updateReservationStatus(Request $request, $id) {
        Reservation::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Updated.');
    }
    public function paymentReceipts() {
        $receipts = PaymentReceipt::where('restaurant_id', $this->getRestaurant()->id)->get();
        return view('admin.payment_receipts', compact('receipts'));
    }
    public function verifyReceipt(Request $request, $id) {
        $receipt = PaymentReceipt::findOrFail($id);
        $receipt->update(['status' => $request->status]);
        if ($request->status === 'approved') {
            $receipt->order->update(['payment_status' => 'paid', 'status' => 'completed']);
        }
        return back()->with('success', 'Verified.');
    }
    public function billing() {
        $restaurant = $this->getRestaurant(); $packages = SubscriptionPackage::all();
        return view('admin.billing', compact('restaurant', 'packages'));
    }
    public function subscribe(Request $request) {
        $restaurant = $this->getRestaurant(); $package = SubscriptionPackage::find($request->package_id);
        $restaurant->update(['subscription_package_id' => $package->id, 'subscription_status' => 'active', 'subscription_expires_at' => now()->addMonth()]);
        return back()->with('success', 'Subscribed.');
    }
}""",

    'app/Http/Controllers/KitchenPOSController.php': """<?php
namespace App\Http\Controllers;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
class KitchenPOSController extends Controller {
    private function getRestaurant() {
        return auth()->user()->restaurant_id ? Restaurant::find(auth()->user()->restaurant_id) : null;
    }
    public function pos() {
        $restaurant = $this->getRestaurant();
        $tables = Table::where('restaurant_id', $restaurant->id)->get();
        $menuItems = MenuItem::where('restaurant_id', $restaurant->id)->get();
        return view('staff.pos', compact('restaurant', 'tables', 'menuItems'));
    }
    public function placePosOrder(Request $request) {
        $restaurant = $this->getRestaurant();
        $totalPrice = 0; $orderItemsData = [];
        foreach ($request->items as $item) {
            $menuItem = MenuItem::findOrFail($item['id']);
            $totalPrice += $menuItem->price * $item['quantity'];
            $orderItemsData[] = ['menu_item_id' => $menuItem->id, 'quantity' => $item['quantity'], 'price' => $menuItem->price, 'kot_status' => 'pending'];
        }
        $order = Order::create([
            'restaurant_id' => $restaurant->id, 'table_id' => $request->table_id, 'waiter_id' => auth()->id(),
            'status' => 'preparing', 'payment_status' => 'paid', 'payment_method' => 'cash', 'total_price' => $totalPrice, 'type' => $request->type,
        ]);
        foreach ($orderItemsData as $itemData) { $order->items()->create($itemData); }
        return response()->json(['success' => true, 'bill_url' => route('staff.pos.bill', $order->id)]);
    }
    public function printBill($id) {
        $order = Order::with('items.menuItem', 'restaurant')->findOrFail($id);
        return view('staff.bill', compact('order'));
    }
    public function kitchen() {
        $orders = Order::where('restaurant_id', $this->getRestaurant()->id)->whereIn('status', ['pending', 'preparing'])->with('items.menuItem')->get();
        return view('staff.kitchen', compact('orders'));
    }
    public function updateItemStatus(Request $request, $id) {
        OrderItem::findOrFail($id)->update(['kot_status' => $request->status]);
        return back()->with('success', 'Updated.');
    }
    public function updateOrderStatus(Request $request, $id) {
        Order::findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Updated.');
    }
}"""
}

# Write Models
for path, code in models.items():
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w') as f:
        f.write(code)
    print(f"Written model: {path}")

# Write Migrations
for path, code in migrations.items():
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w') as f:
        f.write(code)
    print(f"Written migration: {path}")

# Write Middlewares
for path, code in middlewares.items():
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w') as f:
        f.write(code)
    print(f"Written middleware: {path}")

# Write Controllers
for path, code in controllers.items():
    os.makedirs(os.path.dirname(path), exist_ok=True)
    with open(path, 'w') as f:
        f.write(code)
    print(f"Written controller: {path}")

print("All classes and files recreated successfully!")
