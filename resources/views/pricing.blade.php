<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SaaS Subscription Packages - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col justify-between">
    <header class="bg-white border-b py-4">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <a href="/" class="flex items-center gap-2.5 font-extrabold text-lg text-slate-900">TableTrack</a>
            <a href="/" class="text-xs font-semibold text-rose-500 hover:text-rose-600 flex items-center gap-1">Back to Home</a>
        </div>
    </header>

    <main class="py-16 max-w-7xl mx-auto px-4 flex-1">
        <div class="text-center mb-16 space-y-2">
            <h1 class="text-4xl font-extrabold tracking-tight">Flexible SaaS Subscription Plans</h1>
        </div>

        <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            @foreach ($packages as $pkg)
                <div class="bg-white rounded-3xl p-8 border flex flex-col justify-between shadow-lg relative transform hover:scale-[1.02] transition-transform">
                    <div>
                        <span class="text-slate-400 text-xs font-bold uppercase block mb-1">{{ $pkg->billing_period }} Plan</span>
                        <h3 class="text-2xl font-bold text-slate-800 mb-4">{{ $pkg->name }}</h3>
                        <div class="flex items-baseline gap-1 mb-6">
                            <span class="text-3xl font-black">${{ number_format($pkg->price, 2) }}</span>
                            <span class="text-slate-400 text-xs">/ {{ $pkg->billing_period }}</span>
                        </div>
                    </div>
                    <a href="{{ route('signup.form', ['package' => $pkg->id]) }}" class="w-full inline-flex items-center justify-center py-3 px-4 rounded-xl text-xs font-bold text-center bg-slate-100 hover:bg-slate-200 transition-all text-slate-800">
                        Select Plan
                    </a>
                </div>
            @endforeach
        </div>
    </main>
</body>
</html>
