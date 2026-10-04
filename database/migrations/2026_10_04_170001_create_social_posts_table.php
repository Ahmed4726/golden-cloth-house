<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('social_posts',function(Blueprint $t){$t->id();$t->string('title')->nullable();$t->text('caption')->nullable();$t->string('media_type',20)->default('image');$t->json('media_urls')->nullable();$t->text('video_url')->nullable();$t->string('status',30)->default('draft');$t->timestamp('scheduled_at')->nullable();$t->timestamp('published_at')->nullable();$t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();$t->timestamps();});} public function down():void{Schema::dropIfExists('social_posts');}};
