@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Homepage Slider</h1>
            <div class="muted">Manage the hero banners shown on your storefront.</div>
        </div>
        <a class="btn" href="{{ route('slides.create') }}">Add Slide</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Preview</th>
                    <th>Slide</th>
                    <th>Order</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($slides as $slide)
                    <tr>
                        <td>
                            @if ($slide->image_path)
                                <img src="{{ asset('storage/'.$slide->image_path) }}" alt="{{ $slide->title }}" style="width:140px;height:70px;object-fit:cover;border-radius:8px;">
                            @else
                                <span class="muted">No image</span>
                            @endif
                        </td>
                        <td><strong>{{ $slide->title }}</strong><div class="muted">{{ $slide->subtitle }}</div></td>
                        <td>{{ $slide->sort_order }}</td>
                        <td><span class="badge {{ $slide->active ? 'ok' : '' }}">{{ $slide->active ? 'Active' : 'Hidden' }}</span></td>
                        <td>
                            <div class="row">
                                <a class="btn secondary" href="{{ route('slides.edit', $slide) }}">Edit</a>
                                <form method="POST" action="{{ route('slides.destroy', $slide) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5">No slides yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
