@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Fabrics</h1>
            <p class="text-secondary mb-0">Manage the options used for kameez, shalwar/trouser and dupatta.</p>
        </div>
        <a class="btn btn-dark" href="{{ route('fabrics.create') }}">Add fabric</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead><tr><th>Name</th><th>Status</th><th>Sort order</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                    @forelse ($fabrics as $fabric)
                        <tr>
                            <td class="fw-semibold">{{ $fabric->name }}</td>
                            <td><span class="badge {{ $fabric->active ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $fabric->active ? 'Active' : 'Inactive' }}</span></td>
                            <td>{{ $fabric->sort_order }}</td>
                            <td class="text-end">
                                <div class="d-inline-flex gap-2">
                                    <a class="btn btn-sm btn-outline-dark" href="{{ route('fabrics.edit', $fabric) }}">Edit</a>
                                    <form method="POST" action="{{ route('fabrics.destroy', $fabric) }}" onsubmit="return confirm('Delete this fabric? Products will keep their saved fabric details.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center text-secondary py-4">No fabrics have been added.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $fabrics->links() }}</div>
    </div>
@endsection
