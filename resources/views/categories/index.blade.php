@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Categories</h1><div class="text-secondary">Manage product categories.</div></div>
        <a class="btn btn-dark" href="{{ route('categories.create') }}">Add category</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @foreach ($categories as $category)
                <div class="list-group-item p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div><strong>{{ $category->name }}</strong><div class="text-secondary small">{{ $category->slug }} · {{ $category->products_count }} products</div></div>
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('categories.edit', $category) }}">Edit</a>
                            <form method="post" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Products will keep their existing category text but lose the category relation.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer bg-white">{{ $categories->links() }}</div>
    </div>
@endsection
