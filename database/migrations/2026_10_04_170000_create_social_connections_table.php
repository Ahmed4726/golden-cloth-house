<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('social_connections',function(Blueprint $t){$t->id();$t->string('provider',30);$t->string('name');$t->string('external_id')->nullable();$t->text('access_token')->nullable();$t->text('refresh_token')->nullable();$t->timestamp('token_expires_at')->nullable();$t->json('metadata')->nullable();$t->boolean('active')->default(false);$t->string('status',30)->default('pending');$t->text('last_error')->nullable();$t->timestamps();$t->index(['provider','active']);});} public function down():void{Schema::dropIfExists('social_connections');}};
