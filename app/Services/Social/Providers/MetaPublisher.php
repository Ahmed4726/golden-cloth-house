<?php
namespace App\Services\Social\Providers;
use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class MetaPublisher implements SocialPublisher {
 public function publish(SocialPost $post, SocialConnection $connection, SocialPostPublication $publication): array {
  $token=$connection->access_token; $id=$connection->external_id;
  if(!$token||!$id) throw new RuntimeException('Meta connection is missing its token or account ID.');
  if($post->media_type!=='image') throw new RuntimeException('Meta video/Reels publishing needs a dedicated video workflow; image publishing is enabled.');
  $images=$post->media_urls??[]; if(!$images) throw new RuntimeException('At least one public image URL is required.');
  $base=rtrim(config('social.meta.graph_url'),'/');
  if($connection->provider->value==='facebook'){
   $results=[]; foreach($images as $url){$r=Http::asForm()->post("$base/$id/photos",['url'=>$url,'caption'=>$post->caption,'access_token'=>$token]); if($r->failed()) throw new RuntimeException('Facebook publish failed: '.$r->body()); $results[]=$r->json();}
   return ['external_id'=>$results[0]['post_id']??$results[0]['id']??null,'responses'=>$results];
  }
  if($connection->provider->value==='instagram'){
   $r=Http::asForm()->post("$base/$id/media",['image_url'=>$images[0],'caption'=>$post->caption,'access_token'=>$token]); if($r->failed()) throw new RuntimeException('Instagram media creation failed: '.$r->body());
   $creation=$r->json('id'); $r=Http::asForm()->post("$base/$id/media_publish",['creation_id'=>$creation,'access_token'=>$token]); if($r->failed()) throw new RuntimeException('Instagram publish failed: '.$r->body());
   return ['external_id'=>$r->json('id'),'creation_id'=>$creation,'response'=>$r->json()];
  }
  throw new RuntimeException('Unsupported Meta provider.');
 }
}
