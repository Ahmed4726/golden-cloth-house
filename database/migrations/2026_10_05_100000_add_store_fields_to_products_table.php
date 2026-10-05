<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedInteger('stock_quantity')->default(0)->after('price');
            $table->decimal('compare_at_price', 12, 2)->nullable()->after('stock_quantity');
            $table->boolean('featured')->default(false)->after('active');
            $table->unsignedInteger('sort_order')->default(0)->after('featured');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'stock_quantity',
                'compare_at_price',
                'featured',
                'sort_order',
            ]);
        });
    }
};
