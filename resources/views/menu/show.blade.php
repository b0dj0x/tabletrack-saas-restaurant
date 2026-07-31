<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - {{ $restaurant->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen pb-24">
    <header class="bg-white shadow-sm border-b py-4 sticky top-0 z-40">
        <div class="max-w-4xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-rose-500 text-white flex items-center justify-center font-bold text-xl shadow-md">
                    {{ substr($restaurant->name, 0, 1) }}
                </div>
                <div>
                    <h1 class="text-lg font-black text-slate-900">{{ $restaurant->name }}</h1>
                    <p class="text-[10px] text-slate-400"><i class="fa-solid fa-map-marker-alt text-rose-500"></i> {{ $restaurant->address }}</p>
                </div>
            </div>
            
            <button onclick="toggleCartDrawer()" class="relative p-3 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-100 transition-all">
                <i class="fa-solid fa-shopping-basket text-lg"></i>
                <span id="cart-badge" class="absolute -top-1.5 -right-1.5 w-5 h-5 rounded-full bg-rose-600 text-white text-[10px] font-black flex items-center justify-center shadow-md scale-0 transition-transform">0</span>
            </button>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8 space-y-8">
        <div class="relative">
            <input type="text" id="menu-search" oninput="searchMenu()" placeholder="Search menu items..." class="w-full pl-10 pr-4 py-3 rounded-2xl border bg-white text-sm focus:outline-none shadow-sm">
        </div>

        <div class="grid sm:grid-cols-2 gap-6" id="menu-grid">
            @foreach ($categories as $cat)
                @foreach ($cat->menuItems as $item)
                    <div class="menu-item-card bg-white rounded-3xl border p-5 flex flex-col justify-between shadow-sm hover:shadow-md transition-all gap-4 cat-{{ $cat->id }}" data-name="{{ strtolower($item->name) }}">
                        <div class="flex gap-4">
                            <div class="w-20 h-20 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 border">
                                <i class="fa-solid fa-pizza-slice text-2xl"></i>
                            </div>
                            
                            <div class="space-y-1">
                                <span class="text-[9px] font-bold uppercase tracking-widest text-rose-500">{{ $cat->name }}</span>
                                <h4 class="text-sm font-bold text-slate-800 leading-snug">{{ $item->name }}</h4>
                                <p class="text-xs text-slate-400 line-clamp-2">{{ $item->description }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t pt-3">
                            <span class="text-base font-extrabold text-slate-900">${{ number_format($item->price, 2) }}</span>
                            @if ($item->is_available)
                                <button onclick="addToCart({{ $item->id }}, '{{ addslashes($item->name) }}', {{ $item->price }})" class="px-4 py-1.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-600/10 transition-all flex items-center gap-1">Add to Cart</button>
                            @else
                                <span class="px-3 py-1 rounded-lg bg-slate-100 text-slate-400 text-[10px] font-bold">Sold Out</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endforeach
        </div>
    </main>

    <!-- Side Cart Drawer -->
    <div id="cart-drawer" class="fixed inset-0 z-50 overflow-hidden translate-x-full transition-transform duration-300">
        <div onclick="toggleCartDrawer()" class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

        <div class="absolute inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl flex flex-col justify-between">
            <div class="p-6 border-b flex items-center justify-between">
                <h3 class="text-lg font-bold">Your Basket</h3>
                <button onclick="toggleCartDrawer()" class="p-2 rounded-lg bg-slate-100 hover:bg-slate-200"><i class="fa-solid fa-times"></i></button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-4" id="cart-items-container">
                <p class="text-slate-400 text-center py-12 text-sm">Your basket is empty. Add delicious menu items to get started!</p>
            </div>

            <div class="p-6 border-t bg-slate-50 space-y-6">
                <div class="flex items-center justify-between text-base font-extrabold text-slate-800">
                    <span>Total Price</span>
                    <span id="cart-total">$0.00</span>
                </div>

                <div id="checkout-form-container" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <input type="text" id="customer_name" placeholder="Your Name" class="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none">
                        <input type="text" id="customer_phone" placeholder="Phone Number" class="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <select id="order_type" class="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none font-bold">
                            <option value="dine_in">Dine In</option>
                            <option value="takeaway">Take Away</option>
                            <option value="delivery">Delivery</option>
                        </select>
                        <select id="table_id" class="w-full px-3 py-2 rounded-xl border text-xs focus:outline-none font-bold">
                            <option value="">Select Table</option>
                            @foreach ($tables as $table)
                                <option value="{{ $table->id }}">{{ $table->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <button onclick="submitOrder()" class="w-full py-3.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-bold shadow-lg shadow-rose-600/10 hover:shadow-rose-600/20 transition-all">Proceed to Payment</button>
            </div>
        </div>
    </div>

    <script>
        let cart = [];
        function toggleCartDrawer() { document.getElementById('cart-drawer').classList.toggle('translate-x-full'); }
        function searchMenu() {
            const query = document.getElementById('menu-search').value.toLowerCase();
            document.querySelectorAll('.menu-item-card').forEach(card => {
                if (card.getAttribute('data-name').includes(query)) card.classList.remove('hidden');
                else card.classList.add('hidden');
            });
        }
        function addToCart(id, name, price) {
            const existing = cart.find(i => i.id === id);
            if (existing) existing.quantity++;
            else cart.push({ id, name, price, quantity: 1 });
            updateCartUI();
        }
        function changeQuantity(id, change) {
            const item = cart.find(i => i.id === id);
            if (item) {
                item.quantity += change;
                if (item.quantity <= 0) cart = cart.filter(i => i.id !== id);
            }
            updateCartUI();
        }
        function updateCartUI() {
            const container = document.getElementById('cart-items-container');
            const badge = document.getElementById('cart-badge');
            const totalSpan = document.getElementById('cart-total');
            const totalCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            const totalPrice = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

            if (totalCount > 0) { badge.innerText = totalCount; badge.classList.remove('scale-0'); }
            else { badge.classList.add('scale-0'); }

            totalSpan.innerText = `$${totalPrice.toFixed(2)}`;

            if (cart.length === 0) {
                container.innerHTML = `<p class="text-slate-400 text-center py-12 text-sm">Your basket is empty. Add delicious menu items to get started!</p>`;
                return;
            }

            let html = '<div class="space-y-4">';
            cart.forEach(item => {
                html += `
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border">
                        <div>
                            <h5 class="text-xs font-bold text-slate-800">${item.name}</h5>
                            <span class="text-[10px] text-slate-400 block">$${item.price.toFixed(2)} each</span>
                        </div>
                        <div class="flex items-center gap-2.5">
                            <button onclick="changeQuantity(${item.id}, -1)" class="w-7 h-7 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">-</button>
                            <span class="text-xs font-bold text-slate-800">${item.quantity}</span>
                            <button onclick="changeQuantity(${item.id}, 1)" class="w-7 h-7 rounded-lg bg-slate-200 text-slate-700 flex items-center justify-center font-bold text-xs">+</button>
                        </div>
                    </div>
                `;
            });
            html += '</div>';
            container.innerHTML = html;
        }

        async function submitOrder() {
            if (cart.length === 0) { alert('Your cart is empty.'); return; }
            const customerName = document.getElementById('customer_name').value;
            const customerPhone = document.getElementById('customer_phone').value;
            const type = document.getElementById('order_type').value;
            const tableId = document.getElementById('table_id').value;

            if (!customerName || !customerPhone) { alert('Please input your name and phone.'); return; }

            const response = await fetch("{{ route('customer.order.submit', $restaurant->slug) }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    customer_name: customerName, customer_phone: customerPhone,
                    type: type, table_id: tableId, cart: cart
                })
            });

            const result = await response.json();
            if (result.success) { window.location.href = result.redirect_url; }
            else { alert('Error placing order.'); }
        }
    </script>
</body>
</html>
