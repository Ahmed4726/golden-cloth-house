@extends('layouts.app')
@section('content')
<div class="top"><div><h1>Edit brand</h1></div></div>
<form class="card form" method="post" enctype="multipart/form-data" action="{{ route('brands.update', $brand) }}">
    @csrf @method('PUT')
    @include('brands.form')
    <button class="btn">Save changes</button>
</form>
@endsection
