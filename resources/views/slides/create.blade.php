@extends('layouts.app')

@section('content')
    <div class="top">
        <h1>Add Homepage Slide</h1>
        <a class="btn secondary" href="{{ route('slides.index') }}">Back</a>
    </div>

    <form class="card form" method="POST" action="{{ route('slides.store') }}" enctype="multipart/form-data">
        @csrf
        @include('slides.form')
        <button class="btn" type="submit">Save Slide</button>
    </form>
@endsection
