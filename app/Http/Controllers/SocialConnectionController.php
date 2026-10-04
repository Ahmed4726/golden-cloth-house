<?php

namespace App\Http\Controllers;

use App\Enums\SocialProvider;
use App\Models\SocialConnection;
use Illuminate\Http\Request;

class SocialConnectionController extends Controller
{
    public function index()
    {
        return view('connections.index', ['connections' => SocialConnection::latest()->get()]);
    }

    public function destroy(SocialConnection $connection)
    {
        $connection->delete();

        return back()->with('success', 'Connection removed.');
    }

    public function toggle(SocialConnection $connection)
    {
        $connection->update(['active' => !$connection->active]);

        return back()->with('success', 'Connection status updated.');
    }

    public function oauth(SocialProvider $provider)
    {
        $config = config("social.oauth.{$provider->value}");
        abort_unless($config['client_id'], 503, ucfirst($provider->value).' OAuth is not configured yet.');

        $state = bin2hex(random_bytes(24));
        session(['social_oauth_state' => $state, 'social_oauth_provider' => $provider->value]);

        $query = http_build_query([
            'client_id' => $config['client_id'],
            'redirect_uri' => route('social.oauth.callback', $provider),
            'response_type' => 'code',
            'scope' => implode(' ', $config['scopes']),
            'state' => $state,
        ]);

        return redirect($config['authorize_url'].'?'.$query);
    }

    public function callback(Request $request, SocialProvider $provider)
    {
        abort_unless(hash_equals((string) session('social_oauth_state'), (string) $request->state), 419, 'Invalid OAuth state.');
        abort_unless(session('social_oauth_provider') === $provider->value, 419, 'OAuth provider mismatch.');

        if ($request->filled('error')) {
            return redirect()->route('connections.index')->withErrors(['oauth' => $request->error_description ?: 'Authorization was cancelled.']);
        }

        $config = config("social.oauth.{$provider->value}");
        $response = \Illuminate\Support\Facades\Http::asForm()->post($config['token_url'], [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'redirect_uri' => route('social.oauth.callback', $provider),
            'grant_type' => 'authorization_code',
            'code' => $request->code,
        ]);

        if ($response->failed()) {
            return redirect()->route('connections.index')->withErrors(['oauth' => 'Token exchange failed: '.$response->body()]);
        }

        $token = $response->json();
        $connection = SocialConnection::updateOrCreate(
            ['provider' => $provider, 'external_id' => $token['open_id'] ?? ($token['channel_id'] ?? null)],
            [
                'name' => ucfirst($provider->value).' account',
                'access_token' => $token['access_token'] ?? null,
                'refresh_token' => $token['refresh_token'] ?? null,
                'token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int) $token['expires_in']) : null,
                'metadata' => $token,
                'active' => true,
                'status' => 'connected',
                'last_error' => null,
            ]
        );

        session()->forget(['social_oauth_state','social_oauth_provider']);

        return redirect()->route('connections.index')->with('success', ucfirst($provider->value).' connected.');
    }
}
