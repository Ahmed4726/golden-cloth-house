<?php
namespace App\Console\Commands;
use App\Jobs\PublishSocialPost;
use App\Models\SocialPost;
use Illuminate\Console\Command;
class PublishScheduledSocialPosts extends Command {
 protected $signature='social:publish-scheduled'; protected $description='Queue due scheduled social publications.';
 public function handle():int{
  $count=0; SocialPost::with('publications')->where('status','scheduled')->whereNotNull('scheduled_at')->where('scheduled_at','<=',now())->chunkById(100,function($posts)use(&$count){foreach($posts as $post){foreach($post->publications()->where('status','queued')->get() as $pub){PublishSocialPost::dispatch($pub->id);$count++;}$post->update(['status'=>'queued']);}});
  $this->info("Queued {$count} publication(s)."); return self::SUCCESS;
 }
}
