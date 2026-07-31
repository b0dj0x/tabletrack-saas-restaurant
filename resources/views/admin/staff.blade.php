<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">Staff Accounts</h1>
    <form action="{{ route('admin.staff.store') }}" method="POST" class="space-y-4 max-w-md">
        @csrf
        <input type="text" name="name" required placeholder="Name" class="w-full px-4 py-2 border rounded-xl">
        <input type="email" name="email" required placeholder="Email" class="w-full px-4 py-2 border rounded-xl">
        <input type="password" name="password" required placeholder="Password" class="w-full px-4 py-2 border rounded-xl">
        <select name="role" required class="w-full px-4 py-2 border rounded-xl">
            <option value="waiter">Waiter</option>
            <option value="cook">Cook</option>
        </select>
        <button type="submit" class="px-6 py-2 bg-rose-600 text-white font-bold rounded-xl">Save</button>
    </form>
    <div class="grid grid-cols-3 gap-6 mt-6">
        @foreach($staff as $member)
            <div class="p-4 bg-white rounded-xl border">
                <span class="font-bold block">{{ $member->name }}</span>
                <span class="text-xs text-slate-400 block uppercase font-bold">{{ $member->role }}</span>
            </div>
        @endforeach
    </div>
</body>
</html>