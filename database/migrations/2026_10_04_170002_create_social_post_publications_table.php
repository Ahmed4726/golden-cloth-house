<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('social_post_publications',function(Blueprint $t){$t->id();$t->foreignId('social_post_id')->constrained()->cascadeOnDelete();$t->foreignId('social_connection_id')->constrained()->cascadeOnDelete();$t->string('provider',30);$t->string('status',30)->default('queued');$t->string('external_id')->nullable();$t->text('error')->nullable();$t->json('response')->nullable();$t->unsignedInteger('attempts')->default(0);$t->timestamp('published_at')->nullable();$t->timestamps();$t->unique(['social_post_id','social_connection_id']);});} public function down():void{Schema::dropIfExists('social_post_publications');}};
