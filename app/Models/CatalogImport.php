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

    /**
     * Return known catalog metadata used to pre-populate the product builder.
     *
     * Unknown catalogs return null and remain fully editable through the
     * manual builder controls.
     */
    public function presetData(): ?array
    {
        $name = Str::lower($this->name ?? '');

        if (Str::contains($name, 'lime vol-552')) {
            return [
                'collection' => 'Lime Vol-552',
                'gender' => 'women',
                'retail_price' => 6200,
                'discount_percent' => 3,
                'fabric' => 'Peach Slub',
                'season' => 'Winter',
                'description' => 'Peach Slub Embroidered with Peach Slub Embroidered Dupatta.',
                'groups' => [
                    [2, 3, 4],
                    [5, 6, 7],
                    [8, 9, 10, 11],
                    [12, 13, 14, 15],
                    [16, 17, 18],
                    [19, 20, 21, 22],
                ],
            ];
        }

        if (Str::contains($name, 'leore vol-01')) {
            return [
                'collection' => 'Leore Vol-01',
                'gender' => 'women',
                'retail_price' => 7800,
                'discount_percent' => 3,
                'fabric' => 'Fancy Zari Slub Dhanak',
                'season' => 'Winter',
                'piece_count' => 3,
                'description' => 'Premium embroidered fancy zari slub dhanak shirt with fancy zari slub embroidered lace dupatta and comfort of same trouser.',
                'groups' => [
                    [2, 3, 4, 5, 6],
                    [7, 8, 9, 10, 11],
                    [12, 13, 14, 15, 16],
                    [17, 18, 19],
                    [20, 21, 22],
                    [23, 24],
                ],
            ];
        }

        return null;
    }
}
