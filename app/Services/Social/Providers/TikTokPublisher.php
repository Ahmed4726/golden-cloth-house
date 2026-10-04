<?php
namespace App\Services\Social\Providers;
use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class TikTokPublisher implements SocialPublisher {
 public function publish(SocialPost $post, SocialConnection $connection, SocialPostPublication $publication): array {
  if(!$connection->access_token) throw new RuntimeException('TikTok connection has no access token.');
  $base=rtrim(config('social.tiktok.api_url'),'/'); $h=['Authorization'=>'Bearer '.$connection->access_token,'Content-Type'=>'application/json'];
  $creator=Http::withHeaders($h)->post("$base/v2/post/publish/creator_info/query/");
  if($creator->failed()||$creator->json('error.code')!=='ok') throw new RuntimeException('TikTok creator info failed: '.$creator->body());
  $privacy=$creator->json('data.privacy_level_options.0'); if(!$privacy) throw new RuntimeException('TikTok returned no privacy level.');
  if($post->media_type==='video'){
   if(!$post->video_url) throw new RuntimeException('TikTok requires a public video URL.');
   $r=Http::withHeaders($h)->post("$base/v2/post/publish/video/init/",['post_info'=>['title'=>$post->caption,'privacy_level'=>$privacy,'brand_organic_toggle'=>true],'source_info'=>['source'=>'PULL_FROM_URL','video_url'=>$post->video_url]]);
  }else{
   $images=$post->media_urls??[]; if(!$images) throw new RuntimeException('TikTok requires public image URLs.');
   $r=Http::withHeaders($h)->post("$base/v2/post/publish/content/init/",['post_info'=>['title'=>$post->caption,'description'=>$post->caption,'privacy_level'=>$privacy,'brand_organic_toggle'=>true],'source_info'=>['source'=>'PULL_FROM_URL','photo_cover_index'=>0,'photo_images'=>array_values($images)],'post_mode'=>'DIRECT_POST','media_type'=>'PHOTO']);
  }
  if($r->failed()||$r->json('error.code')!=='ok') throw new RuntimeException('TikTok publish failed: '.$r->body());
  return ['external_id'=>$r->json('data.publish_id'),'response'=>$r->json()];
 }
}
