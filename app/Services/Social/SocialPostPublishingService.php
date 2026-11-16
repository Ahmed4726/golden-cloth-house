<?php

namespace App\Services\Social;

use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use Throwable;

class SocialPostPublishingService
{
    public function __construct(private PublisherManager $manager) {}

    public function publish(SocialPostPublication $publication): bool
    {
        $publication->loadMissing(['post', 'connection']);
        $publication->increment('attempts');
        $publication->update(['status' => 'processing', 'error' => null]);

        try {
            $result = $this->manager
                ->driver($publication->connection)
                ->publish($publication->post, $publication->connection, $publication);
        } catch (Throwable $exception) {
            $error = mb_substr($exception->getMessage(), 0, 5000);

            $publication->update(['status' => 'failed', 'error' => $error]);
            $publication->connection->update(['status' => 'error', 'last_error' => $error]);
            $this->updatePostStatus($publication->post);

            return false;
        }

        $isPending = $result['pending'] ?? false;

        $publication->update([
            'status' => $isPending ? 'processing' : 'published',
            'external_id' => $result['external_id'] ?? null,
            'response' => $result,
            'published_at' => $isPending ? null : now(),
        ]);
        $publication->connection->update([
            'status' => 'connected',
            'last_error' => null,
            'active' => true,
        ]);
        $this->updatePostStatus($publication->post);

        return true;
    }

    private function updatePostStatus(SocialPost $post): void
    {
        $statuses = $post->publications()->pluck('status');

        if ($statuses->isNotEmpty() && $statuses->every(fn (string $status): bool => $status === 'published')) {
            $post->update(['status' => 'published', 'published_at' => now()]);

            return;
        }

        $status = $statuses->contains('failed')
            ? 'failed'
            : ($statuses->contains('processing') ? 'processing' : 'queued');

        $post->update(['status' => $status, 'published_at' => null]);
    }
}
