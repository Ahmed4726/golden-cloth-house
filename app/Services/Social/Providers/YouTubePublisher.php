<?php
namespace App\Services\Social\Providers;
use App\Contracts\SocialPublisher;
use App\Models\SocialConnection;
use App\Models\SocialPost;
use App\Models\SocialPostPublication;
use Illuminate\Support\Facades\Http;
use RuntimeException;
class YouTubePublisher implements SocialPublisher {
 public function publish(SocialPost $post, SocialConnection $connection, SocialPostPublication $publication): array {
  if($post->media_type!=='video'||!$post->video_url) throw new RuntimeException('YouTube requires a public video URL.');
  $token=$this->token($connection); $video=Http::timeout(120)->get($post->video_url);
  if($video->failed()) throw new RuntimeException('YouTube could not fetch the video URL.');
  $type=$video->header('Content-Type','video/mp4'); $meta=['snippet'=>['title'=>mb_substr($post->title?:'Golden Cloth House',0,100),'description'=>$post->caption,'categoryId'=>'22'],'status'=>['privacyStatus'=>'public']];
  $init=Http::withToken($token)->withHeaders(['X-Upload-Content-Length'=>strlen($video->body()),'X-Upload-Content-Type'=>$type])->withBody(json_encode($meta),'application/json; charset=UTF-8')->post('https://www.googleapis.com/upload/youtube/v3/videos?uploadType=resumable&part=snippet,status');
  if($init->failed()||!$init->header('Location')) throw new RuntimeException('YouTube upload session failed: '.$init->body());
  $upload=Http::withToken($token)->withBody($video->body(),$type)->put($init->header('Location'));
  if($upload->failed()) throw new RuntimeException('YouTube upload failed: '.$upload->body());
  return ['external_id'=>$upload->json('id'),'response'=>$upload->json()];
 }
 private function token(SocialConnection $c): string {
  if($c->token_expires_at?->isFuture()) return $c->access_token;
  if(!$c->refresh_token) throw new RuntimeException('YouTube token expired and no refresh token is available.');
  $cfg=config('social.oauth.youtube'); $r=Http::asForm()->post($cfg['token_url'],['client_id'=>$cfg['client_id'],'client_secret'=>$cfg['client_secret'],'refresh_token'=>$c->refresh_token,'grant_type'=>'refresh_token']);
  if($r->failed()) throw new RuntimeException('YouTube token refresh failed: '.$r->body());
  $c->update(['access_token'=>$r->json('access_token'),'token_expires_at'=>now()->addSeconds((int)$r->json('expires_in',3600))]); return $r->json('access_token');
 }
}
