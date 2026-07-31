<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reservations - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">Table Reservations</h1>
    <form action="{{ route('admin.reservations.store') }}" method="POST" class="space-y-4 max-w-md">
        @csrf
        <input type="text" name="customer_name" required placeholder="Guest Name" class="w-full px-4 py-2 border rounded-xl">
        <input type="text" name="customer_phone" required placeholder="Guest Phone" class="w-full px-4 py-2 border rounded-xl">
        <select name="table_id" required class="w-full px-4 py-2 border rounded-xl">
            @foreach($tables as $table)
                <option value="{{ $table->id }}">{{ $table->name }}</option>
            @endforeach
        </select>
        <input type="datetime-local" name="reservation_time" required class="w-full px-4 py-2 border rounded-xl">
        <input type="number" name="party_size" required placeholder="Party Size" class="w-full px-4 py-2 border rounded-xl">
        <button type="submit" class="px-6 py-2 bg-rose-600 text-white font-bold rounded-xl">Reserve</button>
    </form>
</body>
</html>