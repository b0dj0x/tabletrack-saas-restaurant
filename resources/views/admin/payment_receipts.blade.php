<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payments - TableTrack</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 text-slate-800 p-8 space-y-6">
    <h1 class="text-3xl font-black">BaridiMob Receipt Verifications</h1>
    <div class="bg-white p-6 rounded-xl border">
        <table class="w-full text-left text-xs">
            <thead>
                <tr class="border-b font-bold text-slate-400">
                    <th class="py-2.5">Receipt ID</th>
                    <th class="py-2.5">Amount</th>
                    <th class="py-2.5">Verify</th>
                </tr>
            </thead>
            <tbody>
                @foreach($receipts as $rec)
                    <tr>
                        <td class="py-3">#{{ $rec->id }}</td>
                        <td class="py-3 font-bold">${{ $rec->amount }}</td>
                        <td class="py-3">
                            <form action="{{ route('admin.payment_receipts.verify', $rec->id) }}" method="POST">
                                @csrf
                                <button type="submit" name="status" value="approved" class="px-3 py-1 bg-green-500 text-white rounded font-bold text-[10px]">Approve</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>