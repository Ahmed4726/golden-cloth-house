@extends('layouts.app')

@section('content')
    @php
        $primaryStats = [
            [
                'label' => 'Active products',
                'value' => number_format($stats['products']),
                'icon' => 'fa-boxes-stacked',
                'iconClass' => 'bg-primary-subtle text-primary',
                'hint' => 'Available in your shop',
            ],
            [
                'label' => 'Total orders',
                'value' => number_format($stats['orders']),
                'icon' => 'fa-bag-shopping',
                'iconClass' => 'bg-info-subtle text-info-emphasis',
                'hint' => 'Orders placed',
            ],
            [
                'label' => 'Pending orders',
                'value' => number_format($stats['pendingOrders']),
                'icon' => 'fa-clock',
                'iconClass' => 'bg-warning-subtle text-warning-emphasis',
                'hint' => 'Need your attention',
            ],
            [
                'label' => 'COD revenue',
                'value' => number_format((float) $stats['revenue'], 0) . ' PKR',
                'icon' => 'fa-wallet',
                'iconClass' => 'bg-success-subtle text-success-emphasis',
                'hint' => 'Excluding cancelled orders',
            ],
        ];

        $socialStats = [
            ['label' => 'Connections', 'value' => $stats['connections'], 'icon' => 'fa-share-nodes'],
            ['label' => 'Drafts', 'value' => $stats['drafts'], 'icon' => 'fa-file-lines'],
            ['label' => 'Scheduled', 'value' => $stats['scheduled'], 'icon' => 'fa-calendar-days'],
            ['label' => 'Published', 'value' => $stats['published'], 'icon' => 'fa-paper-plane'],
        ];
    @endphp

    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <div class="small text-uppercase fw-semibold text-secondary mb-1">Store overview</div>
            <h1 class="h2 fw-bold mb-1">Dashboard</h1>
            <p class="text-secondary mb-0">A quick look at your store, orders and social activity.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-outline-dark" href="{{ route('orders.index') }}">
                <i class="fa-solid fa-list-check me-2" aria-hidden="true"></i>View orders
            </a>
            <a class="btn btn-dark" href="{{ route('posts.create') }}">
                <i class="fa-solid fa-circle-plus me-2" aria-hidden="true"></i>Create social post
            </a>
        </div>
    </div>

    <section class="mb-4" aria-label="Store performance">
        <div class="row row-cols-1 row-cols-sm-2 row-cols-xxl-4 g-3">
            @foreach ($primaryStats as $stat)
                <div class="col">
                    <article class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="small text-secondary mb-2">{{ $stat['label'] }}</div>
                                    <div class="h3 fw-bold text-dark mb-2">{{ $stat['value'] }}</div>
                                    <div class="small text-secondary">{{ $stat['hint'] }}</div>
                                </div>
                                <span class="d-inline-flex align-items-center justify-content-center rounded-3 p-3 {{ $stat['iconClass'] }}">
                                    <i class="fa-solid {{ $stat['icon'] }} fs-5" aria-hidden="true"></i>
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <section class="mb-4" aria-labelledby="socialOverviewHeading">
        <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h2 class="h5 fw-bold mb-1" id="socialOverviewHeading">Social publishing</h2>
                <p class="small text-secondary mb-0">Your connected channels and post pipeline.</p>
            </div>
            <a class="small fw-semibold link-dark text-nowrap" href="{{ route('posts.index') }}">Manage posts</a>
        </div>

        <div class="row row-cols-2 row-cols-md-4 g-3">
            @foreach ($socialStats as $stat)
                <div class="col">
                    <article class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body d-flex align-items-center gap-3 p-3 p-lg-4">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-3 bg-light text-secondary p-3">
                                <i class="fa-solid {{ $stat['icon'] }}" aria-hidden="true"></i>
                            </span>
                            <div>
                                <div class="small text-secondary">{{ $stat['label'] }}</div>
                                <div class="h4 fw-bold mb-0">{{ number_format($stat['value']) }}</div>
                            </div>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </section>

    <div class="row g-4">
        <div class="col-12">
            <section class="card border-0 shadow-sm rounded-4" aria-labelledby="recentOrdersHeading">
                <div class="card-header bg-white border-0 rounded-top-4 px-4 pt-4 pb-3">
                    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                        <div>
                            <h2 class="h5 fw-bold mb-1" id="recentOrdersHeading">Recent orders</h2>
                            <p class="small text-secondary mb-0">The latest orders placed by your customers.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('orders.index') }}">View all orders</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small text-uppercase" scope="col">Order</th>
                                <th class="text-secondary small text-uppercase" scope="col">Customer</th>
                                <th class="text-secondary small text-uppercase" scope="col">Status</th>
                                <th class="text-secondary small text-uppercase text-end" scope="col">Total</th>
                                <th class="pe-4 text-secondary small text-uppercase text-end" scope="col">Placed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentOrders as $order)
                                @php
                                    $orderStatusClass = match ($order->status) {
                                        'delivered' => 'text-bg-success',
                                        'pending' => 'text-bg-warning',
                                        'confirmed' => 'text-bg-info',
                                        'processing' => 'text-bg-primary',
                                        'cancelled' => 'text-bg-danger',
                                        default => 'text-bg-secondary',
                                    };
                                @endphp
                                <tr>
                                    <td class="ps-4">
                                        <a class="fw-semibold link-dark text-decoration-none" href="{{ route('orders.show', $order) }}">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $order->customer_name ?: 'Walk-in customer' }}</div>
                                        <div class="small text-secondary">{{ $order->customer_phone ?: 'No phone provided' }}</div>
                                    </td>
                                    <td><span class="badge rounded-pill {{ $orderStatusClass }}">{{ ucfirst($order->status) }}</span></td>
                                    <td class="text-end fw-semibold text-nowrap">{{ number_format((float) $order->total, 0) }} {{ $order->currency }}</td>
                                    <td class="pe-4 text-end text-secondary text-nowrap">{{ $order->created_at->format('d M Y, H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-5 text-center">
                                        <i class="fa-solid fa-bag-shopping fs-3 text-secondary mb-2" aria-hidden="true"></i>
                                        <div class="fw-semibold">No orders yet</div>
                                        <div class="small text-secondary">New customer orders will appear here.</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="card border-0 shadow-sm rounded-4 h-100" aria-labelledby="connectionHealthHeading">
                <div class="card-header bg-white border-0 rounded-top-4 px-4 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1" id="connectionHealthHeading">Connection health</h2>
                            <p class="small text-secondary mb-0">Status of your social accounts.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('connections.index') }}">Manage</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 text-secondary small text-uppercase" scope="col">Platform</th>
                                <th class="text-secondary small text-uppercase" scope="col">Account</th>
                                <th class="text-secondary small text-uppercase" scope="col">Status</th>
                                <th class="pe-4 text-secondary small text-uppercase text-end" scope="col">Enabled</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($connections as $connection)
                                <tr>
                                    <td class="ps-4 fw-semibold">{{ ucfirst($connection->provider->value) }}</td>
                                    <td>{{ $connection->name }}</td>
                                    <td>
                                        <span class="badge rounded-pill {{ $connection->status === 'connected' ? 'text-bg-success' : 'text-bg-secondary' }}">
                                            {{ ucfirst($connection->status) }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <span class="badge rounded-pill {{ $connection->active ? 'text-bg-success' : 'text-bg-light border text-secondary' }}">
                                            {{ $connection->active ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-5 text-center">
                                        <i class="fa-solid fa-share-nodes fs-3 text-secondary mb-2" aria-hidden="true"></i>
                                        <div class="fw-semibold">No accounts connected</div>
                                        <div class="small text-secondary mb-3">Connect a social account to start publishing.</div>
                                        <a class="btn btn-sm btn-dark" href="{{ route('connections.index') }}">Connect an account</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="col-12 col-xl-6">
            <section class="card border-0 shadow-sm rounded-4 h-100" aria-labelledby="recentPostsHeading">
                <div class="card-header bg-white border-0 rounded-top-4 px-4 pt-4 pb-3">
                    <div class="d-flex justify-content-between align-items-center gap-3">
                        <div>
                            <h2 class="h5 fw-bold mb-1" id="recentPostsHeading">Recent social posts</h2>
                            <p class="small text-secondary mb-0">Review drafts and publishing activity.</p>
                        </div>
                        <a class="btn btn-sm btn-outline-dark" href="{{ route('posts.index') }}">View all</a>
                    </div>
                </div>
                <div class="list-group list-group-flush">
                    @forelse ($posts as $post)
                        @php
                            $postStatusClass = match ($post->status) {
                                'published' => 'text-bg-success',
                                'scheduled' => 'text-bg-primary',
                                'draft' => 'text-bg-secondary',
                                default => 'text-bg-warning',
                            };
                        @endphp
                        <a class="list-group-item list-group-item-action px-4 py-3" href="{{ route('posts.show', $post) }}">
                            <div class="d-flex justify-content-between align-items-start gap-3">
                                <div class="flex-grow-1 overflow-hidden">
                                    <div class="fw-semibold text-truncate">{{ $post->title ?: 'Untitled post' }}</div>
                                    <div class="small text-secondary mt-1">
                                        {{ $post->created_at->format('d M Y') }}
                                        <span class="mx-1" aria-hidden="true">&middot;</span>
                                        {{ number_format($post->publications_count) }} {{ \Illuminate\Support\Str::plural('publication', $post->publications_count) }}
                                    </div>
                                </div>
                                <span class="badge rounded-pill flex-shrink-0 {{ $postStatusClass }}">{{ ucfirst($post->status) }}</span>
                            </div>
                        </a>
                    @empty
                        <div class="px-4 py-5 text-center">
                            <i class="fa-solid fa-paper-plane fs-3 text-secondary mb-2" aria-hidden="true"></i>
                            <div class="fw-semibold">No social posts yet</div>
                            <div class="small text-secondary mb-3">Create a post to share your latest collection.</div>
                            <a class="btn btn-sm btn-dark" href="{{ route('posts.create') }}">Create a post</a>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
@endsection
