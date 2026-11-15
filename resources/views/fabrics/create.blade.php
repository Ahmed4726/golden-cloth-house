@extends('layouts.app')

@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Add fabric</h1><p class="text-secondary mb-0">Create an option for product fabric dropdowns.</p></div>
    <form class="card border-0 shadow-sm" method="POST" action="{{ route('fabrics.store') }}">
        @csrf
        <div class="card-body p-4">@include('fabrics.form')</div>
        <div class="card-footer bg-white p-3 d-flex gap-2">
            <button class="btn btn-dark" type="submit">Save fabric</button>
            <a class="btn btn-outline-secondary" href="{{ route('fabrics.index') }}">Cancel</a>
        </div>
    </form>
@endsection
