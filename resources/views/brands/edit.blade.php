@extends('layouts.app')
@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold">Edit brand</h1></div>
    <form class="card border-0 shadow-sm" method="post" enctype="multipart/form-data" action="{{ route('brands.update', $brand) }}">
        @csrf @method('PUT')
        <div class="card-body p-4">@include('brands.form')</div>
        <div class="card-footer bg-white p-4"><button class="btn btn-dark">Save changes</button></div>
    </form>
@endsection
