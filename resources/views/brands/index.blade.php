@extends('layouts.app')
@section('content')
<div class="top">
    <div><h1>Brands</h1><div class="muted">Manage product brands.</div></div>
    <a class="btn" href="{{ route('brands.create') }}">Add brand</a>
</div>
<div class="card">
    @foreach ($brands as $brand)
        <div class="row" style="justify-content:space-between;align-items:center;border-bottom:1px solid #eee;padding:14px 0;">
            <div class="row" style="align-items:center">
                @if ($brand->logo_path)<img src="{{ asset('storage/'.$brand->logo_path) }}" alt="{{ $brand->name }}" style="width:45px;height:45px;object-fit:contain;border-radius:6px">@endif
                <div><strong>{{ $brand->name }}</strong><div class="muted">{{ $brand->slug }} · {{ $brand->products_count }} products</div></div>
            </div>
            <div class="row">
                <a class="btn secondary" href="{{ route('brands.edit', $brand) }}">Edit</a>
                <form method="post" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand?')">
                    @csrf @method('DELETE') <button class="btn danger">Delete</button>
                </form>
            </div>
        </div>
    @endforeach
    {{ $brands->links() }}
</div>
@endsection
