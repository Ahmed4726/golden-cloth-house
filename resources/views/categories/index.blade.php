@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Categories</h1><div class="text-secondary">Manage product categories.</div></div>
        <a class="btn btn-dark" href="{{ route('categories.create') }}">Add category</a>
    </div>

    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('categories.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="category-search">Search categories</label>
                    <input class="form-control" id="category-search" name="q" value="{{ request('q') }}" placeholder="Name or slug">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label" for="category-status">Status</label>
                    <select class="form-select" id="category-status" name="status">
                        <option value="">All statuses</option>
                        <option value="active" @selected(request('status') === 'active')>Active</option>
                        <option value="hidden" @selected(request('status') === 'hidden')>Hidden</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('categories.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @forelse ($categories as $category)
                <div class="list-group-item p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <div class="text-secondary small">{{ $category->slug }} · {{ $category->products_count }} products</div>
                            <span class="badge {{ $category->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->active ? 'Active' : 'Hidden' }}</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('categories.edit', $category) }}">Edit</a>
                            <form method="post" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Products will keep their existing category text but lose the category relation.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="list-group-item text-secondary">No categories match these filters.</div>
            @endforelse
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($categories->total())
                    Showing {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} categories
                @else
                    No matching categories
                @endif
            </span>
            {{ $categories->links() }}
        </div>
    </div>
@endsection
