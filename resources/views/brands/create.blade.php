@extends('layouts.app')
@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold">Add brand</h1></div>
    <form class="card border-0 shadow-sm" method="post" enctype="multipart/form-data" action="{{ route('brands.store') }}">
        @csrf
        <div class="card-body p-4">@include('brands.form')</div>
        <div class="card-footer bg-white p-4"><button class="btn btn-dark">Create brand</button></div>
    </form>
@endsection
