<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Order {{ $order->order_number }}</title>
</head>
<body style="margin:0;background:#f6f3ee;color:#17130f;font-family:Arial,sans-serif;line-height:1.6;">
    <div style="max-width:620px;margin:30px auto;padding:28px;background:#fff;border:1px solid #e8e1d8;border-radius:12px;">
        <h1 style="margin-top:0;">{{ config('store.name') }}</h1>
        <p>Hi {{ $order->customer_name }},</p>
        <p>We received your Cash on Delivery order <strong>{{ $order->order_number }}</strong>.</p>

        @foreach ($order->items as $item)
            <div style="padding:12px 0;border-bottom:1px solid #eee;">
                <strong>{{ $item->product_name }}</strong><br>
                {{ $item->quantity }} × {{ number_format((float) $item->unit_price, 0) }} = {{ number_format((float) $item->line_total, 0) }} {{ $order->currency }}
            </div>
        @endforeach

        <p><strong>Total payable on delivery: {{ number_format((float) $order->total, 0) }} {{ $order->currency }}</strong></p>
        <p>Delivery to: {{ $order->shipping_address }}, {{ $order->shipping_area }}, {{ $order->shipping_city }}.</p>
        <p>We will contact you on {{ $order->customer_phone }} to confirm the order.</p>

        <p style="margin-bottom:0;">Thank you,<br>{{ config('store.name') }}</p>
    </div>
</body>
</html>
