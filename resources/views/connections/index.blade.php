@extends('layouts.app')
@section('content')
    <div class="mb-4"><h1 class="h2 fw-bold mb-1">Social Connections</h1><div class="text-secondary">Connect the shop's official social accounts.</div></div>
    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('connections.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label" for="connection-search">Search connections</label>
                    <input class="form-control" id="connection-search" name="q" value="{{ request('q') }}" placeholder="Provider or account name">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label" for="connection-provider">Provider</label>
                    <select class="form-select" id="connection-provider" name="provider">
                        <option value="">All providers</option>
                        @foreach (\App\Enums\SocialProvider::cases() as $provider)
                            <option value="{{ $provider->value }}" @selected(request('provider') === $provider->value)>{{ ucfirst($provider->value) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label" for="connection-active">Connection state</label>
                    <select class="form-select" id="connection-active" name="active">
                        <option value="">All states</option>
                        <option value="1" @selected(request('active') === '1')>Enabled</option>
                        <option value="0" @selected(request('active') === '0')>Disabled</option>
                    </select>
                </div>
                <div class="col-12 col-md-2 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('connections.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3">
        @forelse ($providers as $provider)
            @php($c = $connections->get($provider->value))
            <div class="col"><div class="card border-0 shadow-sm h-100"><div class="card-body"><h2 class="h5">{{ ucfirst($provider->value) }}</h2><p class="text-secondary">@if($c){{ $c->name }}<br>Status: {{ $c->status }}@else Not connected @endif</p>@if($c)<div class="d-flex gap-2"><form method="post" action="{{ route('connections.toggle', $c) }}">@csrf<button class="btn btn-sm btn-outline-dark" type="submit">{{ $c->active ? 'Disable' : 'Enable' }}</button></form><form method="post" action="{{ route('connections.destroy', $c) }}">@csrf @method('delete')<button class="btn btn-sm btn-outline-danger" type="submit">Remove</button></form></div>@else<a class="btn btn-dark" href="{{ route('social.oauth', $provider->value) }}">Connect</a>@endif</div></div></div>
        @empty
            <div class="col-12"><div class="alert alert-secondary mb-0">No connections match these filters.</div></div>
        @endforelse
    </div>
    <div class="card border-0 shadow-sm mt-4"><div class="card-body"><h2 class="h5">Platform readiness</h2><p class="text-secondary mb-0">TikTok Direct Post requires an approved <code>video.publish</code> scope; unaudited clients are restricted to private posting. YouTube uploads require OAuth with <code>youtube.upload</code>.</p></div></div>
@endsection
