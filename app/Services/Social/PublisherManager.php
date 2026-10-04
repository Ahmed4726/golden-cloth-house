<?php
namespace App\Services\Social;
use App\Contracts\SocialPublisher;
use App\Exceptions\UnsupportedSocialProvider;
use App\Models\SocialConnection;
class PublisherManager {
 public function driver(SocialConnection $connection):SocialPublisher {
  $class=config('social.publishers.'.$connection->provider->value);
  if(!$class) throw new UnsupportedSocialProvider("Provider [{$connection->provider->value}] is not configured.");
  return app($class);
 }
}
