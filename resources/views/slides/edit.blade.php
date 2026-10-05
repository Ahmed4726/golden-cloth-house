@extends('layouts.app')

@section('content')
    <div class="top">
        <h1>Edit Homepage Slide</h1>
        <a class="btn secondary" href="{{ route('slides.index') }}">Back</a>
    </div>

    <form class="card form" method="POST" action="{{ route('slides.update', $slide) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('slides.form')
        <button class="btn" type="submit">Save Changes</button>
    </form>
@endsection
