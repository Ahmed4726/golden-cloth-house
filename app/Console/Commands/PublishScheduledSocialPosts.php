<?php

namespace App\Console\Commands;

use App\Models\SocialPost;
use App\Services\Social\SocialPostPublishingService;
use Illuminate\Console\Command;

class PublishScheduledSocialPosts extends Command
{
    protected $signature = 'social:publish-scheduled';

    protected $description = 'Publish due scheduled social publications.';

    public function handle(SocialPostPublishingService $publisher): int
    {
        $published = 0;
        $failed = 0;

        SocialPost::query()
            ->where('status', 'scheduled')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->chunkById(100, function ($posts) use ($publisher, &$published, &$failed): void {
                foreach ($posts as $post) {
                    $post->update(['status' => 'queued']);

                    foreach ($post->publications()->where('status', 'queued')->get() as $publication) {
                        if ($publisher->publish($publication)) {
                            $published++;
                        } else {
                            $failed++;
                        }
                    }
                }
            });

        $this->info("Published or accepted {$published} publication(s).");

        if ($failed > 0) {
            $this->warn("Failed to publish {$failed} publication(s). Review their saved error details.");
        }

        return self::SUCCESS;
    }
}
