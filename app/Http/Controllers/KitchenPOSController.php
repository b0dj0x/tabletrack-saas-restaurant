<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use App\Models\Table;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenPOSController extends Controller
{
    private function getRestaurant()
    {
        return auth()->user()->restaurant_id 
            ? Restaurant::find(auth()->user()->restaurant_id)
            : null;
    }

    public function pos()
    {
        $restaurant = $this->getRestaurant();
        $tables = Table::where('restaurant_id', $restaurant->id)->get();
        $menuItems = MenuItem::where('restaurant_id', $restaurant->id)->where('is_available', true)->with('category')->get();
        return view('staff.pos', compact('restaurant', 'tables', 'menuItems'));
    }

    public function placePosOrder(Request $request)
    {
        $restaurant = $this->getRestaurant();

        $request->validate([
            'table_id' => 'nullable|exists:tables,id',
            'type' => 'required|in:dine_in,takeaway',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $totalPrice = 0;
        $orderItemsData = [];

        foreach ($request->items as $item) {
            $menuItem = MenuItem::findOrFail($item['id']);
            $price = $menuItem->price;
            $qty = $item['quantity'];
            $totalPrice += $price * $qty;

            $orderItemsData[] = [
                'menu_item_id' => $menuItem->id,
                'quantity' => $qty,
                'price' => $price,
                'kot_status' => 'pending',
            ];
        }

        $order = Order::create([
            'restaurant_id' => $restaurant->id,
            'table_id' => $request->type === 'dine_in' ? $request->table_id : null,
            'waiter_id' => auth()->id(),
            'status' => 'preparing',
            'payment_status' => 'paid',
            'payment_method' => 'cash',
            'total_price' => $totalPrice,
            'type' => $request->type,
        ]);

        foreach ($orderItemsData as $itemData) {
            $order->items()->create($itemData);
        }

        if ($request->type === 'dine_in' && $request->table_id) {
            Table::where('id', $request->table_id)->update(['status' => 'occupied']);
        }

        return response()->json([
            'success' => true,
            'bill_url' => route('staff.pos.bill', $order->id),
        ]);
    }

    public function printBill($id)
    {
        $order = Order::with('items.menuItem', 'restaurant', 'table')->findOrFail($id);
        return view('staff.bill', compact('order'));
    }

    public function kitchen()
    {
        $restaurant = $this->getRestaurant();
        $orders = Order::where('restaurant_id', $restaurant->id)
            ->whereIn('status', ['pending', 'preparing'])
            ->with('items.menuItem', 'table')
            ->latest()
            ->get();
        return view('staff.kitchen', compact('orders'));
    }

    public function updateItemStatus(Request $request, $id)
    {
        $item = OrderItem::findOrFail($id);
        $item->update(['kot_status' => $request->status]);
        return back()->with('success', 'Item status updated.');
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);

        if (in_array($request->status, ['served', 'completed', 'cancelled']) && $order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'available']);
        }

        return back()->with('success', 'Order status updated.');
    }
}
