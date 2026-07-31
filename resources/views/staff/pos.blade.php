<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS Terminal - {{ $restaurant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen flex flex-col justify-between">
    <header class="bg-white border-b py-4 px-6 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.dashboard') }}" class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold shadow-md hover:bg-slate-850">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h1 class="text-base font-black">POS Point of Sale</h1>
                <p class="text-[10px] text-slate-400">Operator: {{ auth()->user()->name }}</p>
            </div>
        </div>
        <span class="text-xs font-bold text-slate-500">{{ $restaurant->name }}</span>
    </header>

    <main class="flex-1 flex overflow-hidden w-full mx-auto">
        <div class="flex-1 p-6 space-y-6 overflow-y-auto">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach ($menuItems as $item)
                    <div class="bg-white rounded-3xl border p-4 flex flex-col justify-between shadow-sm cursor-pointer hover:shadow-md transition-all" onclick="addToPosCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})">
                        <h4 class="text-xs font-bold leading-tight">{{ $item->name }}</h4>
                        <span class="text-sm font-black text-rose-600 mt-2 block">${{ number_format($item->price, 2) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="w-96 bg-white border-l shadow-xl flex flex-col justify-between">
            <div class="p-6 border-b flex items-center justify-between">
                <h3 class="font-bold">Active Ticket</h3>
                <span id="pos-ticket-qty" class="text-xs bg-slate-100 px-2 py-0.5 rounded font-black">0 Items</span>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4" id="pos-cart-container">
                <p class="text-slate-400 text-center py-12 text-sm">Basket is empty.</p>
            </div>

            <div class="p-6 border-t bg-slate-50 space-y-4">
                <div class="flex items-center justify-between text-base font-black">
                    <span>Total Bill</span>
                    <span id="pos-total-span" class="text-rose-600">$0.00</span>
                </div>
                <button onclick="submitPosOrder()" class="w-full py-3.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-lg">Complete Sale & Print Bill</button>
            </div>
        </div>
    </main>

    <script>
        let posCart = [];
        function addToPosCart(id, name, price) {
            const existing = posCart.find(i => i.id === id);
            if (existing) existing.quantity++;
            else posCart.push({ id, name, price, quantity: 1 });
            updatePosUI();
        }
        function updatePosUI() {
            const container = document.getElementById('pos-cart-container');
            const totalSpan = document.getElementById('pos-total-span');
            const qtyBadge = document.getElementById('pos-ticket-qty');
            const totalCount = posCart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = posCart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            qtyBadge.innerText = `${totalCount} Items`;
            totalSpan.innerText = `$${totalPrice.toFixed(2)}`;

            if (posCart.length === 0) {
                container.innerHTML = '<p class="text-slate-400 text-center py-12 text-sm">Basket is empty.</p>';
                return;
            }

            let html = '<div class="space-y-3">';
            posCart.forEach(item => {
                html += `<div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border">
                    <div><h5 class="text-xs font-bold">${item.name}</h5></div>
                    <span class="text-xs font-bold">${item.quantity}</span>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        async function submitPosOrder() {
            if (posCart.length === 0) { alert('Please add items.'); return; }
            const response = await fetch("{{ route('staff.pos.order') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ type: 'takeaway', items: posCart })
            });
            const result = await response.json();
            if (result.success) { window.location.href = result.bill_url; }
            else { alert('Error.'); }
        }
    </script>
</body>
</html>
