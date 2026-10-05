@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Add Product</h1>
            <div class="muted">Create a product for your website and social content.</div>
        </div>
        <a class="btn secondary" href="{{ route('products.index') }}">Back</a>
    </div>

    <form
        class="card form"
        method="POST"
        action="{{ route('products.store') }}"
        enctype="multipart/form-data"
    >
        @csrf

        @include('products.form')

        <button class="btn" type="submit">Save Product</button>
    </form>
@endsection
