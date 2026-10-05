@extends('layouts.app')
@section('content')
<div class="top"><div><h1>Add category</h1></div></div>
<form class="card form" method="post" action="{{ route('categories.store') }}">
    @csrf
    @include('categories.form')
    <button class="btn">Create category</button>
</form>
@endsection
