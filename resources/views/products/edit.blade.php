@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div><h1 class="h2 fw-bold mb-1">Edit Product</h1><div class="text-secondary">Update product details, inventory and images.</div></div>
        <a class="btn btn-outline-dark" href="{{ route('products.index') }}">Back</a>
    </div>

    <form class="card border-0 shadow-sm" method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="card-body p-4">@include('products.form', ['product' => $product])</div>
        <div class="card-footer bg-white p-4"><button class="btn btn-dark" type="submit">Save Changes</button></div>
    </form>
@endsection
