<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Builder;
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
        DB::table('products')
            ->where(function (Builder $query): void {
                $query->whereNull('sku')
                    ->orWhereRaw('TRIM(sku) = ?', ['']);
            })
            ->orderBy('id')
            ->chunkById(500, function ($products): void {
                foreach ($products as $product) {
                    $sku = 'GCH-LEGACY-'.$product->id;
                    $suffix = 1;

                    while (DB::table('products')->where('sku', $sku)->exists()) {
                        $sku = 'GCH-LEGACY-'.$product->id.'-'.$suffix;
                        $suffix++;
                    }

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['sku' => $sku]);
                }
            });

        Schema::table('products', function (Blueprint $table): void {
            $table->string('sku')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->string('sku')->nullable()->change();
        });
    }
};
