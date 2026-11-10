<?php

namespace App\Http\Controllers;

use App\Models\StoreSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StoreSettingController extends Controller
{
    public function edit(): View
    {
        $settings = StoreSetting::query()->find(StoreSetting::SINGLETON_ID);

        return view('settings.edit', [
            'whatsappNumber' => $settings
                ? $settings->whatsapp_number
                : config('store.whatsapp'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'whatsapp_number' => ['nullable', 'string', 'max:32', 'regex:/^\+?[0-9\s().-]+$/'],
        ]);

        $whatsappNumber = preg_replace('/\D+/', '', $data['whatsapp_number'] ?? '') ?? '';

        if ($whatsappNumber !== '' && ! preg_match('/^[0-9]{8,15}$/', $whatsappNumber)) {
            throw ValidationException::withMessages([
                'whatsapp_number' => 'Enter a WhatsApp number with 8 to 15 digits, including its country code.',
            ]);
        }

        $settings = StoreSetting::query()->find(StoreSetting::SINGLETON_ID) ?? new StoreSetting;
        $settings->id = StoreSetting::SINGLETON_ID;
        $settings->whatsapp_number = $whatsappNumber === '' ? null : $whatsappNumber;
        $settings->save();

        return redirect()
            ->route('settings.edit')
            ->with('success', 'WhatsApp number updated successfully.');
    }
}
