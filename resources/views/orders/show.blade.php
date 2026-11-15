@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4"><div><h1 class="h2 fw-bold mb-1">{{ $order->order_number }}</h1><div class="text-secondary">Placed {{ $order->created_at->format('d M Y, H:i') }}</div></div><a class="btn btn-outline-dark" href="{{ route('orders.index') }}">Back to Orders</a></div>

    <div class="row g-3 mb-4">
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Customer</div><strong>{{ $order->customer_name ?: 'Walk-in customer' }}</strong><div>{{ $order->customer_phone ?: 'No phone provided' }}</div><div>{{ $order->customer_email ?: 'No email provided' }}</div>@if ($order->customer)<a class="btn btn-sm btn-outline-dark mt-2" href="{{ route('customers.show', $order->customer) }}">View customer history</a>@endif</div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Address</div><strong>{{ $order->shipping_city ?: 'No city provided' }}</strong><div>{{ $order->shipping_area }}</div><div>{{ $order->shipping_address ?: 'No address provided' }}</div></div></div></div>
        <div class="col-md-4"><div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">Payment</div><strong>{{ $order->payment_method === 'cash' ? 'Cash (in-store)' : 'Cash on Delivery' }}</strong><div>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</div><div>{{ ucfirst($order->payment_status) }}</div></div></div></div>
    </div>

    <div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white"><h2 class="h5 mb-0">Items</h2></div><div class="table-responsive"><table class="table table-hover mb-0"><thead><tr><th>Product</th><th>SKU</th><th>Qty</th><th>Unit</th><th>Total</th></tr></thead><tbody>@foreach ($order->items as $item)<tr><td>{{ $item->product_name }}</td><td>{{ $item->sku ?: '—' }}</td><td>{{ $item->quantity }}</td><td>{{ number_format((float) $item->unit_price, 0) }}</td><td>{{ number_format((float) $item->line_total, 0) }}</td></tr>@endforeach</tbody></table></div><div class="card-body"><div class="ms-auto col-md-4"><div class="d-flex justify-content-between py-2"><span>Subtotal</span><strong>{{ number_format((float) $order->subtotal, 0) }}</strong></div>@if ($order->coupon_code)<div class="d-flex justify-content-between py-2 text-success"><span>Coupon ({{ $order->coupon_code }})</span><strong>−{{ number_format((float) $order->discount_amount, 0) }}</strong></div>@endif<div class="d-flex justify-content-between py-2"><span>Delivery</span><strong>{{ number_format((float) $order->shipping_amount, 0) }}</strong></div><div class="d-flex justify-content-between border-top pt-2"><strong>Total</strong><strong>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</strong></div></div></div></div>

    @if ($order->customer_note)
        <div class="card border-0 shadow-sm mb-4"><div class="card-body"><h2 class="h6 fw-bold">Customer notes</h2><p class="mb-0">{{ $order->customer_note }}</p></div></div>
    @endif

    <form class="card border-0 shadow-sm" method="POST" action="{{ route('orders.update', $order) }}">
        @csrf @method('PATCH')
        <div class="card-body p-4"><h2 class="h5 mb-4">Order status</h2><div class="mb-3"><label class="form-label fw-semibold">Status</label><select class="form-select" name="status">@foreach (['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'] as $status)<option value="{{ $status }}" @selected($order->status === $status)>{{ ucfirst($status) }}</option>@endforeach</select></div><div class="mb-3"><label class="form-label fw-semibold">Admin note</label><textarea class="form-control" name="admin_note" rows="5">{{ old('admin_note', $order->admin_note) }}</textarea></div><button class="btn btn-dark" type="submit">Save Order</button></div>
    </form>
@endsection
