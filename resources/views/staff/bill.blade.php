<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Bill #{{ $order->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-mono py-12 px-4 flex flex-col items-center">
    <div class="max-w-sm w-full bg-white p-6 border shadow-lg space-y-6 text-xs text-center">
        <div>
            <h2 class="text-base font-black uppercase">{{ $order->restaurant->name }}</h2>
            <p>{{ $order->restaurant->address }}</p>
        </div>
        <div class="border-t border-dashed my-4"></div>
        <div class="text-left space-y-1">
            <p>Order ID: #{{ $order->id }}</p>
            <p>Date: {{ $order->created_at->format('Y-m-d H:i') }}</p>
        </div>
        <div class="border-t border-dashed my-4"></div>
        <div class="space-y-2 text-left">
            @foreach($order->items as $item)
                <div class="flex justify-between items-center">
                    <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                    <span>${{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>
        <div class="border-t border-dashed my-4"></div>
        <div class="flex justify-between items-center font-black text-sm text-left">
            <span>Total Due:</span>
            <span>${{ number_format($order->total_price, 2) }}</span>
        </div>
    </div>
    <div class="mt-6 flex gap-4">
        <button onclick="window.print()" class="px-6 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-md">Print Bill</button>
        <a href="{{ route('staff.pos') }}" class="px-6 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs shadow-md">Back to POS</a>
    </div>
</body>
</html>
