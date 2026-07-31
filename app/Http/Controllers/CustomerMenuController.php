<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\PaymentReceipt;
use Illuminate\Http\Request;

class CustomerMenuController extends Controller
{
    public function showMenu($restaurant_slug)
    {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $categories = Category::where('restaurant_id', $restaurant->id)->with('menuItems')->get();
        $tables = Table::where('restaurant_id', $restaurant->id)->get();

        return view('menu.show', compact('restaurant', 'categories', 'tables'));
    }

    public function placeOrder(Request $request, $restaurant_slug)
    {
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

        $totalPrice = 0;
        $orderItemsData = [];

        foreach ($request->cart as $cartItem) {
            $menuItem = MenuItem::findOrFail($cartItem['id']);
            $price = $menuItem->price;
            $quantity = $cartItem['quantity'];
            $totalPrice += $price * $quantity;

            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'price' => $price,
                'kot_status' => 'pending',
            ];
        }

        // Create the order
        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'table_id' => $request->type === 'dine_in' ? $request->table_id : null,
            'status' => 'pending',
            'payment_status' => 'pending',
            'total_price' => $totalPrice,
            'type' => $request->type,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'notes' => $request->notes,
        ]);

        // Create the order items
        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }

        // If table was selected, mark it as occupied
        if ($request->type === 'dine_in' && $request->table_id) {
            Table::where('id', $request->table_id)->update(['status' => 'occupied']);
        }

        return response()->json([
            'success' => true,
            'redirect_url' => route('customer.checkout', [$restaurant->slug, $order->id]),
        ]);
    }

    public function checkout($restaurant_slug, $order_id)
    {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $order = Order::with('items.menuItem')->where('id', $order_id)->firstOrFail();

        return view('menu.checkout', compact('restaurant', 'order'));
    }

    public function processPayment(Request $request, $restaurant_slug, $order_id)
    {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $order = Order::findOrFail($order_id);

        $request->validate([
            'payment_method' => 'required|in:cash,baridimob',
            'transaction_id' => 'required_if:payment_method,baridimob|nullable|string|max:255',
            'receipt_screenshot' => 'required_if:payment_method,baridimob|nullable|image|max:4096',
        ]);

        $order->update([
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending', // will remain pending until admin reviews
        ]);

        if ($request->payment_method === 'baridimob') {
            $screenshotPath = null;
            if ($request->hasFile('receipt_screenshot')) {
                $screenshotPath = $request->file('receipt_screenshot')->store('receipts', 'public');
            }

            PaymentReceipt::create([
                'restaurant_id' => $restaurant->id,
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'transaction_id' => $request->transaction_id,
                'receipt_screenshot' => $screenshotPath,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('customer.order.success', [$restaurant->slug, $order->id]);
    }

    public function success($restaurant_slug, $order_id)
    {
        $restaurant = Restaurant::where('slug', $restaurant_slug)->firstOrFail();
        $order = Order::with('items.menuItem')->findOrFail($order_id);

        return view('menu.success', compact('restaurant', 'order'));
    }
}
