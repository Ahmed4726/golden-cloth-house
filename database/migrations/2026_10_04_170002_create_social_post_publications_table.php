<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('social_post_publications', function (Blueprint $table) {
    $table->id();

    $table->foreignId('social_post_id')
        ->constrained('social_posts')
        ->cascadeOnDelete();

    $table->foreignId('social_connection_id')
        ->constrained('social_connections')
        ->cascadeOnDelete();

    $table->string('provider');
    $table->string('status')->default('queued');
    $table->string('external_id')->nullable();
    $table->text('error')->nullable();
    $table->json('response')->nullable();
    $table->unsignedInteger('attempts')->default(0);
    $table->timestamp('published_at')->nullable();

    $table->unique(
        ['social_post_id', 'social_connection_id'],
        'social_post_conn_unique'
    );

    $table->timestamps();
});
}};
