@extends('layouts.app')
@section('content')
<div class="top"><div><h1>Add brand</h1></div></div>
<form class="card form" method="post" enctype="multipart/form-data" action="{{ route('brands.store') }}">
    @csrf
    @include('brands.form')
    <button class="btn">Create brand</button>
</form>
@endsection
