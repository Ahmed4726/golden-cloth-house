@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Orders</h1>
            <div class="muted">Manage Cash on Delivery orders from the storefront.</div>
        </div>
    </div>

    <form class="card row" method="GET" action="{{ route('orders.index') }}" style="margin-bottom:18px;">
        <input class="field" style="margin:0;flex:1;" name="q" value="{{ request('q') }}" placeholder="Order number, customer or phone">
        <select name="status" style="width:180px;padding:11px;border:1px solid #d7dae2;border-radius:10px;">
            <option value="">All statuses</option>
            @foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <button class="btn" type="submit">Filter</button>
    </form>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td><a href="{{ route('orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td>
                        <td>{{ $order->customer_name }}<div class="muted">{{ $order->customer_phone }}</div></td>
                        <td>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td>
                        <td><span class="badge">COD · {{ $order->payment_status }}</span></td>
                        <td><span class="badge {{ in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']) ? 'ok' : ($order->status === 'cancelled' ? 'bad' : '') }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6">No orders found.</td></tr>
                @endforelse
            </tbody>
        </table>

        {{ $orders->links() }}
    </div>
@endsection
