<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'price',
        'currency',
        'stock_quantity',
        'compare_at_price',
        'discount_percent',
        'category',
        'category_id',
        'brand_id',
        'gender',
        'wear_type',
        'piece_count',
        'included_pieces',
        'piece_lengths',
        'fabric',
        'season',
        'design_code',
        'catalog_import_id',
        'description',
        'image_urls',
        'media_paths',
        'active',
        'featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'image_urls' => 'array',
            'media_paths' => 'array',
            'included_pieces' => 'array',
            'piece_lengths' => 'array',
            'active' => 'boolean',
            'featured' => 'boolean',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
            'discount_percent' => 'decimal:2',
        ];
    }

    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function catalogImport(): BelongsTo
    {
        return $this->belongsTo(CatalogImport::class);
    }

    public function socialPosts(): HasMany
    {
        return $this->hasMany(SocialPost::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function images(): array
    {
        return array_values(
            array_unique(
                array_merge(
                    $this->media_paths ?? [],
                    $this->image_urls ?? []
                )
            )
        );
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->active && $this->stock_quantity >= $quantity;
    }

    public function imageUrl(string $image): string
    {
        if (filter_var($image, FILTER_VALIDATE_URL)) {
            return $image;
        }

        return '/storage/'.ltrim($image, '/');
    }
}
