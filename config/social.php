<?php

use App\Services\Social\Providers\MetaPublisher;
use App\Services\Social\Providers\TikTokPublisher;
use App\Services\Social\Providers\YouTubePublisher;

return [
    'publishers' => [
        'facebook' => MetaPublisher::class,
        'instagram' => MetaPublisher::class,
        'tiktok' => TikTokPublisher::class,
        'youtube' => YouTubePublisher::class,
    ],
    'meta' => ['graph_url' => env('META_GRAPH_URL', 'https://graph.facebook.com')],
    'tiktok' => ['api_url' => env('TIKTOK_API_URL', 'https://open.tiktokapis.com')],
    'youtube' => ['api_url' => env('YOUTUBE_API_URL', 'https://www.googleapis.com')],
    'oauth' => [
        'facebook' => [
            'client_id' => env('META_APP_ID'), 'client_secret' => env('META_APP_SECRET'),
            'authorize_url' => env('META_AUTHORIZE_URL', 'https://www.facebook.com/dialog/oauth'),
            'token_url' => env('META_TOKEN_URL', 'https://graph.facebook.com/oauth/access_token'),
            'scopes' => ['pages_show_list','pages_read_engagement','pages_manage_posts'],
        ],
        'instagram' => [
            'client_id' => env('META_APP_ID'), 'client_secret' => env('META_APP_SECRET'),
            'authorize_url' => env('META_AUTHORIZE_URL', 'https://www.facebook.com/dialog/oauth'),
            'token_url' => env('META_TOKEN_URL', 'https://graph.facebook.com/oauth/access_token'),
            'scopes' => ['instagram_basic','instagram_content_publish','pages_show_list'],
        ],
        'tiktok' => [
            'client_id' => env('TIKTOK_CLIENT_KEY'), 'client_secret' => env('TIKTOK_CLIENT_SECRET'),
            'authorize_url' => env('TIKTOK_AUTHORIZE_URL', 'https://www.tiktok.com/v2/auth/authorize/'),
            'token_url' => env('TIKTOK_TOKEN_URL', 'https://open.tiktokapis.com/v2/oauth/token/'),
            'scopes' => ['user.info.basic','video.publish','video.upload'],
        ],
        'youtube' => [
            'client_id' => env('GOOGLE_CLIENT_ID'), 'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'authorize_url' => env('GOOGLE_AUTHORIZE_URL', 'https://accounts.google.com/o/oauth2/v2/auth'),
            'token_url' => env('GOOGLE_TOKEN_URL', 'https://oauth2.googleapis.com/token'),
            'scopes' => ['https://www.googleapis.com/auth/youtube.upload','https://www.googleapis.com/auth/youtube.readonly'],
        ],
    ],
];
