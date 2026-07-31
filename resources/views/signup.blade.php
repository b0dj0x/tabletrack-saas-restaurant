<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Registration - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 font-sans min-h-screen flex flex-col justify-between">
    <main class="py-12 max-w-2xl mx-auto px-4 w-full flex-1 flex items-center">
        <div class="bg-white rounded-3xl border p-8 shadow-xl w-full space-y-8">
            <h1 class="text-3xl font-extrabold tracking-tight text-center">Register Your Restaurant</h1>

            <form action="{{ route('signup.submit') }}" method="POST" class="space-y-4">
                @csrf
                <input type="text" name="restaurant_name" required placeholder="Restaurant Name" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                <input type="text" name="phone" required placeholder="Phone" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                <input type="text" name="address" required placeholder="Address" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                <input type="text" name="owner_name" required placeholder="Your Name" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                <input type="email" name="email" required placeholder="Email" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                <input type="password" name="password" required placeholder="Password" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                <input type="password" name="password_confirmation" required placeholder="Confirm Password" class="w-full px-4 py-2.5 rounded-xl border text-sm focus:outline-none focus:border-rose-500">
                
                <select name="package_id" required class="w-full px-4 py-3 rounded-xl border text-sm focus:outline-none">
                    @foreach ($packages as $pkg)
                        <option value="{{ $pkg->id }}">{{ $pkg->name }}</option>
                    @endforeach
                </select>

                <button type="submit" class="w-full py-3.5 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white font-bold">
                    Register
                </button>
            </form>
        </div>
    </main>
</body>
</html>
