@extends('layouts.app')
@section('content')
<div class="top"><div><h1>Edit category</h1></div></div>
<form class="card form" method="post" action="{{ route('categories.update', $category) }}">
    @csrf @method('PUT')
    @include('categories.form')
    <button class="btn">Save changes</button>
</form>
@endsection
