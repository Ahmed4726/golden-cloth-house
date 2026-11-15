@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Customers</h1>
            <div class="text-secondary">Customer profiles are grouped by phone number, or email when no phone is available.</div>
        </div>
    </div>

    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('customers.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md">
                    <label class="form-label" for="customer-search">Search customers</label>
                    <input class="form-control" id="customer-search" name="q" value="{{ request('q') }}" placeholder="Name, phone or email">
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Search</button>
                    <a class="btn btn-outline-secondary" href="{{ route('customers.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr><th>Customer</th><th>Contact</th><th>Latest address</th><th>Orders</th><th>Order value</th><th>Last purchase</th></tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td>
                                <a class="fw-semibold text-decoration-none" href="{{ route('customers.show', $customer) }}">
                                    {{ $customer->customer_name ?: 'Unnamed customer' }}
                                </a>
                            </td>
                            <td>
                                <div>{{ $customer->customer_phone ?: 'No phone' }}</div>
                                <div class="text-secondary small">{{ $customer->customer_email ?: 'No email' }}</div>
                            </td>
                            <td>
                                @if ($customer->latestOrder)
                                    <div>{{ $customer->latestOrder->shipping_city ?: 'No city' }}{{ $customer->latestOrder->shipping_area ? ', '.$customer->latestOrder->shipping_area : '' }}</div>
                                    <div class="text-secondary small">{{ $customer->latestOrder->shipping_address ?: 'No address' }}</div>
                                @else
                                    <span class="text-secondary">No address</span>
                                @endif
                            </td>
                            <td>{{ $customer->orders_count }}</td>
                            <td>{{ number_format((float) $customer->orders_sum_total, 0) }} PKR</td>
                            <td>{{ $customer->latestOrder?->created_at?->format('d M Y') ?: '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-secondary">No customers found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($customers->total())
                    Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers
                @else
                    No matching customers
                @endif
            </span>
            {{ $customers->links() }}
        </div>
    </div>
@endsection
