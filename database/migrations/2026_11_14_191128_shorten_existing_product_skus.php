<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $temporaryPrefix = 'TMP-'.Str::upper(Str::random(12)).'-';

        while (DB::table('products')->where('sku', 'like', $temporaryPrefix.'%')->exists()) {
            $temporaryPrefix = 'TMP-'.Str::upper(Str::random(12)).'-';
        }

        DB::table('products')
            ->whereRaw('LENGTH(sku) > 20')
            ->orderBy('id')
            ->chunkById(500, function ($products) use ($temporaryPrefix): void {
                foreach ($products as $product) {
                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['sku' => $temporaryPrefix.$product->id]);
                }
            });

        DB::table('products')
            ->where('sku', 'like', $temporaryPrefix.'%')
            ->orderBy('id')
            ->chunkById(500, function ($products): void {
                foreach ($products as $product) {
                    $sku = 'GCH-'.str_pad((string) $product->id, 5, '0', STR_PAD_LEFT);
                    $suffix = 1;

                    while (DB::table('products')->where('sku', $sku)->exists()) {
                        $sku = 'GCH-'.str_pad((string) $product->id, 5, '0', STR_PAD_LEFT).'-'.$suffix++;
                    }

                    DB::table('products')
                        ->where('id', $product->id)
                        ->update(['sku' => $sku]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Existing SKUs are intentionally kept short; their former values cannot be reconstructed.
    }
};
