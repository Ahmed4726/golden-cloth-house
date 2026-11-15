@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">{{ $customer->customer_name ?: 'Unnamed customer' }}</h1>
            <div class="text-secondary">Complete purchase history and saved contact details.</div>
        </div>
        <a class="btn btn-outline-dark" href="{{ route('customers.index') }}">Back to Customers</a>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary mb-2">Contact</div>
                    <div><i class="fa-solid fa-phone me-2 text-secondary"></i>{{ $customer->customer_phone ?: 'No phone provided' }}</div>
                    <div class="mt-1"><i class="fa-solid fa-envelope me-2 text-secondary"></i>{{ $customer->customer_email ?: 'No email provided' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary mb-2">Latest address</div>
                    @if ($customer->latestOrder)
                        <strong>{{ $customer->latestOrder->shipping_city ?: 'No city provided' }}</strong>
                        <div>{{ $customer->latestOrder->shipping_area }}</div>
                        <div>{{ $customer->latestOrder->shipping_address ?: 'No address provided' }}</div>
                    @else
                        <div>No address provided</div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-secondary mb-2">Purchase summary</div>
                    <div><strong>{{ $customer->orders_count }}</strong> order(s)</div>
                    <div class="mt-1">Order value: <strong>{{ number_format((float) $customer->orders_sum_total, 0) }} PKR</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3">
            <h2 class="h5 fw-bold mb-0">Purchase history</h2>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Order</th><th>Products</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td><a class="fw-semibold" href="{{ route('orders.show', $order) }}">{{ $order->order_number }}</a></td>
                            <td>
                                @foreach ($order->items as $item)
                                    <div>{{ $item->product_name }} <span class="text-secondary">× {{ $item->quantity }}</span></div>
                                @endforeach
                            </td>
                            <td>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td>
                            <td><span class="badge {{ in_array($order->status, ['confirmed', 'processing', 'shipped', 'delivered']) ? 'text-bg-success' : ($order->status === 'cancelled' ? 'text-bg-danger' : 'text-bg-secondary') }}">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ $order->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-secondary">No purchase history found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($orders->total())
                    Showing {{ $orders->firstItem() }}–{{ $orders->lastItem() }} of {{ $orders->total() }} orders
                @else
                    No orders found
                @endif
            </span>
            {{ $orders->links() }}
        </div>
    </div>
@endsection
