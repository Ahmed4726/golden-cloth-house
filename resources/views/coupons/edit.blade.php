@extends('layouts.app')

@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Edit coupon</h1><p class="text-secondary mb-0">Update discount details or redemption limits.</p></div>
    <form class="card border-0 shadow-sm" method="POST" action="{{ route('coupons.update', $coupon) }}">
        @csrf
        @method('PUT')
        <div class="card-body p-4">@include('coupons.form')</div>
        <div class="card-footer bg-white p-3 d-flex gap-2"><button class="btn btn-dark" type="submit">Save changes</button><a class="btn btn-outline-secondary" href="{{ route('coupons.index') }}">Cancel</a></div>
    </form>
@endsection
