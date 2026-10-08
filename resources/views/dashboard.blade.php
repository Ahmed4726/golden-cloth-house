@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Dashboard</h1>
            <div class="text-secondary">Manage your clothing store, orders and social publishing.</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-dark" href="{{ route('orders.index') }}">View Orders</a>
            <a class="btn btn-dark" href="{{ route('posts.create') }}">Create Social Post</a>
        </div>
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3">
        @foreach ([
            ['Products', $stats['products']],
            ['Orders', $stats['orders']],
            ['Pending Orders', $stats['pendingOrders']],
            ['COD Revenue', number_format((float) $stats['revenue'], 0).' PKR'],
        ] as $stat)
            <div class="col">
                <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">{{ $stat[0] }}</div><div class="display-6 fw-bold">{{ $stat[1] }}</div></div></div>
            </div>
        @endforeach
    </div>

    <div class="row row-cols-1 row-cols-sm-2 row-cols-xl-4 g-3 mt-1">
        @foreach ([
            ['Connections', $stats['connections']],
            ['Drafts', $stats['drafts']],
            ['Scheduled', $stats['scheduled']],
            ['Published', $stats['published']],
        ] as $stat)
            <div class="col">
                <div class="card border-0 shadow-sm h-100"><div class="card-body"><div class="text-secondary">{{ $stat[0] }}</div><div class="display-6 fw-bold">{{ $stat[1] }}</div></div></div>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center"><h2 class="h5 mb-0">Recent COD Orders</h2><a href="{{ route('orders.index') }}">View all</a></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Created</th></tr></thead>
                <tbody>
                    @forelse ($recentOrders as $order)
                        <tr>
                            <td><a href="{{ route('orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td>
                            <td>{{ $order->customer_name }}<div class="text-secondary small">{{ $order->customer_phone }}</div></td>
                            <td>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td>
                            <td><span class="badge text-bg-secondary">{{ ucfirst($order->status) }}</span></td>
                            <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-secondary">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center"><h2 class="h5 mb-0">Connection health</h2><a href="{{ route('connections.index') }}">Manage</a></div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Platform</th><th>Account</th><th>Status</th><th>Active</th></tr></thead>
                <tbody>
                    @forelse ($connections as $connection)
                        <tr>
                            <td>{{ ucfirst($connection->provider->value) }}</td>
                            <td>{{ $connection->name }}</td>
                            <td><span class="badge {{ $connection->status === 'connected' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $connection->status }}</span></td>
                            <td>{{ $connection->active ? 'Yes' : 'No' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-secondary">No social accounts connected yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
