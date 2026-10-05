@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Edit Product</h1>
            <div class="muted">Update product details, inventory and images.</div>
        </div>
        <a class="btn secondary" href="{{ route('products.index') }}">Back</a>
    </div>

    <form
        class="card form"
        method="POST"
        action="{{ route('products.update', $product) }}"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        @include('products.form', ['product' => $product])

        <button class="btn" type="submit">Save Changes</button>
    </form>
@endsection
