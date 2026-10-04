<?php
use App\Services\Social\Providers\MetaPublisher;
use App\Services\Social\Providers\TikTokPublisher;
use App\Services\Social\Providers\YouTubePublisher;
return [
 'publishers'=>['facebook'=>MetaPublisher::class,'instagram'=>MetaPublisher::class,'tiktok'=>TikTokPublisher::class,'youtube'=>YouTubePublisher::class],
 'meta'=>['graph_url'=>env('META_GRAPH_URL','https://graph.facebook.com')],
 'tiktok'=>['api_url'=>env('TIKTOK_API_URL','https://open.tiktokapis.com')],
 'youtube'=>['api_url'=>env('YOUTUBE_API_URL','https://www.googleapis.com')],
];
