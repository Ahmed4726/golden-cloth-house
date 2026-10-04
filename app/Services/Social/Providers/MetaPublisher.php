<?php
namespace App\Services\Social\Providers;
use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use RuntimeException;
class MetaPublisher implements SocialPublisher {
 public function publish(SocialPost $post,SocialConnection $connection,SocialPostPublication $publication):array {
  throw new RuntimeException('Meta publishing is not enabled until the required OAuth credentials and approved scopes are configured.');
 }
}
