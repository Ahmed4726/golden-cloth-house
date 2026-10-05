<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
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
        'category',
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
            'active' => 'boolean',
            'featured' => 'boolean',
            'price' => 'decimal:2',
            'compare_at_price' => 'decimal:2',
        ];
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
        return array_values(array_unique(array_merge(
            $this->media_paths ?? [],
            $this->image_urls ?? []
        )));
    }

    public function isInStock(int $quantity = 1): bool
    {
        return $this->active && $this->stock_quantity >= $quantity;
    }
}
