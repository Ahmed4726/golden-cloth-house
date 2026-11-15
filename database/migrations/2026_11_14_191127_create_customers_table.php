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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('identity_key')->unique();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 40)->nullable();
            $table->string('phone_normalized', 40)->nullable();
            $table->string('customer_email')->nullable();
            $table->string('email_normalized')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->index(['customer_id', 'created_at']);
        });

        DB::table('orders')
            ->where(function ($query): void {
                $query->whereNotNull('customer_phone')
                    ->orWhereNotNull('customer_email');
            })
            ->orderBy('id')
            ->chunkById(500, function ($orders): void {
                foreach ($orders as $order) {
                    $phone = preg_replace('/\D+/', '', trim((string) $order->customer_phone)) ?? '';
                    $email = mb_strtolower(trim((string) $order->customer_email));

                    if ($phone === '' && $email === '') {
                        continue;
                    }

                    $identityKey = $phone !== '' ? 'phone:'.$phone : 'email:'.$email;
                    $customer = DB::table('customers')
                        ->where('identity_key', $identityKey)
                        ->first();

                    $attributes = [
                        'customer_name' => $order->customer_name,
                        'customer_phone' => $order->customer_phone,
                        'phone_normalized' => $phone !== '' ? $phone : null,
                        'customer_email' => $order->customer_email,
                        'email_normalized' => $email !== '' ? $email : null,
                        'updated_at' => $order->updated_at,
                    ];

                    if ($customer) {
                        DB::table('customers')
                            ->where('id', $customer->id)
                            ->update($attributes);
                        $customerId = $customer->id;
                    } else {
                        $customerId = DB::table('customers')->insertGetId([
                            'identity_key' => $identityKey,
                            ...$attributes,
                            'created_at' => $order->created_at,
                        ]);
                    }

                    DB::table('orders')
                        ->where('id', $order->id)
                        ->update(['customer_id' => $customerId]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['customer_id']);
            $table->dropIndex(['customer_id', 'created_at']);
            $table->dropColumn('customer_id');
        });

        Schema::dropIfExists('customers');
    }
};
