<?php
namespace Tests\Feature;
use App\Enums\SocialProvider;
use App\Jobs\PublishSocialPost;
use App\Models\SocialConnection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
class SocialCommerceTest extends TestCase {
 use RefreshDatabase;
 public function test_public_shop_is_accessible():void{$this->get('/')->assertOk()->assertSee('Golden Cloth House');}
 public function test_admin_dashboard_is_protected():void{$this->get('/admin')->assertRedirect('/login');}
 public function test_admin_can_open_dashboard():void{$u=User::factory()->create();$this->withSession(['admin_user_id'=>$u->id])->get('/admin')->assertOk()->assertSee('Dashboard');}
 public function test_admin_can_queue_post():void{Queue::fake();$u=User::factory()->create();$c=SocialConnection::create(['provider'=>SocialProvider::Facebook,'name'=>'Test Page','external_id'=>'123','access_token'=>'token','active'=>true,'status'=>'connected']);$this->withSession(['admin_user_id'=>$u->id])->post('/admin/posts',['caption'=>'New collection','media_type'=>'image','media_urls'=>'https://example.com/shirt.jpg','connections'=>[$c->id],'action'=>'publish'])->assertRedirect();Queue::assertPushed(PublishSocialPost::class);$this->assertDatabaseHas('social_posts',['caption'=>'New collection','status'=>'queued']);}
 public function test_readiness_endpoint_has_all_providers():void{$u=User::factory()->create();$this->withSession(['admin_user_id'=>$u->id])->get('/admin/api/readiness')->assertOk()->assertJsonStructure(['facebook','instagram','tiktok','youtube']);}
}