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
}
