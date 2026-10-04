<?php
namespace App\Console\Commands;

use App\Enums\SocialProvider;
use App\Models\SocialPostPublication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CheckTikTokPublicationStatus extends Command
{
 protected $signature='social:check-tiktok';
 protected $description='Check pending TikTok publication status.';
 public function handle(): int {
  $base=rtrim(config('social.tiktok.api_url'),'/');
  SocialPostPublication::with('connection')->where('provider',SocialProvider::TikTok)->where('status','processing')->whereNotNull('external_id')->chunkById(50,function($items)use($base){
   foreach($items as $p){
    $r=Http::withToken($p->connection->access_token)->post("$base/v2/post/publish/status/fetch/",['publish_id'=>$p->external_id]);
    if($r->failed()||$r->json('error.code')!=='ok') continue;
    $status=$r->json('data.status');
    if($status==='PUBLISH_COMPLETE'){
     $p->update(['status'=>'published','published_at'=>now(),'response'=>array_merge($p->response??[],['status_check'=>$r->json()])]);
     $post=$p->post()->with('publications')->first();
     if($post && $post->publications->every(fn($x)=>$x->status==='published')) $post->update(['status'=>'published','published_at'=>now()]);
    } elseif($status==='FAILED'){
     $reason=$r->json('data.fail_reason','TikTok reported a publishing failure.');
     $p->update(['status'=>'failed','error'=>$reason,'response'=>array_merge($p->response??[],['status_check'=>$r->json()])]);
     $p->connection->update(['status'=>'error','last_error'=>$reason]);
    }
   }
  });
  return self::SUCCESS;
 }
}