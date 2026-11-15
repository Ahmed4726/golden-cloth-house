@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4"><div><h1 class="h2 fw-bold mb-1">PDF Catalogs</h1><p class="text-secondary mb-0">Upload a collection PDF, select the pages for each design, and create ready-to-sell listings.</p></div><a class="btn btn-dark" href="{{ route('catalog-imports.create') }}">Upload PDF</a></div>
    <form class="card border-0 shadow-sm mb-4" method="GET" action="{{ route('catalog-imports.index') }}">
        <div class="card-body">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-6">
                    <label class="form-label" for="catalog-search">Search catalogs</label>
                    <input class="form-control" id="catalog-search" name="q" value="{{ request('q') }}" placeholder="Catalog name">
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label" for="catalog-status">Status</label>
                    <select class="form-select" id="catalog-status" name="status">
                        <option value="">All statuses</option>
                        @foreach (['uploaded' => 'Uploaded', 'completed' => 'Drafts ready', 'published' => 'Published'] as $value => $label)
                            <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-3 d-flex gap-2">
                    <button class="btn btn-dark flex-grow-1" type="submit">Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('catalog-imports.index') }}">Reset</a>
                </div>
            </div>
        </div>
    </form>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Catalog</th><th>Status</th><th>Products</th><th>Uploaded</th><th></th></tr></thead><tbody>
        @forelse ($imports as $import)
            <tr><td><strong>{{ $import->name }}</strong><div class="text-secondary small">{{ basename($import->pdf_path) }}</div></td><td><span class="badge {{ $import->status === 'published' ? 'text-bg-success' : ($import->status === 'completed' ? 'text-bg-warning' : 'text-bg-light') }}">{{ $import->status === 'completed' ? 'Drafts ready' : ucfirst($import->status) }}</span></td><td>{{ $import->products_count }}</td><td>{{ $import->created_at->format('d M Y, h:i A') }}</td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-dark" href="{{ $import->status === 'uploaded' ? route('catalog-imports.builder', $import) : route('catalog-imports.review', $import) }}">{{ $import->status === 'uploaded' ? 'Open builder' : 'Review' }}</a><form method="POST" action="{{ route('catalog-imports.destroy', $import) }}" onsubmit="return confirm('Delete this catalog and all products created from it?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></div></td></tr>
        @empty
            <tr><td colspan="5" class="text-secondary">No PDF catalogs uploaded yet.</td></tr>
        @endforelse
    </tbody></table></div>
        <div class="card-footer bg-white d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
            <span class="text-secondary small">
                @if ($imports->total())
                    Showing {{ $imports->firstItem() }}–{{ $imports->lastItem() }} of {{ $imports->total() }} catalogs
                @else
                    No matching catalogs
                @endif
            </span>
            {{ $imports->links() }}
        </div>
    </div>
@endsection
