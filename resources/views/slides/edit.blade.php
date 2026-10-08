@extends('layouts.app')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4"><h1 class="h2 fw-bold">Edit Homepage Slide</h1><a class="btn btn-outline-dark" href="{{ route('slides.index') }}">Back</a></div>
    <form class="card border-0 shadow-sm" method="POST" action="{{ route('slides.update', $slide) }}" enctype="multipart/form-data">
        @csrf @method('PUT')
        <div class="card-body p-4">@include('slides.form')</div>
        <div class="card-footer bg-white p-4"><button class="btn btn-dark" type="submit">Save Changes</button></div>
    </form>
@endsection
