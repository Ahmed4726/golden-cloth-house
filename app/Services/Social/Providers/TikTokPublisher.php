<?php
namespace App\Services\Social\Providers;
use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use RuntimeException;
class TikTokPublisher implements SocialPublisher {
 public function publish(SocialPost $post,SocialConnection $connection,SocialPostPublication $publication):array {
  throw new RuntimeException('TikTok publishing requires an approved Content Posting API client and configured media.');
 }
}
