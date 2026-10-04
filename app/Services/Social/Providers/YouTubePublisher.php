<?php
namespace App\Services\Social\Providers;
use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use RuntimeException;
class YouTubePublisher implements SocialPublisher {
 public function publish(SocialPost $post,SocialConnection $connection,SocialPostPublication $publication):array {
  throw new RuntimeException('YouTube publishing requires Google OAuth and an authenticated upload-capable channel.');
 }
}
