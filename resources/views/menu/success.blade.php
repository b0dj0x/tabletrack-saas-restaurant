<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Placed - {{ $restaurant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex items-center justify-center py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-3xl border p-8 shadow-xl text-center space-y-8">
        <div class="w-20 h-20 rounded-full bg-green-500/10 text-green-500 flex items-center justify-center text-4xl mx-auto shadow-md">
            <i class="fa-solid fa-circle-check animate-bounce"></i>
        </div>

        <div class="space-y-2">
            <h1 class="text-2xl font-black tracking-tight">Order Placed Successfully!</h1>
            <p class="text-xs text-slate-400">Your order #{{ $order->id }} has been registered and is now being processed.</p>
        </div>

        <div class="p-4 rounded-2xl bg-slate-50 text-xs text-slate-500 border">
            @if ($order->payment_method === 'baridimob')
                <p class="font-bold text-rose-500">BaridiMob Verification Pending</p>
                <p class="mt-1">We are preparing your meal. Our cashier is currently verifying your manual BaridiMob transfer receipt screenshot.</p>
            @else
                <p class="font-bold text-green-500">Cash Counter Selected</p>
                <p class="mt-1">Your order has been sent to our kitchen chefs! Please pay cash directly at the counter upon service.</p>
            @endif
        </div>

        <a href="{{ route('customer.menu', $restaurant->slug) }}" class="w-full inline-flex items-center justify-center py-3.5 px-6 rounded-2xl text-xs font-bold bg-slate-100 text-slate-800 hover:bg-slate-200 transition-all">Return to Menu</a>
    </div>
</body>
</html>
