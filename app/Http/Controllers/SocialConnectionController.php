<?php

namespace App\Http\Controllers;

use App\Enums\SocialProvider;
use App\Models\SocialConnection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SocialConnectionController extends Controller
{
    public function index(Request $request)
    {
        $providerValues = array_map(fn (SocialProvider $provider) => $provider->value, SocialProvider::cases());
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'provider' => ['nullable', 'in:'.implode(',', $providerValues)],
            'active' => ['nullable', 'in:0,1'],
        ]);
        $search = mb_strtolower(trim($filters['q'] ?? ''));
        $connections = SocialConnection::latest()->get()
            ->unique(fn (SocialConnection $connection) => $connection->provider->value)
            ->keyBy(fn (SocialConnection $connection) => $connection->provider->value);
        $providers = collect(SocialProvider::cases())
            ->when($filters['provider'] ?? null, fn ($providers, $provider) => $providers->where('value', $provider))
            ->filter(function (SocialProvider $provider) use ($connections, $filters, $search) {
                $connection = $connections->get($provider->value);

                if (isset($filters['active']) && (int) ($connection?->active ?? false) !== (int) $filters['active']) {
                    return false;
                }

                return $search === ''
                    || str_contains(mb_strtolower($provider->value), $search)
                    || str_contains(mb_strtolower($connection?->name ?? ''), $search);
            })
            ->values();

        return view('connections.index', compact('connections', 'providers'));
    }

    public function destroy(SocialConnection $connection)
    {
        $connection->delete();

        return back()->with('success', 'Connection removed.');
    }

    public function toggle(SocialConnection $connection)
    {
        $connection->update(['active' => ! $connection->active]);

        return back()->with('success', 'Connection status updated.');
    }

    public function oauth(SocialProvider $provider)
    {
        $config = config("social.oauth.{$provider->value}");
        abort_unless($config['client_id'], 503, ucfirst($provider->value).' OAuth is not configured.');

        $state = bin2hex(random_bytes(24));
        session(['social_oauth_state' => $state, 'social_oauth_provider' => $provider->value]);

        $params = [
            'redirect_uri' => route('social.oauth.callback', $provider),
            'response_type' => 'code',
            'scope' => implode(' ', $config['scopes']),
            'state' => $state,
        ];
        $params[$provider === SocialProvider::TikTok ? 'client_key' : 'client_id'] = $config['client_id'];

        if ($provider === SocialProvider::YouTube) {
            $params['access_type'] = 'offline';
            $params['prompt'] = 'consent';
        }

        return redirect($config['authorize_url'].'?'.http_build_query($params));
    }

    public function callback(Request $request, SocialProvider $provider)
    {
        abort_unless(hash_equals((string) session('social_oauth_state'), (string) $request->state), 419, 'Invalid OAuth state.');
        abort_unless(session('social_oauth_provider') === $provider->value, 419, 'OAuth provider mismatch.');

        if ($request->filled('error')) {
            return redirect()->route('connections.index')->withErrors(['oauth' => $request->error_description ?: 'Authorization was cancelled.']);
        }

        $config = config("social.oauth.{$provider->value}");
        $payload = [
            'client_secret' => $config['client_secret'],
            'redirect_uri' => route('social.oauth.callback', $provider),
            'grant_type' => 'authorization_code',
            'code' => $request->code,
        ];
        $payload[$provider === SocialProvider::TikTok ? 'client_key' : 'client_id'] = $config['client_id'];

        $response = Http::asForm()->post($config['token_url'], $payload);
        if ($response->failed()) {
            return redirect()->route('connections.index')->withErrors(['oauth' => 'Token exchange failed: '.$response->body()]);
        }

        $token = $response->json();

        try {
            match ($provider) {
                SocialProvider::Facebook, SocialProvider::Instagram => $this->saveMetaConnections($token, $provider),
                SocialProvider::TikTok => $this->saveTikTokConnection($token),
                SocialProvider::YouTube => $this->saveYouTubeConnection($token),
            };
        } catch (\Throwable $e) {
            return redirect()->route('connections.index')->withErrors(['oauth' => $e->getMessage()]);
        }

        session()->forget(['social_oauth_state', 'social_oauth_provider']);

        return redirect()->route('connections.index')->with('success', ucfirst($provider->value).' connection completed.');
    }

    private function saveMetaConnections(array $token, SocialProvider $requested): void
    {
        $userToken = $token['access_token'] ?? null;
        if (! $userToken) {
            throw new \RuntimeException('Meta did not return an access token.');
        }

        $graph = rtrim(config('social.meta.graph_url'), '/');
        $pages = Http::get($graph.'/me/accounts', [
            'fields' => 'id,name,access_token,instagram_business_account',
            'access_token' => $userToken,
        ]);

        if ($pages->failed()) {
            throw new \RuntimeException('Meta could not list Pages: '.$pages->body());
        }
        $data = $pages->json('data', []);
        if (! $data) {
            throw new \RuntimeException('No Facebook Pages were returned. Make sure the authorized account manages a Page.');
        }

        foreach ($data as $page) {
            if ($requested === SocialProvider::Facebook) {
                SocialConnection::updateOrCreate(
                    ['provider' => SocialProvider::Facebook, 'external_id' => $page['id']],
                    ['name' => $page['name'].' (Facebook Page)', 'access_token' => $page['access_token'], 'metadata' => $page, 'active' => true, 'status' => 'connected', 'last_error' => null]
                );
            }

            if (! empty($page['instagram_business_account']['id'])) {
                $igId = $page['instagram_business_account']['id'];
                SocialConnection::updateOrCreate(
                    ['provider' => SocialProvider::Instagram, 'external_id' => $igId],
                    ['name' => $page['name'].' (Instagram)', 'access_token' => $page['access_token'], 'metadata' => ['page_id' => $page['id'], 'instagram_business_account' => $page['instagram_business_account']], 'active' => true, 'status' => 'connected', 'last_error' => null]
                );
            }
        }
    }

    private function saveTikTokConnection(array $token): void
    {
        if (empty($token['access_token']) || empty($token['open_id'])) {
            throw new \RuntimeException('TikTok did not return the access token/open ID.');
        }
        SocialConnection::updateOrCreate(
            ['provider' => SocialProvider::TikTok, 'external_id' => $token['open_id']],
            ['name' => 'TikTok account '.$token['open_id'], 'access_token' => $token['access_token'], 'refresh_token' => $token['refresh_token'] ?? null, 'token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int) $token['expires_in']) : null, 'metadata' => $token, 'active' => true, 'status' => 'connected', 'last_error' => null]
        );
    }

    private function saveYouTubeConnection(array $token): void
    {
        if (empty($token['access_token'])) {
            throw new \RuntimeException('Google did not return an access token.');
        }
        $channels = Http::withToken($token['access_token'])->get('https://www.googleapis.com/youtube/v3/channels', ['part' => 'snippet,contentDetails', 'mine' => 'true']);
        if ($channels->failed() || empty($channels->json('items.0.id'))) {
            throw new \RuntimeException('Could not identify the authorized YouTube channel: '.$channels->body());
        }

        $channel = $channels->json('items.0');
        SocialConnection::updateOrCreate(
            ['provider' => SocialProvider::YouTube, 'external_id' => $channel['id']],
            ['name' => $channel['snippet']['title'].' (YouTube)', 'access_token' => $token['access_token'], 'refresh_token' => $token['refresh_token'] ?? null, 'token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int) $token['expires_in']) : null, 'metadata' => $channel, 'active' => true, 'status' => 'connected', 'last_error' => null]
        );
    }
}
