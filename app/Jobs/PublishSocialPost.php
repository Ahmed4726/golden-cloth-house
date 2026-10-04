<?php

namespace App\Jobs;

use App\Models\SocialPostPublication;
use App\Services\Social\PublisherManager;
use Illuminate\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class PublishSocialPost implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $publicationId) {}

    public function handle(PublisherManager $manager): void
    {
        $p = SocialPostPublication::with(['post','connection'])->findOrFail($this->publicationId);
        $p->increment('attempts');
        $p->update(['status' => 'processing', 'error' => null]);

        try {
            $result = $manager->driver($p->connection)->publish($p->post, $p->connection, $p);
            $p->update([
                'status' => 'published',
                'external_id' => $result['external_id'] ?? null,
                'response' => $result,
                'published_at' => now(),
            ]);

            $post = $p->post->fresh()->load('publications');
            if ($post->publications->every(fn ($publication) => $publication->status === 'published')) {
                $post->update(['status' => 'published', 'published_at' => now()]);
            }
        } catch (\Throwable $e) {
            $p->update(['status' => 'failed', 'error' => $e->getMessage()]);
            throw $e;
        }
    }
}
