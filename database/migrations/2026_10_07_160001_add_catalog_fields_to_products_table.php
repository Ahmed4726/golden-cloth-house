<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('gender', 20)->nullable()->after('brand_id');
            $table->string('wear_type', 30)->default('unstitched')->after('gender');
            $table->unsignedTinyInteger('piece_count')->nullable()->after('wear_type');
            $table->string('fabric', 100)->nullable()->after('piece_count');
            $table->string('season', 50)->nullable()->after('fabric');
            $table->string('design_code', 100)->nullable()->after('season');
            $table->decimal('discount_percent', 5, 2)->default(0)->after('compare_at_price');
            $table->foreignId('catalog_import_id')->nullable()->after('discount_percent')->constrained('catalog_imports')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['catalog_import_id']);
            $table->dropColumn([
                'gender',
                'wear_type',
                'piece_count',
                'fabric',
                'season',
                'design_code',
                'discount_percent',
                'catalog_import_id',
            ]);
        });
    }
};
