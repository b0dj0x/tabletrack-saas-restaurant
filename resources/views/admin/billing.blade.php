<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billing - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">Billing & Subscriptions</h1>
    <div class="p-6 bg-white rounded-xl border">
        <span class="block font-bold">Current Status: {{ $restaurant->subscription_status }}</span>
    </div>
</body>
</html>