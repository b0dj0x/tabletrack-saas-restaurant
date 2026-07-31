<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kitchen KOT monitor - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen flex flex-col justify-between">
    <header class="bg-white border-b py-4 px-6 flex justify-between items-center shadow-sm">
        <h1 class="text-base font-black">Kitchen Order Tickets (KOT)</h1>
    </header>

    <main class="flex-1 p-6 overflow-y-auto max-w-7xl mx-auto w-full">
        <div class="grid md:grid-cols-4 gap-6">
            @forelse($orders as $order)
                <div class="bg-white rounded-3xl border shadow-sm overflow-hidden flex flex-col justify-between">
                    <div class="bg-slate-900 text-white p-4">
                        <h3 class="text-sm font-black">#{{ $order->id }} ({{ $order->table->name ?? 'Takeaway' }})</h3>
                    </div>
                    <div class="p-4 flex-1 space-y-3">
                        @foreach($order->items as $item)
                            <div class="flex justify-between items-center text-xs">
                                <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                                <span class="uppercase text-[9px] font-bold">{{ $item->kot_status }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="bg-slate-50 p-4 border-t flex gap-2">
                        <form action="{{ route('staff.kitchen.order.status', $order->id) }}" method="POST" class="w-full">
                            @csrf
                            <input type="hidden" name="status" value="served">
                            <button type="submit" class="w-full py-2 bg-green-500 hover:bg-green-600 text-white font-bold text-[10px] rounded-lg shadow-sm">Serve</button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 text-center col-span-full py-12">No active orders cooking.</p>
            @endforelse
        </div>
    </main>
</body>
</html>
