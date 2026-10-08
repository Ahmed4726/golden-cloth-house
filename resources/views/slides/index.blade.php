@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Homepage Slider</h1>
            <div class="text-secondary">Manage the hero banners shown on your storefront.</div>
        </div>
        <a class="btn btn-dark" href="{{ route('slides.create') }}">Add Slide</a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
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
                                <div class="ratio ratio-2x1 bg-light rounded overflow-hidden w-50">
                                    <img
                                        class="w-100 h-100 object-fit-cover"
                                        src="{{ $slide->imageUrl() ?: asset('images/product-placeholder.svg') }}"
                                        alt="{{ $slide->title }}"
                                        loading="lazy"
                                        onerror="this.onerror=null;this.src='{{ asset('images/product-placeholder.svg') }}';"
                                    >
                                </div>
                            </td>
                            <td>
                                <strong>{{ $slide->title }}</strong>
                                <div class="text-secondary small">{{ $slide->subtitle }}</div>
                            </td>
                            <td>{{ $slide->sort_order }}</td>
                            <td>
                                <span class="badge {{ $slide->active ? 'text-bg-success' : 'text-bg-secondary' }}">
                                    {{ $slide->active ? 'Active' : 'Hidden' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-sm btn-outline-dark" href="{{ route('slides.edit', $slide) }}">Edit</a>
                                    <form method="POST" action="{{ route('slides.destroy', $slide) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-secondary">No slides yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
