<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Areas - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">Areas Layout</h1>
    <form action="{{ route('admin.areas.store') }}" method="POST" class="flex gap-4">
        @csrf
        <input type="text" name="name" required placeholder="Area Name" class="px-4 py-2 border rounded-xl">
        <button type="submit" class="px-6 py-2 bg-rose-600 text-white font-bold rounded-xl">Save</button>
    </form>
    <div class="grid grid-cols-3 gap-6">
        @foreach($areas as $area)
            <div class="p-4 bg-white rounded-xl border font-bold">{{ $area->name }}</div>
        @endforeach
    </div>
</body>
</html>