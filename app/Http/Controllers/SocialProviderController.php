<?php

namespace App\Http\Controllers;

use App\Enums\SocialProvider;
use App\Models\SocialConnection;

class SocialProviderController extends Controller
{
    public function readiness()
    {
        return response()->json(collect(SocialProvider::cases())->mapWithKeys(function (SocialProvider $provider) {
            $connections = SocialConnection::where('provider', $provider)->get();
            return [$provider->value => [
                'configured' => (bool) config("social.oauth.{$provider->value}.client_id"),
                'connected' => $connections->where('active', true)->count(),
                'status' => $connections->first()?->status ?? 'not_connected',
            ]];
        }));
    }
}
