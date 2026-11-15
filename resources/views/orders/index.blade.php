@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Orders &amp; Sales</h1><div class="text-secondary">Manage storefront orders and in-store sales.</div></div>
        <a class="btn btn-outline-dark" href="{{ route('products.index') }}">Record an in-store sale</a>
    </div>
    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('orders.index') }}">
        <div class="card-body"><div class="row g-2 align-items-end"><div class="col-12 col-md"><label class="form-label" for="order-search">Search orders</label><input class="form-control" id="order-search" name="q" value="{{ request('q') }}" placeholder="Order number, customer or phone"></div><div class="col-12 col-sm-6 col-md-3"><label class="form-label" for="order-status">Status</label><select class="form-select" id="order-status" name="status"><option value="">All statuses</option>@foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div><div class="col-12 col-sm-6 col-md-auto d-flex gap-2"><button class="btn btn-dark flex-grow-1" type="submit">Filter</button><a class="btn btn-outline-secondary" href="{{ route('orders.index') }}">Reset</a></div></div></div>
    </form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Payment</th><th>Status</th><th>Date</th></tr></thead><tbody>
        @forelse ($orders as $order)
            <tr><td><a href="{{ route('orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td><td>{{ $order->customer_name ?: 'Walk-in customer' }}<div class="text-secondary small">{{ $order->customer_phone ?: 'No phone provided' }}</div></td><td>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td><td><span class="badge text-bg-secondary">{{ $order->payment_method === 'cash' ? 'Cash' : 'COD' }} · {{ $order->payment_status }}</span></td><td><span class="badge {{ in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']) ? 'text-bg-success' : ($order->status === 'cancelled' ? 'text-bg-danger' : 'text-bg-secondary') }}">{{ ucfirst($order->status) }}</span></td><td>{{ $order->created_at->format('d M Y, H:i') }}</td></tr>
        @empty
            <tr><td colspan="6" class="text-secondary">No orders found.</td></tr>
        @endforelse
    </tbody></table></div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($orders->total())
                    Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                @else
                    No matching orders
                @endif
            </span>
            {{ $orders->links() }}
        </div>
    </div>
@endsection
