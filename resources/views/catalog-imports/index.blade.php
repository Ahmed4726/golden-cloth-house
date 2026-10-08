@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center gap-3 mb-4"><div><h1 class="h2 fw-bold mb-1">PDF Catalogs</h1><p class="text-secondary mb-0">Upload a collection PDF, select the pages for each design, and create ready-to-sell listings.</p></div><a class="btn btn-dark" href="{{ route('catalog-imports.create') }}">Upload PDF</a></div>
    <div class="card border-0 shadow-sm"><div class="table-responsive"><table class="table table-hover align-middle mb-0"><thead><tr><th>Catalog</th><th>Status</th><th>Products</th><th>Uploaded</th><th></th></tr></thead><tbody>
        @forelse ($imports as $import)
            <tr><td><strong>{{ $import->name }}</strong><div class="text-secondary small">{{ basename($import->pdf_path) }}</div></td><td><span class="badge text-bg-success">{{ ucfirst($import->status) }}</span></td><td>{{ $import->products_count }}</td><td>{{ $import->created_at->format('d M Y, h:i A') }}</td><td><div class="d-flex gap-2"><a class="btn btn-sm btn-outline-dark" href="{{ route('catalog-imports.builder', $import) }}">Open</a><form method="POST" action="{{ route('catalog-imports.destroy', $import) }}" onsubmit="return confirm('Delete this catalog and all products created from it?');">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Delete</button></form></div></td></tr>
        @empty
            <tr><td colspan="5" class="text-secondary">No PDF catalogs uploaded yet.</td></tr>
        @endforelse
    </tbody></table></div><div class="card-footer bg-white">{{ $imports->links() }}</div></div>
@endsection
