@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4"><div><h1 class="h2 fw-bold mb-1">Posts</h1><div class="text-secondary">Draft, publish and review multi-channel posts.</div></div><a class="btn btn-dark" href="{{ route('posts.create') }}">Create Post</a></div>
    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('posts.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="post-search">Search posts</label>
                    <input class="form-control" id="post-search" name="q" value="{{ request('q') }}" placeholder="Title, caption or product">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label" for="post-status">Status</label>
                    <select class="form-select" id="post-status" name="status">
                        <option value="">All statuses</option>
                        @foreach (['draft', 'scheduled', 'queued', 'processing', 'published', 'failed'] as $status)
                            <option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('posts.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead><tr><th>Post</th><th>Status</th><th>Publications</th><th>Date</th></tr></thead>
                <tbody>
                    @forelse ($posts as $post)
                        <tr>
                            <td><a href="{{ route('posts.show', $post) }}"><strong>{{ $post->title ?: 'Untitled' }}</strong></a><div class="text-secondary small">{{ \Illuminate\Support\Str::limit($post->caption, 80) }}</div></td>
                            <td><span class="badge text-bg-secondary">{{ $post->status }}</span></td>
                            <td>{{ $post->publications_count }}</td>
                            <td>{{ $post->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-secondary">No posts match these filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($posts->total())
                    Showing {{ $posts->firstItem() }}–{{ $posts->lastItem() }} of {{ $posts->total() }} posts
                @else
                    No matching posts
                @endif
            </span>
            {{ $posts->links() }}
        </div>
    </div>
@endsection
