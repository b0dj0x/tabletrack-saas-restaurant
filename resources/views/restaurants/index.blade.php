<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Restaurants Directory - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen">
    <header class="bg-white border-b py-4">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <span class="text-lg font-bold text-slate-900">TableTrack</span>
            <a href="/" class="text-xs font-semibold text-rose-500">Home</a>
        </div>
    </header>

    <main class="py-12 max-w-7xl mx-auto px-4">
        <h1 class="text-4xl font-extrabold tracking-tight text-center">Active Restaurants</h1>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mt-12">
            @foreach ($restaurants as $res)
                <div class="bg-white rounded-3xl border p-6 space-y-4">
                    <h3 class="text-xl font-bold text-slate-800">{{ $res->name }}</h3>
                    <p class="text-xs text-slate-400">{{ $res->address }}</p>
                    <a href="{{ route('customer.menu', $res->slug) }}" class="inline-block px-4 py-2 rounded-xl bg-rose-50 text-rose-600 text-xs font-bold">Browse Menu</a>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
