<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        foreach ([
            ['name' => 'Women Embroidered', 'slug' => 'women-embroidered', 'sort_order' => 1],
            ['name' => 'Women Printed', 'slug' => 'women-printed', 'sort_order' => 2],
            ['name' => 'Men Unstitched', 'slug' => 'men-unstitched', 'sort_order' => 3],
        ] as $category) {
            $exists = DB::table('categories')
                ->where('slug', $category['slug'])
                ->orWhere('name', $category['name'])
                ->exists();

            if (! $exists) {
                DB::table('categories')->insert([
                    ...$category,
                    'active' => true,
                    'description' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('categories')
            ->whereIn('slug', ['women-embroidered', 'women-printed', 'men-unstitched'])
            ->whereNotExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('products')
                    ->whereColumn('products.category_id', 'categories.id');
            })
            ->delete();
    }
};
