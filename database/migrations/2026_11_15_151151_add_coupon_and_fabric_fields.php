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
        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('kameez_fabric_id')->nullable()->constrained('fabrics')->nullOnDelete();
            $table->foreignId('trouser_fabric_id')->nullable()->constrained('fabrics')->nullOnDelete();
            $table->foreignId('dupatta_fabric_id')->nullable()->constrained('fabrics')->nullOnDelete();
        });

        Schema::table('orders', function (Blueprint $table): void {
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->string('coupon_code', 40)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn(['coupon_id', 'coupon_code']);
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropForeign(['kameez_fabric_id']);
            $table->dropForeign(['trouser_fabric_id']);
            $table->dropForeign(['dupatta_fabric_id']);
            $table->dropColumn(['kameez_fabric_id', 'trouser_fabric_id', 'dupatta_fabric_id']);
        });
    }
};
