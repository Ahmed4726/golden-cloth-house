@extends('layouts.app')

@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Edit fabric</h1><p class="text-secondary mb-0">Update this dropdown option.</p></div>
    <form class="card border-0 shadow-sm" method="POST" action="{{ route('fabrics.update', $fabric) }}">
        @csrf
        @method('PUT')
        <div class="card-body p-4">@include('fabrics.form')</div>
        <div class="card-footer bg-white p-3 d-flex gap-2">
            <button class="btn btn-dark" type="submit">Save changes</button>
            <a class="btn btn-outline-secondary" href="{{ route('fabrics.index') }}">Cancel</a>
        </div>
    </form>
@endsection
