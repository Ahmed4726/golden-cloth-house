@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Coupons</h1><p class="text-secondary mb-0">Discount codes and redemption limits.</p></div>
        <a class="btn btn-dark" href="{{ route('coupons.create') }}">Add coupon</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('coupons.index') }}">
        <div class="card-body row g-2 align-items-end">
            <div class="col-md-6"><label class="form-label" for="coupon-search">Code</label><input class="form-control" id="coupon-search" name="q" value="{{ request('q') }}"></div>
            <div class="col-md-3">
                <label class="form-label" for="coupon-status">Status</label>
                <select class="form-select" id="coupon-status" name="status">
                    <option value="">All</option>
                    <option value="active" @selected(request('status') === 'active')>Active</option>
                    <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2"><button class="btn btn-dark flex-grow-1" type="submit">Filter</button><a class="btn btn-outline-secondary" href="{{ route('coupons.index') }}">Reset</a></div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Code</th><th>Discount</th><th>Redemptions</th><th>Status / validity</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($coupons as $coupon)
                        <tr>
                            <td class="fw-bold">{{ $coupon->code }}</td>
                            <td>{{ $coupon->discount_type === 'percentage' ? number_format($coupon->discount_value, 2).'%' : number_format($coupon->discount_value, 2).' '.config('store.currency') }}</td>
                            <td>{{ $coupon->usage_count }}{{ $coupon->max_redemptions ? ' / '.$coupon->max_redemptions : '' }}<div class="small text-secondary">{{ $coupon->orders_count }} linked orders</div></td>
                            <td><span class="badge {{ $coupon->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $coupon->active ? 'Active' : 'Inactive' }}</span><div class="small text-secondary">{{ $coupon->expires_at?->format('M j, Y H:i') ?? 'No expiry' }}</div></td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-outline-dark" href="{{ route('coupons.edit', $coupon) }}">Edit</a>
                                    <form method="POST" action="{{ route('coupons.destroy', $coupon) }}" onsubmit="return confirm('Delete this coupon? Existing orders retain the recorded code and discount.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-secondary py-4">No coupons found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $coupons->links() }}</div>
    </div>
@endsection
