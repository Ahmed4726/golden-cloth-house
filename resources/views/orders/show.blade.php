@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>{{ $order->order_number }}</h1>
            <div class="muted">Placed {{ $order->created_at->format('d M Y, H:i') }}</div>
        </div>
        <a class="btn secondary" href="{{ route('orders.index') }}">Back to Orders</a>
    </div>

    <div class="grid" style="grid-template-columns:repeat(3,1fr);margin-bottom:18px;">
        <div class="card"><div class="muted">Customer</div><strong>{{ $order->customer_name }}</strong><div>{{ $order->customer_phone }}</div><div>{{ $order->customer_email ?: 'No email' }}</div></div>
        <div class="card"><div class="muted">Delivery</div><strong>{{ $order->shipping_city }}</strong><div>{{ $order->shipping_area }}</div><div>{{ $order->shipping_address }}</div></div>
        <div class="card"><div class="muted">Payment</div><strong>Cash on Delivery</strong><div>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</div><div>{{ ucfirst($order->payment_status) }}</div></div>
    </div>

    <div class="card" style="margin-bottom:18px;">
        <h2>Items</h2>
        <table class="table">
            <thead><tr><th>Product</th><th>SKU</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->sku ?: '—' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ number_format((float) $item->unit_price, 0) }}</td>
                        <td>{{ number_format((float) $item->line_total, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="max-width:320px;margin:18px 0 0 auto;">
            <div class="row" style="justify-content:space-between;padding:6px 0;"><span>Subtotal</span><strong>{{ number_format((float) $order->subtotal, 0) }}</strong></div>
            <div class="row" style="justify-content:space-between;padding:6px 0;"><span>Delivery</span><strong>{{ number_format((float) $order->shipping_amount, 0) }}</strong></div>
            <div class="row" style="justify-content:space-between;padding:10px 0;border-top:1px solid #e7e9ef;"><strong>Total</strong><strong>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</strong></div>
        </div>
    </div>

    <form class="card form" method="POST" action="{{ route('orders.update', $order) }}">
        @csrf
        @method('PATCH')

        <h2>Order status</h2>

        <div class="field">
            <label>Status</label>
            <select name="status">
                @foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                    <option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Admin note</label>
            <textarea name="admin_note">{{ old('admin_note', $order->admin_note) }}</textarea>
        </div>

        <button class="btn" type="submit">Save Order</button>
    </form>
@endsection
