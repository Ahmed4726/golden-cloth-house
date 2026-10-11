<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('catalog_imports', function (Blueprint $table) {
            $table->foreignId('generated_brand_id')
                ->nullable()
                ->after('product_count')
                ->constrained('brands')
                ->nullOnDelete();
            $table->foreignId('generated_slide_id')
                ->nullable()
                ->after('generated_brand_id')
                ->constrained('homepage_slides')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalog_imports', function (Blueprint $table) {
            $table->dropForeign(['generated_brand_id']);
            $table->dropForeign(['generated_slide_id']);
            $table->dropColumn(['generated_brand_id', 'generated_slide_id']);
        });
    }
};
