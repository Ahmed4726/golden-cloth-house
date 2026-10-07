@extends('layouts.app')

@section('content')
    <div class="top">
        <div>
            <h1>Upload Collection PDF</h1>
            <p class="muted">The PDF will be opened in a visual builder where you can select images for every product design.</p>
        </div>
        <a class="btn secondary" href="{{ route('catalog-imports.index') }}">Back</a>
    </div>

    <form class="card form" method="POST" action="{{ route('catalog-imports.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="field">
            <label for="pdf">Collection PDF</label>
            <input id="pdf" name="pdf" type="file" accept="application/pdf" required>
            <small class="muted">Maximum 50 MB. Use a high-quality catalogue PDF so the generated product images remain sharp.</small>
        </div>

        <div class="flash">
            <strong>How it works:</strong> upload the PDF → pages are rendered in your browser → choose pages for each design → enter retail price → the default 3% discount is calculated automatically → publish the selected products.
        </div>

        <button class="btn" type="submit">Open Product Builder</button>
    </form>
@endsection
