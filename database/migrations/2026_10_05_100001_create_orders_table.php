<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('cod');
            $table->string('payment_status')->default('unpaid');
            $table->string('currency', 3)->default('PKR');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('shipping_amount', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 40);
            $table->string('shipping_address');
            $table->string('shipping_city', 120);
            $table->string('shipping_area', 160)->nullable();
            $table->text('customer_note')->nullable();
            $table->text('admin_note')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('customer_phone');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
