<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8">
    <h1 class="text-3xl font-black">Dashboard - {{ $restaurant->name }}</h1>
    <div class="grid grid-cols-3 gap-6 mt-8">
        <a href="{{ route('admin.areas') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Areas (Floors)</a>
        <a href="{{ route('admin.tables') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Tables</a>
        <a href="{{ route('admin.categories') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Categories</a>
        <a href="{{ route('admin.menu_items') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Menu Items</a>
        <a href="{{ route('admin.staff') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Staff</a>
        <a href="{{ route('admin.reservations') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Reservations</a>
        <a href="{{ route('admin.payment_receipts') }}" class="p-6 bg-white rounded-xl border font-bold">Manage Payments</a>
        <a href="{{ route('admin.billing') }}" class="p-6 bg-white rounded-xl border font-bold">SaaS Billing</a>
    </div>
    <div class="mt-8 flex gap-4">
        <a href="{{ route('staff.pos') }}" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl text-xs">Open POS</a>
        <a href="{{ route('staff.kitchen') }}" class="px-6 py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs">Open Kitchen</a>
    </div>
</body>
</html>