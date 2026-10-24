@php($s = \App\Models\Setting::allValues())
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Hind+Siliguri:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', 'Hind Siliguri', sans-serif; color: #222; margin: 0; padding: 40px; font-size: 14px; }
        .invoice { max-width: 780px; margin: 0 auto; }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #f37021; padding-bottom: 18px; }
        .brand { font-size: 24px; font-weight: 700; color: #f37021; }
        .muted { color: #777; }
        h2 { margin: 0; font-size: 28px; letter-spacing: 2px; color: #444; }
        .info { display: flex; justify-content: space-between; margin: 26px 0; gap: 30px; }
        .info h4 { margin: 0 0 6px; text-transform: uppercase; font-size: 12px; color: #999; letter-spacing: 1px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #fdf0e7; text-align: left; padding: 10px; font-size: 12px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #eee; }
        .r { text-align: right; }
        .totals { width: 300px; margin-left: auto; margin-top: 16px; }
        .totals div { display: flex; justify-content: space-between; padding: 6px 0; }
        .totals .grand { border-top: 2px solid #222; font-weight: 700; font-size: 17px; margin-top: 6px; padding-top: 10px; }
        .foot { margin-top: 50px; text-align: center; color: #888; font-size: 12px; }
        .print-btn { position: fixed; top: 16px; right: 16px; background: #f37021; color: #fff; border: 0; padding: 10px 18px; border-radius: 6px; cursor: pointer; font-size: 14px; }
        @media print { .print-btn { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
<button class="print-btn" onclick="window.print()">Print</button>
<div class="invoice">
    <div class="head">
        <div>
            <div class="brand">{{ $s['site_name'] }}</div>
            <div class="muted">{{ $s['address'] }}<br>{{ $s['phone'] }} · {{ $s['email'] }}</div>
        </div>
        <div style="text-align:right">
            <h2>INVOICE</h2>
            <div class="muted">#{{ $order->order_number }}<br>{{ $order->created_at->format('d M Y') }}</div>
        </div>
    </div>

    <div class="info">
        <div>
            <h4>Bill to</h4>
            <strong>{{ $order->customer_name }}</strong><br>
            {{ $order->phone }}<br>
            @if($order->email){{ $order->email }}<br>@endif
            {{ $order->address }}@if($order->city), {{ $order->city }}@endif
        </div>
        <div style="text-align:right">
            <h4>Payment</h4>
            {{ $order->payment_method_label }}<br>
            @if($order->transaction_id)TrxID: {{ $order->transaction_id }}<br>@endif
            Status: {{ ucfirst($order->payment_status) }}
        </div>
    </div>

    <table>
        <thead><tr><th>#</th><th>Product</th><th class="r">Price</th><th class="r">Qty</th><th class="r">Total</th></tr></thead>
        <tbody>
        @foreach($order->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->product_name }}</td>
                <td class="r">{{ money($item->price) }}</td>
                <td class="r">{{ $item->quantity }}</td>
                <td class="r">{{ money($item->total) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span>{{ money($order->subtotal) }}</span></div>
        <div><span>Delivery charge</span><span>{{ money($order->shipping_cost) }}</span></div>
        @if($order->discount > 0)<div><span>Discount</span><span>-{{ money($order->discount) }}</span></div>@endif
        <div class="grand"><span>Total</span><span>{{ money($order->total) }}</span></div>
    </div>

    <div class="foot">Thank you for shopping with {{ $s['site_name'] }}!</div>
</div>
</body>
</html>
