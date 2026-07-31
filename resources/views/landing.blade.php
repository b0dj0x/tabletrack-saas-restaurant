<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TableTrack - The Ultimate SaaS Restaurant Management Solution</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-slate-50 text-slate-800 font-sans">
    <header class="bg-white border-b py-5 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-rose-500 to-rose-600 flex items-center justify-center text-white shadow-lg shadow-rose-500/20">
                    <i class="fa-solid fa-utensils text-lg"></i>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-rose-500 to-rose-600 bg-clip-text text-transparent">TableTrack</span>
            </div>
            <nav class="hidden md:flex gap-8">
                <a href="#features" class="hover:text-rose-500 font-medium">Features</a>
                <a href="{{ route('restaurants.list') }}" class="hover:text-rose-500 font-medium">Explore Menus</a>
                <a href="#pricing" class="hover:text-rose-500 font-medium">Pricing</a>
            </nav>
            <div class="flex gap-4">
                @auth
                    <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-rose-50 text-rose-600 font-semibold text-sm hover:bg-rose-100">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl text-slate-700 font-semibold text-sm">Sign In</a>
                    <a href="{{ route('signup.form') }}" class="px-5 py-2.5 rounded-xl bg-rose-600 text-white font-semibold text-sm hover:bg-rose-700 shadow-md">Get Started</a>
                @endauth
            </div>
        </div>
    </header>

    <section class="max-w-7xl mx-auto px-4 py-20 text-center space-y-6">
        <h1 class="text-5xl font-extrabold tracking-tight text-slate-900 leading-tight">Restaurant POS software made simple!</h1>
        <p class="text-lg text-slate-500 max-w-2xl mx-auto">Easily manage orders, menus, and tables in one place. Save time, reduce errors, and grow your business faster. Enabling contactless Algerian <span class="font-bold text-rose-500">BaridiMob QR payments</span> or cash instantly.</p>
        <div class="flex justify-center gap-4">
            <a href="{{ route('signup.form') }}" class="px-8 py-3 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold shadow-md">Start 30 Days Trial</a>
            <a href="{{ route('restaurants.list') }}" class="px-8 py-3 rounded-xl bg-white text-slate-700 hover:bg-slate-50 border shadow-sm font-bold">Explore Demo Menus</a>
        </div>
    </section>

    <!-- Pricing Package Cards Grid -->
    <section id="pricing" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 space-y-12">
            <h2 class="text-3xl font-extrabold tracking-tight text-center">Simple, Transparent Pricing</h2>
            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                @foreach ($packages as $pkg)
                    <div class="bg-white rounded-3xl p-8 border flex flex-col justify-between shadow-lg relative transform hover:scale-[1.02] transition-transform">
                        <div>
                            <span class="text-slate-400 text-xs font-bold uppercase tracking-wider block mb-2">{{ $pkg->billing_period }} Plan</span>
                            <h3 class="text-2xl font-bold text-slate-800 mb-4">{{ $pkg->name }}</h3>
                            <div class="flex items-baseline gap-1.5 mb-6">
                                <span class="text-4xl font-extrabold text-slate-900">${{ number_format($pkg->price, 2) }}</span>
                                <span class="text-slate-400 text-sm">/ {{ $pkg->billing_period }}</span>
                            </div>
                        </div>
                        <a href="{{ route('signup.form', ['package' => $pkg->id]) }}" class="w-full inline-flex items-center justify-center py-3.5 px-6 rounded-2xl text-sm font-bold text-center bg-slate-100 hover:bg-slate-200 transition-all text-slate-800">
                            Get Started
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</body>
</html>
