@extends('layouts.app')
@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Orders</h1><div class="text-secondary">Manage Cash on Delivery orders from the storefront.</div></div>
    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('orders.index') }}">
        <div class="card-body"><div class="row g-2"><div class="col-md"><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Order number, customer or phone"></div><div class="col-md-3"><select class="form-select" name="status"><option value="">All statuses</option>@foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div><div class="col-md-auto"><button class="btn btn-dark" type="submit">Filter</button></div></div></div>
    </form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead><tbody>
        @forelse ($orders as $order)
            <tr><td><a href="{{ route('orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td><td>{{ $order->customer_name }}<div class="text-secondary small">{{ $order->customer_phone }}</div></td><td>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td><td><span class="badge text-bg-secondary">COD · {{ $order->payment_status }}</span></td><td><span class="badge {{ in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']) ? 'text-bg-success' : ($order->status === 'cancelled' ? 'text-bg-danger' : 'text-bg-secondary') }}">{{ ucfirst($order->status) }}</span></td><td>{{ $order->created_at->format('d M Y, H:i') }}</td></tr>
        @empty
            <tr><td colspan="6" class="text-secondary">No orders found.</td></tr>
        @endforelse
    </tbody></table></div><div class="card-footer bg-white">{{ $orders->links() }}</div></div>
@endsection
