<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_name')->nullable()->change();
            $table->string('customer_phone', 40)->nullable()->change();
            $table->string('shipping_address')->nullable()->change();
            $table->string('shipping_city', 120)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('orders')->whereNull('customer_name')->update(['customer_name' => 'Walk-in customer']);
        DB::table('orders')->whereNull('customer_phone')->update(['customer_phone' => 'N/A']);
        DB::table('orders')->whereNull('shipping_address')->update(['shipping_address' => 'In-store sale']);
        DB::table('orders')->whereNull('shipping_city')->update(['shipping_city' => 'N/A']);

        Schema::table('orders', function (Blueprint $table) {
            $table->string('customer_name')->nullable(false)->change();
            $table->string('customer_phone', 40)->nullable(false)->change();
            $table->string('shipping_address')->nullable(false)->change();
            $table->string('shipping_city', 120)->nullable(false)->change();
        });
    }
};
