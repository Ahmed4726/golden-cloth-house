@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>PDF Catalogs</h1>
            <p class="muted">Upload a collection PDF, select the pages for each design, and create ready-to-sell listings.</p>
        </div>
        <a class="btn" href="{{ route('catalog-imports.create') }}">Upload PDF</a>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Catalog</th>
                    <th>Status</th>
                    <th>Products</th>
                    <th>Uploaded</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($imports as $import)
                    <tr>
                        <td>
                            <strong>{{ $import->name }}</strong>
                            <div class="muted">{{ basename($import->pdf_path) }}</div>
                        </td>
                        <td><span class="badge ok">{{ ucfirst($import->status) }}</span></td>
                        <td>{{ $import->products_count }}</td>
                        <td>{{ $import->created_at->format('d M Y, h:i A') }}</td>
                        <td class="row">
                            <a class="btn secondary" href="{{ route('catalog-imports.builder', $import) }}">Open</a>
                            <form method="POST" action="{{ route('catalog-imports.destroy', $import) }}" onsubmit="return confirm('Delete this catalog and all products created from it?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn danger" type="submit">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="muted">No PDF catalogs uploaded yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top:18px;">{{ $imports->links() }}</div>
    </div>
@endsection
