<?php

namespace App\Services\Social\Providers;

use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MetaPublisher implements SocialPublisher
{
    public function publish(SocialPost $post, SocialConnection $connection, SocialPostPublication $publication): array
    {
        $token = $connection->access_token;
        $id = $connection->external_id;
        if (! $token || ! $id) {
            throw new RuntimeException('Meta connection is missing its token or account ID.');
        }
        if ($post->media_type !== 'image') {
            throw new RuntimeException('Meta video/Reels publishing needs a dedicated video workflow; image publishing is enabled.');
        }
        $images = $post->media_urls ?? [];
        if (! $images) {
            throw new RuntimeException('At least one public image URL is required.');
        }
        $base = rtrim(config('social.meta.graph_url'), '/');
        if ($connection->provider->value === 'facebook') {
            if (count($images) === 1) {
                $r = Http::asForm()->post("$base/$id/photos", ['url' => $images[0], 'caption' => $post->caption, 'access_token' => $token]);
                if ($r->failed()) {
                    throw new RuntimeException('Facebook publish failed: '.$r->body());
                }

                return ['external_id' => $r->json('post_id') ?? $r->json('id'), 'response' => $r->json()];
            }
            $mediaIds = [];
            foreach ($images as $url) {
                $r = Http::asForm()->post("$base/$id/photos", ['url' => $url, 'published' => false, 'access_token' => $token]);
                if ($r->failed()) {
                    throw new RuntimeException('Facebook image upload failed: '.$r->body());
                }
                $mediaId = $r->json('id');
                if (! $mediaId) {
                    throw new RuntimeException('Facebook image upload returned no media ID.');
                }
                $mediaIds[] = $mediaId;
            }
            $attachedMedia = [];
            foreach ($mediaIds as $index => $mediaId) {
                $attachedMedia["attached_media[$index]"] = json_encode(['media_fbid' => $mediaId], JSON_THROW_ON_ERROR);
            }
            $r = Http::asForm()->post("$base/$id/feed", [
                ...$attachedMedia,
                'message' => $post->caption,
                'access_token' => $token,
            ]);
            if ($r->failed()) {
                throw new RuntimeException('Facebook publish failed: '.$r->body());
            }

            return ['external_id' => $r->json('id'), 'media_ids' => $mediaIds, 'response' => $r->json()];
        }
        if ($connection->provider->value === 'instagram') {
            if (count($images) > 10) {
                throw new RuntimeException('Instagram carousel posts cannot contain more than 10 images.');
            }

            if (count($images) === 1) {
                $r = Http::asForm()->post("$base/$id/media", [
                    'image_url' => $images[0],
                    'caption' => $post->caption,
                    'access_token' => $token,
                ]);
            } else {
                $children = [];
                foreach ($images as $image) {
                    $r = Http::asForm()->post("$base/$id/media", [
                        'image_url' => $image,
                        'is_carousel_item' => true,
                        'access_token' => $token,
                    ]);
                    if ($r->failed()) {
                        throw new RuntimeException('Instagram carousel image creation failed: '.$r->body());
                    }
                    $child = $r->json('id');
                    if (! $child) {
                        throw new RuntimeException('Instagram carousel image creation returned no container ID.');
                    }
                    $children[] = $child;
                }

                $r = Http::asForm()->post("$base/$id/media", [
                    'media_type' => 'CAROUSEL',
                    'children' => implode(',', $children),
                    'caption' => $post->caption,
                    'access_token' => $token,
                ]);
            }
            if ($r->failed()) {
                throw new RuntimeException('Instagram media creation failed: '.$r->body());
            }
            $creation = $r->json('id');
            if (! $creation) {
                throw new RuntimeException('Instagram media creation returned no container ID.');
            }
            $r = Http::asForm()->post("$base/$id/media_publish", ['creation_id' => $creation, 'access_token' => $token]);
            if ($r->failed()) {
                throw new RuntimeException('Instagram publish failed: '.$r->body());
            }

            return ['external_id' => $r->json('id'), 'creation_id' => $creation, 'response' => $r->json()];
        }
        throw new RuntimeException('Unsupported Meta provider.');
    }
}
