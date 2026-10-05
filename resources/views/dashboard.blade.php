@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Dashboard</h1>
            <div class="muted">Manage your clothing store, orders and social publishing.</div>
        </div>

        <div class="row">
            <a class="btn secondary" href="{{ route('orders.index') }}">View Orders</a>
            <a class="btn" href="{{ route('posts.create') }}">Create Social Post</a>
        </div>
    </div>

    <div class="grid">
        @foreach ([
            ['Products', $stats['products']],
            ['Orders', $stats['orders']],
            ['Pending Orders', $stats['pendingOrders']],
            ['COD Revenue', number_format((float) $stats['revenue'], 0).' PKR'],
        ] as $stat)
            <div class="card">
                <div class="muted">{{ $stat[0] }}</div>
                <div class="stat">{{ $stat[1] }}</div>
            </div>
        @endforeach
    </div>

    <div class="grid" style="margin-top:16px;">
        @foreach ([
            ['Connections', $stats['connections']],
            ['Drafts', $stats['drafts']],
            ['Scheduled', $stats['scheduled']],
            ['Published', $stats['published']],
        ] as $stat)
            <div class="card">
                <div class="muted">{{ $stat[0] }}</div>
                <div class="stat">{{ $stat[1] }}</div>
            </div>
        @endforeach
    </div>

    <div class="card" style="margin-top:20px;">
        <div class="top">
            <h2 style="margin:0;">Recent COD Orders</h2>
            <a href="{{ route('orders.index') }}">View all</a>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Created</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td><a href="{{ route('orders.show', $order) }}"><strong>{{ $order->order_number }}</strong></a></td>
                        <td>{{ $order->customer_name }}<div class="muted">{{ $order->customer_phone }}</div></td>
                        <td>{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td>
                        <td><span class="badge">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card" style="margin-top:20px;">
        <div class="top">
            <h2 style="margin:0;">Connection health</h2>
            <a href="{{ route('connections.index') }}">Manage</a>
        </div>

        <table class="table">
            <thead>
                <tr><th>Platform</th><th>Account</th><th>Status</th><th>Active</th></tr>
            </thead>
            <tbody>
                @forelse ($connections as $connection)
                    <tr>
                        <td>{{ ucfirst($connection->provider->value) }}</td>
                        <td>{{ $connection->name }}</td>
                        <td><span class="badge {{ $connection->status === 'connected' ? 'ok' : '' }}">{{ $connection->status }}</span></td>
                        <td>{{ $connection->active ? 'Yes' : 'No' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="muted">No social accounts connected yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
