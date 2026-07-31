<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - {{ $restaurant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen pb-12">
    <header class="bg-white shadow-sm border-b py-4 mb-8">
        <div class="max-w-xl mx-auto px-4 flex items-center justify-between">
            <h1 class="text-lg font-black text-slate-900 flex items-center gap-2">Checkout</h1>
            <span class="text-xs text-slate-400 font-bold">Order #{{ $order->id }}</span>
        </div>
    </header>

    <main class="max-w-xl mx-auto px-4 space-y-6">
        <div class="bg-white rounded-3xl border p-6 shadow-sm space-y-4">
            <h3 class="text-sm font-black uppercase text-slate-400 tracking-wider">Order Summary</h3>
            <div class="divide-y">
                @foreach ($order->items as $item)
                    <div class="flex justify-between items-center py-3 text-xs">
                        <div>
                            <span class="font-bold text-slate-800">{{ $item->menuItem->name }}</span>
                            <span class="text-slate-400 block">Qty: {{ $item->quantity }}</span>
                        </div>
                        <span class="font-extrabold text-slate-800">${{ number_format($item->price * $item->quantity, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <div class="border-t pt-4 flex justify-between items-center text-sm font-black">
                <span>Total Amount Due</span>
                <span class="text-rose-600">${{ number_format($order->total_price, 2) }}</span>
            </div>
        </div>

        <form action="{{ route('customer.payment.process', [$restaurant->slug, $order->id]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <div class="bg-white rounded-3xl border p-6 shadow-sm space-y-6">
                <h3 class="text-sm font-black uppercase text-slate-400 tracking-wider">Select Payment Method</h3>
                <div class="space-y-3">
                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer bg-slate-50/50 hover:bg-slate-50">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="cash" checked onclick="togglePaymentMethod('cash')">
                            <div>
                                <span class="text-xs font-bold block">Cash on Counter</span>
                                <span class="text-[10px] text-slate-400 block">Pay manually at the counter upon service</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-money-bill-wave text-green-500 text-lg"></i>
                    </label>

                    <label class="flex items-center justify-between p-4 rounded-2xl border cursor-pointer bg-slate-50/50 hover:bg-slate-50">
                        <div class="flex items-center gap-3">
                            <input type="radio" name="payment_method" value="baridimob" onclick="togglePaymentMethod('baridimob')">
                            <div>
                                <span class="text-xs font-bold block">BaridiMob Transfer</span>
                                <span class="text-[10px] text-slate-400 block">Instant Algerian postal transfer</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-qrcode text-rose-500 text-lg"></i>
                    </label>
                </div>

                <div id="cash-instructions" class="p-4 rounded-2xl bg-slate-50 text-slate-500 text-xs leading-relaxed">
                    <p class="font-medium">Please confirm your order. You can pay cash directly to your waiter or at the cashier counter once your order is prepared and served.</p>
                </div>

                <div id="baridimob-instructions" class="hidden space-y-4">
                    <div class="p-5 rounded-2xl bg-rose-50/40 border text-xs space-y-3">
                        <p class="font-bold text-rose-600">Restaurant RIP Transfer Info:</p>
                        <span class="font-mono text-xs font-bold tracking-widest text-slate-700" id="rip-code">{{ $restaurant->baridimob_rip ?? '00799999000123456789' }}</span>
                        <p class="text-slate-500 mt-1">Please transfer the total of <span class="font-bold">${{ number_format($order->total_price, 2) }}</span> via BaridiMob, take a screenshot of the receipt, and upload it below.</p>
                    </div>

                    <div class="space-y-4 pt-2">
                        <input type="text" name="transaction_id" id="transaction_id" placeholder="Transaction Reference" class="w-full px-4 py-2.5 rounded-xl border text-sm">
                        <input type="file" name="receipt_screenshot" id="receipt_screenshot" accept="image/*" class="w-full px-4 py-2.5 rounded-xl border text-sm text-xs">
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-4 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-xl">Confirm & Place Order</button>
        </form>
    </main>

    <script>
        function togglePaymentMethod(method) {
            const cash = document.getElementById('cash-instructions');
            const baridimob = document.getElementById('baridimob-instructions');
            const txnInput = document.getElementById('transaction_id');
            const fileInput = document.getElementById('receipt_screenshot');

            if (method === 'cash') {
                cash.classList.remove('hidden');
                baridimob.classList.add('hidden');
                txnInput.removeAttribute('required');
                fileInput.removeAttribute('required');
            } else {
                cash.classList.add('hidden');
                baridimob.classList.remove('hidden');
                txnInput.setAttribute('required', 'required');
                fileInput.setAttribute('required', 'required');
            }
        }
    </script>
</body>
</html>
