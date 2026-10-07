@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Upload Collection PDF</h1>
            <p class="muted">
                Upload a supplier catalogue and turn its pages into product-gallery images without manually saving screenshots.
            </p>
        </div>
        <a class="btn secondary" href="{{ route('catalog-imports.index') }}">Back</a>
    </div>

    <form class="card form" method="POST" action="{{ route('catalog-imports.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="field">
            <label for="pdf">Collection PDF</label>
            <input id="pdf" name="pdf" type="file" accept="application/pdf" required>
            <small class="muted">
                Maximum 50 MB. High-resolution catalogue PDFs are recommended for better product-gallery quality.
            </small>
        </div>

        <div class="flash">
            <strong>Automatic workflow:</strong>
            upload PDF → all pages are rendered as images → the builder pre-creates the known designs when the filename matches
            <strong>Lime Vol-552</strong> or <strong>Leore Vol-01</strong> → select/change pages → review title, description, fabric, season, stock and price → 3% discount is calculated → create listings.
        </div>

        <div class="flash">
            <strong>Image setup:</strong>
            uploaded product images use Laravel's public storage. Run <code>php artisan storage:link</code> once after installing or deploying the project.
        </div>

        <button class="btn" type="submit">Open Product Builder</button>
    </form>
@endsection
