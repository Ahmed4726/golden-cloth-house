@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Brands</h1><div class="text-secondary">Manage product brands.</div></div>
        <a class="btn btn-dark" href="{{ route('brands.create') }}">Add brand</a>
    </div>
    <div class="card border-0 shadow-sm">
        <div class="list-group list-group-flush">
            @foreach ($brands as $brand)
                <div class="list-group-item p-3">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div class="d-flex align-items-center gap-3">
                            @if ($brand->logo_path)<div class="ratio ratio-1x1 bg-light rounded overflow-hidden" style="width:45px;"><img class="w-100 h-100 object-fit-contain" src="{{ asset('storage/'.$brand->logo_path) }}" alt="{{ $brand->name }}"></div>@endif
                            <div><strong>{{ $brand->name }}</strong><div class="text-secondary small">{{ $brand->slug }} · {{ $brand->products_count }} products</div></div>
                        </div>
                        <div class="d-flex gap-2">
                            <a class="btn btn-sm btn-outline-dark" href="{{ route('brands.edit', $brand) }}">Edit</a>
                            <form method="post" action="{{ route('brands.destroy', $brand) }}" onsubmit="return confirm('Delete this brand?')">
                                @csrf @method('DELETE') <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="card-footer bg-white">{{ $brands->links() }}</div>
    </div>
@endsection
