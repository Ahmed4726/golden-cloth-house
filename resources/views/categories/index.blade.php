@extends('layouts.app')

@section('content')
<div class="top">
    <div><h1>Categories</h1><div class="muted">Manage product categories.</div></div>
    <a class="btn" href="{{ route('categories.create') }}">Add category</a>
</div>

<div class="card">
    @foreach ($categories as $category)
        <div class="row" style="justify-content:space-between;align-items:center;border-bottom:1px solid #eee;padding:14px 0;">
            <div><strong>{{ $category->name }}</strong><div class="muted">{{ $category->slug }} · {{ $category->products_count }} products</div></div>
            <div class="row">
                <a class="btn secondary" href="{{ route('categories.edit', $category) }}">Edit</a>
                <form method="post" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Products will keep their existing category text but lose the category relation.')">
                    @csrf @method('DELETE') <button class="btn danger">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
    {{ $categories->links() }}
</div>
@endsection
