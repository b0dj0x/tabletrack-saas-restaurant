<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tables - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">Tables Seating</h1>
    <form action="{{ route('admin.tables.store') }}" method="POST" class="flex gap-4">
        @csrf
        <input type="text" name="name" required placeholder="Table Name" class="px-4 py-2 border rounded-xl">
        <select name="area_id" required class="px-4 py-2 border rounded-xl">
            @foreach($areas as $area)
                <option value="{{ $area->id }}">{{ $area->name }}</option>
            @endforeach
        </select>
        <input type="number" name="capacity" required placeholder="Capacity" class="px-4 py-2 border rounded-xl">
        <button type="submit" class="px-6 py-2 bg-rose-600 text-white font-bold rounded-xl">Save</button>
    </form>
    <div class="grid grid-cols-3 gap-6">
        @foreach($tables as $table)
            <div class="p-4 bg-white rounded-xl border">
                <span class="font-bold">{{ $table->name }}</span>
                <p class="text-xs text-slate-400">Capacity: {{ $table->capacity }} | Area: {{ $table->area->name ?? 'None' }}</p>
            </div>
        @endforeach
    </div>
</body>
</html>