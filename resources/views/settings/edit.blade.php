@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">Store Settings</h1>
            <p class="text-secondary mb-0">Manage the WhatsApp number customers use to place orders.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('settings.update') }}" class="col-lg-7">
                @csrf
                @method('PATCH')

                <label class="form-label fw-semibold" for="whatsapp_number">WhatsApp number</label>
                <input
                    class="form-control"
                    id="whatsapp_number"
                    name="whatsapp_number"
                    type="tel"
                    value="{{ old('whatsapp_number', $whatsappNumber) }}"
                    placeholder="923001234567"
                    autocomplete="tel"
                    aria-describedby="whatsappNumberHelp"
                >
                <div class="form-text mb-3" id="whatsappNumberHelp">
                    Include the country code (for example, 92 for Pakistan). Spaces, +, brackets, and dashes are accepted and removed when saved. Leave blank to hide WhatsApp order buttons.
                </div>

                <button class="btn btn-dark" type="submit">
                    <i class="fa-solid fa-floppy-disk me-2" aria-hidden="true"></i>Save WhatsApp Number
                </button>
            </form>
        </div>
    </div>
@endsection
