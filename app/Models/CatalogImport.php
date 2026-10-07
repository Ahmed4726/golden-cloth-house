<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class CatalogImport extends Model
{
    protected $fillable = [
        'uuid',
        'name',
        'pdf_path',
        'product_count',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (CatalogImport $import) {
            $import->uuid ??= (string) Str::uuid();
        });
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function presetData(): ?array
    {
        $name = Str::lower($this->name ?? '');

        if (Str::contains($name, 'lime vol-552')) {
            $groups = [
                [2, 3, 4],
                [5, 6, 7],
                [8, 9, 10, 11],
                [12, 13, 14, 15],
                [16, 17, 18],
                [19, 20, 21, 22],
            ];

            return [
                'collection' => 'Lime Vol-552',
                'gender' => 'women',
                'retail_price' => 6200,
                'discount_percent' => 3,
                'fabric' => 'Peach Slub',
                'season' => 'Winter',
                'description' => 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                'groups' => $groups,
                'products' => collect($groups)->values()->map(function (array $pages, int $index) {
                    return [
                        'name' => 'Lime Vol-552 — Design ' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'description' => 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                        'retail_price' => 6200,
                        'discount_percent' => 3,
                        'gender' => 'women',
                        'piece_count' => '',
                        'fabric' => 'Peach Slub',
                        'season' => 'Winter',
                        'design_code' => 'LIME-552-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'stock_quantity' => 3,
                        'featured' => false,
                        'pages' => $pages,
                    ];
                })->all(),
            ];
        }

        if (Str::contains($name, 'leore vol-01')) {
            $groups = [
                [2, 3, 4, 5, 6],
                [7, 8, 9, 10, 11],
                [12, 13, 14, 15, 16],
                [17, 18, 19],
                [20, 21, 22],
                [23, 24],
            ];

            return [
                'collection' => 'Leore Vol-01',
                'gender' => 'women',
                'retail_price' => 7800,
                'discount_percent' => 3,
                'fabric' => 'Fancy Zari Slub Dhanak',
                'season' => 'Winter',
                'piece_count' => 3,
                'description' => 'Premium embroidered fancy zari slub dhanak shirt with fancy zari slub embroidered lace dupatta and comfort of same trouser.',
                'groups' => $groups,
                'products' => collect($groups)->values()->map(function (array $pages, int $index) {
                    return [
                        'name' => 'Leore Vol-01 — Design ' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'description' => 'Premium embroidered fancy zari slub dhanak shirt with fancy zari slub embroidered lace dupatta and comfort of same trouser.',
                        'retail_price' => 7800,
                        'discount_percent' => 3,
                        'gender' => 'women',
                        'piece_count' => 3,
                        'fabric' => 'Fancy Zari Slub Dhanak',
                        'season' => 'Winter',
                        'design_code' => 'LEORE-01-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT),
                        'stock_quantity' => 3,
                        'featured' => false,
                        'pages' => $pages,
                    ];
                })->all(),
            ];
        }

        return null;
    }
}
