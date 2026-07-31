<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Menu Items - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">Menu Items</h1>
    <form action="{{ route('admin.menu_items.store') }}" method="POST" class="flex gap-4">
        @csrf
        <input type="text" name="name" required placeholder="Item Name" class="px-4 py-2 border rounded-xl">
        <select name="category_id" required class="px-4 py-2 border rounded-xl">
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
        </select>
        <input type="number" step="0.01" name="price" required placeholder="Price" class="px-4 py-2 border rounded-xl">
        <button type="submit" class="px-6 py-2 bg-rose-600 text-white font-bold rounded-xl">Save</button>
    </form>
    <div class="grid grid-cols-3 gap-6">
        @foreach($menuItems as $item)
            <div class="p-4 bg-white rounded-xl border">
                <span class="font-bold">{{ $item->name }}</span>
                <p class="text-xs text-slate-400">Price: ${{ $item->price }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>