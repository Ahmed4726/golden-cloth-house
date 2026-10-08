@extends('layouts.app')
@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Social Connections</h1><div class="text-secondary">Connect the shop's official social accounts.</div></div>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
        @foreach (\App\Enums\SocialProvider::cases() as $provider)
            @php($c = $connections->firstWhere('provider', $provider))
            <div class="col"><div class="card border-0 shadow-sm h-100"><div class="card-body"><h2 class="h5">{{ ucfirst($provider->value) }}</h2><p class="text-secondary">@if($c){{ $c->name }}<br>Status: {{ $c->status }}@else Not connected @endif</p>@if($c)<div class="d-flex gap-2"><form method="post" action="{{ route('connections.toggle', $c) }}">@csrf<button class="btn btn-sm btn-outline-dark" type="submit">{{ $c->active ? 'Disable' : 'Enable' }}</button></form><form method="post" action="{{ route('connections.destroy', $c) }}">@csrf @method('delete')<button class="btn btn-sm btn-outline-danger" type="submit">Remove</button></form></div>@else<a class="btn btn-dark" href="{{ route('social.oauth', $provider->value) }}">Connect</a>@endif</div></div></div>
        @endforeach
    </div>
    <div class="card border-0 shadow-sm mt-4"><div class="card-body"><h2 class="h5">Platform readiness</h2><p class="text-secondary mb-0">TikTok Direct Post requires an approved <code>video.publish</code> scope; unaudited clients are restricted to private posting. YouTube uploads require OAuth with <code>youtube.upload</code>.</p></div></div>
@endsection
