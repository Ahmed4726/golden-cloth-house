@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Brands</h1><div class="text-secondary">Manage product brands.</div></div>
        <a class="btn btn-dark" href="{{ route('brands.create') }}">Add brand</a>
    </div>

    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('brands.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="brand-search">Search brands</label>
                    <input class="form-control" id="brand-search" name="q" value="{{ request('q') }}" placeholder="Name or slug">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label" for="brand-status">Status</label>
                    <select class="form-select" id="brand-status" name="status">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('brands.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @forelse ($brands as $brand)
                <div class="list-group-item p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @if ($brand->logo_path)<div class="ratio ratio-1x1 bg-light rounded overflow-hidden w-25"><img class="w-100 h-100 object-fit-contain" src="{{ asset('storage/'.$brand->logo_path) }}" alt="{{ $brand->name }}"></div>@endif
                            <div>
                                <strong>{{ $brand->name }}</strong>
                                <div class="text-secondary small">{{ $brand->slug }} · {{ $brand->products_count }} products</div>
                                <span class="badge {{ $brand->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $brand->active ? 'Active' : 'Hidden' }}</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('brands.edit', $brand) }}">Edit</a>
                            <form method="post" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-secondary">No brands match these filters.</div>
            @endforelse
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($brands->total())
                    Showing {{ $brands->firstItem() }}–{{ $brands->lastItem() }} of {{ $brands->total() }} brands
                @else
                    No matching brands
                @endif
            </span>
            {{ $brands->links() }}
        </div>
    </div>
@endsection
