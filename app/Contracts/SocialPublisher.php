<?php
namespace App\Contracts;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
interface SocialPublisher {
 public function publish(SocialPost $post,SocialConnection $connection,SocialPostPublication $publication):array;
}
